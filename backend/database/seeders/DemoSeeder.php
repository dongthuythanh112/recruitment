<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Industry;
use App\Models\Job;
use App\Models\JobModerationHistory;
use App\Models\RecruiterProfile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu mẫu để thử các màn hình quản trị trên máy dev. Không chạy trên production.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->firstOrFail();

        $it = Industry::firstOrCreate(['name' => 'Công nghệ thông tin']);
        $software = Industry::firstOrCreate(['name' => 'Phát triển phần mềm'], ['parent_id' => $it->id]);
        $marketing = Industry::firstOrCreate(['name' => 'Marketing']);
        $accounting = Industry::firstOrCreate(['name' => 'Kế toán - Kiểm toán']);

        $skills = collect(['PHP', 'Laravel', 'React', 'MySQL', 'Giao tiếp', 'SEO'])
            ->mapWithKeys(fn (string $name) => [$name => Skill::firstOrCreate(['name' => $name])->id]);

        $company = Company::firstOrCreate(
            ['name' => 'Công ty TNHH Công nghệ Sao Việt'],
            ['address' => 'Duy Tân, Cầu Giấy, Hà Nội', 'company_size' => '50-100', 'email' => 'tuyendung@saoviet.test', 'created_by' => $admin->id],
        );

        $recruiter = $this->recruiter('Trần Minh Anh', 'tuyendung@saoviet.test', $company, 'Trưởng phòng nhân sự');
        $this->recruiter('Lê Hoàng Nam', 'nam.le@saoviet.test', $company, 'Chuyên viên tuyển dụng');

        $candidate = $this->candidate('Nguyễn Thu Hà', 'thuha@example.test', 'Lập trình viên PHP', 2.5);
        $this->candidate('Phạm Đức Long', 'duclong@example.test', 'Nhân viên Marketing', 1);
        $this->candidate('Vũ Khánh Linh', 'khanhlinh@example.test', 'Kế toán tổng hợp', 4);

        $resume = $candidate->resumes()->firstOrCreate(
            ['title' => 'CV Lập trình viên PHP'],
            ['file_path' => 'resumes/cv-thu-ha.pdf', 'original_filename' => 'cv-thu-ha.pdf', 'is_default' => true],
        );
        $candidate->educations()->firstOrCreate(
            ['school_name' => 'Đại học Công nghiệp Hà Nội'],
            ['degree' => 'Cử nhân', 'major' => 'Công nghệ thông tin', 'start_date' => '2018-09-01', 'end_date' => '2022-06-30'],
        );
        $candidate->experiences()->firstOrCreate(
            ['company_name' => 'Công ty Phần mềm An Phát', 'position' => 'Lập trình viên'],
            ['start_date' => '2022-08-01', 'is_current' => true],
        );
        $candidate->skills()->syncWithoutDetaching([
            $skills['PHP'] => ['level' => 'Khá', 'created_at' => now()],
            $skills['Laravel'] => ['level' => 'Khá', 'created_at' => now()],
        ]);

        $approved = $this->job($recruiter, $company, $software, 'Lập trình viên Laravel', [$skills['PHP'], $skills['Laravel'], $skills['MySQL']]);
        if ($approved->status === Job::STATUS_PENDING) {
            $approved->moderate(JobModerationHistory::ACTION_APPROVE, $admin);
        }

        $this->job($recruiter, $company, $software, 'Lập trình viên React', [$skills['React']]);
        $this->job($recruiter, $company, $marketing, 'Chuyên viên SEO', [$skills['SEO']]);
        $this->job($recruiter, $company, $accounting, 'Kế toán nội bộ', [$skills['Giao tiếp']]);

        Application::firstOrCreate(
            ['job_id' => $approved->id, 'candidate_id' => $candidate->id],
            ['resume_id' => $resume->id, 'status' => 'reviewing'],
        );

        $this->publishedJobs($admin, $marketing, $accounting, $skills->all());
    }

    /**
     * Các tin đã duyệt của nhiều công ty để trang chủ có nội dung.
     *
     * @param  array<string, int>  $skills
     */
    private function publishedJobs(User $admin, Industry $marketing, Industry $accounting, array $skills): void
    {
        $sales = Industry::firstOrCreate(['name' => 'Bán hàng']);
        $logistics = Industry::firstOrCreate(['name' => 'Vận tải - Logistics']);
        $design = Industry::firstOrCreate(['name' => 'Thiết kế']);

        $listings = [
            ['Công ty CP Truyền thông Mây Xanh', 'tuyendung@mayxanh.test', [
                ['Trưởng nhóm Digital Marketing', $marketing, 'TP. Hồ Chí Minh', 20000000, 35000000, 'hybrid', [$skills['SEO']]],
                ['Nhân viên Content Marketing', $marketing, 'TP. Hồ Chí Minh', 10000000, 15000000, 'onsite', [$skills['SEO'], $skills['Giao tiếp']]],
            ]],
            ['Công ty TNHH Kế toán Thành Tín', 'tuyendung@thanhtin.test', [
                ['Kế toán tổng hợp', $accounting, 'Hà Nội', 12000000, 18000000, 'onsite', [$skills['Giao tiếp']]],
                ['Kiểm toán viên', $accounting, 'Đà Nẵng', 18000000, null, 'onsite', []],
            ]],
            ['Công ty CP Bán lẻ Phố Mới', 'tuyendung@phomoi.test', [
                ['Nhân viên kinh doanh', $sales, 'Hà Nội', 9000000, 20000000, 'onsite', [$skills['Giao tiếp']]],
                ['Quản lý cửa hàng', $sales, 'Hải Phòng', null, null, 'onsite', [$skills['Giao tiếp']]],
            ]],
            ['Công ty TNHH Giao vận Đường Dài', 'tuyendung@duongdai.test', [
                ['Điều phối vận tải', $logistics, 'Bình Dương', 11000000, 16000000, 'onsite', []],
            ]],
            ['Công ty CP Thiết kế Nắng Mới', 'tuyendung@nangmoi.test', [
                ['Thiết kế đồ họa', $design, 'Đà Nẵng', 12000000, 20000000, 'remote', []],
            ]],
        ];

        foreach ($listings as [$companyName, $email, $jobs]) {
            $company = Company::firstOrCreate(['name' => $companyName], ['email' => $email, 'created_by' => $admin->id]);
            $recruiter = $this->recruiter('Phòng Nhân sự', $email, $company, 'Chuyên viên tuyển dụng');

            foreach ($jobs as [$title, $industry, $location, $salaryMin, $salaryMax, $workplace, $skillIds]) {
                $job = $this->job($recruiter, $company, $industry, $title, $skillIds, [
                    'location' => $location,
                    'salary_min' => $salaryMin,
                    'salary_max' => $salaryMax,
                    'workplace_type' => $workplace,
                ]);

                if ($job->status === Job::STATUS_PENDING) {
                    $job->moderate(JobModerationHistory::ACTION_APPROVE, $admin);
                }
            }
        }
    }

    private function recruiter(string $name, string $email, Company $company, string $position): RecruiterProfile
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => 'password', 'role' => User::ROLE_RECRUITER, 'phone' => '0912345678'],
        );

        return RecruiterProfile::firstOrCreate(['user_id' => $user->id], ['company_id' => $company->id, 'position' => $position]);
    }

    private function candidate(string $name, string $email, string $position, float $years): CandidateProfile
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => 'password', 'role' => User::ROLE_CANDIDATE, 'phone' => '0987654321'],
        );

        return CandidateProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['desired_position' => $position, 'years_of_experience' => $years, 'gender' => 'other', 'address' => 'Hà Nội'],
        );
    }

    /**
     * @param  array<int, int>  $skillIds
     * @param  array<string, mixed>  $overrides
     */
    private function job(RecruiterProfile $recruiter, Company $company, Industry $industry, string $title, array $skillIds, array $overrides = []): Job
    {
        $job = Job::firstOrCreate(['title' => $title], [
            'company_id' => $company->id,
            'created_by' => $recruiter->user_id,
            'industry_id' => $industry->id,
            'description' => 'Tham gia đội ngũ '.$company->name.' ở vị trí '.$title.'.',
            'requirements' => 'Có trách nhiệm, chủ động trong công việc.',
            'benefits' => 'Lương tháng 13, bảo hiểm đầy đủ.',
            'employment_type' => 'full_time',
            'workplace_type' => 'hybrid',
            'location' => 'Hà Nội',
            'salary_min' => 15000000,
            'salary_max' => 25000000,
            'quantity' => 2,
            'deadline' => now()->addMonth(),
            'status' => Job::STATUS_PENDING,
            ...$overrides,
        ]);

        $job->skills()->syncWithoutDetaching(
            collect($skillIds)->mapWithKeys(fn (int $id) => [$id => ['is_required' => true, 'created_at' => now()]])->all()
        );

        return $job;
    }
}
