<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResumeController extends Controller
{
    public function index(Request $request): View
    {
        $candidate = $this->candidate($request);
        $resumes = $candidate->resumes()
            ->withCount('applications')
            ->latest()
            ->get();

        return view('client.resumes.index', compact('resumes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'file.required' => 'Vui lòng chọn tệp CV dạng PDF.',
            'file.mimes' => 'CV cần có định dạng PDF.',
            'file.max' => 'Kích thước CV không được vượt quá 10 MB.',
        ]);

        $file = $request->file('file');
        $path = $file->store('resumes/'.$candidate->id, 'local');
        $originalName = Str::limit(basename(str_replace('\\', '/', $file->getClientOriginalName())), 255, '');
        $title = trim($data['title'] ?? '') ?: pathinfo($originalName, PATHINFO_FILENAME);

        try {
            DB::transaction(function () use ($candidate, $path, $originalName, $title): void {
                $makeDefault = ! $candidate->resumes()->exists();

                if ($makeDefault) {
                    $candidate->resumes()->update(['is_default' => false]);
                }

                $candidate->resumes()->create([
                    'title' => $title,
                    'file_path' => $path,
                    'original_filename' => $originalName,
                    'is_default' => $makeDefault,
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        return redirect()->route('candidate.resumes.index')->with('success', 'Đã tải CV lên thành công.');
    }

    public function setDefault(Request $request, Resume $resume): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $this->assertOwnedResume($candidate, $resume);

        DB::transaction(function () use ($candidate, $resume): void {
            $candidate->resumes()->lockForUpdate()->update(['is_default' => false]);
            $resume->update(['is_default' => true]);
        });

        return back()->with('success', 'Đã chọn CV mặc định. CV này sẽ được dùng trước khi ứng tuyển.');
    }

    public function view(Request $request, Resume $resume): StreamedResponse
    {
        $this->assertOwnedResume($this->candidate($request), $resume);
        $disk = $this->resumeDisk($resume);
        $filename = $this->resumeFilename($resume);

        return Storage::disk($disk)->response($resume->file_path, $filename, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);
    }

    public function download(Request $request, Resume $resume): StreamedResponse
    {
        $this->assertOwnedResume($this->candidate($request), $resume);
        $disk = $this->resumeDisk($resume);

        return Storage::disk($disk)->download($resume->file_path, $this->resumeFilename($resume), [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroy(Request $request, Resume $resume): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $this->assertOwnedResume($candidate, $resume);

        if ($resume->applications()->exists()) {
            return back()->with('error', 'CV này đã được dùng để ứng tuyển nên không thể xóa.');
        }

        $disk = $this->findResumeDisk($resume);
        $path = $resume->file_path;
        $wasDefault = $resume->is_default;

        DB::transaction(function () use ($resume, $candidate, $wasDefault): void {
            $resume->delete();

            if ($wasDefault) {
                $candidate->resumes()->oldest()->first()?->update(['is_default' => true]);
            }
        });

        if ($disk) {
            Storage::disk($disk)->delete($path);
        }

        return redirect()->route('candidate.resumes.index')->with('success', 'Đã xóa CV.');
    }

    private function candidate(Request $request): CandidateProfile
    {
        abort_unless(
            $request->user()?->role === User::ROLE_CANDIDATE
                && $request->user()?->status === User::STATUS_ACTIVE,
            403,
            'Chức năng này chỉ dành cho ứng viên đang hoạt động.',
        );

        return $request->user()->candidateProfile()->firstOrCreate();
    }

    private function assertOwnedResume(CandidateProfile $candidate, Resume $resume): void
    {
        abort_unless((int) $resume->candidate_id === (int) $candidate->id, 404);
    }

    private function resumeDisk(Resume $resume): string
    {
        $disk = $this->findResumeDisk($resume);
        abort_unless($disk, 404, 'Không tìm thấy tệp CV.');

        return $disk;
    }

    private function findResumeDisk(Resume $resume): ?string
    {
        $path = $resume->file_path;

        if (! is_string($path) || $path === '' || preg_match('#^(?:[A-Za-z]:|[/\\\\])#', $path)
            || preg_match('#(^|[/\\\\])\.\.([/\\\\]|$)#', $path)) {
            return null;
        }

        foreach (array_unique([config('filesystems.default'), 'local', 'public']) as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return $disk;
            }
        }

        return null;
    }

    private function resumeFilename(Resume $resume): string
    {
        $filename = basename(str_replace('\\', '/', $resume->original_filename ?: $resume->file_path));

        return $filename !== '' ? $filename : 'CV.pdf';
    }
}
