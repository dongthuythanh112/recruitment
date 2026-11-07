<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IndustryController extends Controller
{
    public function index(Request $request): View
    {
        $industries = Industry::with('parent')
            ->withCount(['jobs', 'children'])
            ->when($request->query('q'), fn ($query, $keyword) => $query->where('name', 'like', "%{$keyword}%"))
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.industries.index', compact('industries'));
    }

    public function create(): View
    {
        return view('admin.industries.create', [
            'industry' => new Industry,
            'parents' => Industry::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Industry::create($this->validated($request));

        return redirect()->route('admin.industries.index')->with('success', 'Đã thêm ngành nghề.');
    }

    public function edit(Industry $industry): View
    {
        return view('admin.industries.edit', [
            'industry' => $industry,
            'parents' => Industry::whereNotIn('id', $industry->selfAndDescendantIds())->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, Industry $industry): RedirectResponse
    {
        $data = $this->validated($request, $industry);

        if ($data['name'] !== $industry->name) {
            $data['slug'] = Industry::uniqueSlug($data['name'], $industry->id);
        }

        $industry->update($data);

        return redirect()->route('admin.industries.index')->with('success', 'Đã cập nhật ngành nghề.');
    }

    public function destroy(Industry $industry): RedirectResponse
    {
        if ($industry->jobs()->exists()) {
            return back()->with('error', 'Ngành nghề đang có tin tuyển dụng nên không thể xóa. Hãy chuyển sang "Ngừng dùng".');
        }

        if ($industry->children()->exists()) {
            return back()->with('error', 'Ngành nghề đang có ngành con nên không thể xóa.');
        }

        $industry->delete();

        return redirect()->route('admin.industries.index')->with('success', 'Đã xóa ngành nghề.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Industry $industry = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('industries', 'name')->ignore($industry)],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:industries,id', Rule::notIn($industry?->selfAndDescendantIds() ?? [])],
            'status' => ['required', Rule::in(array_keys(Industry::STATUS_LABELS))],
        ]);
    }
}
