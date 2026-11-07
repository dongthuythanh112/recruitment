<?php

namespace Tests\Feature\Admin;

use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Industry;
use App\Models\Job;
use App\Models\RecruiterProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class AdminTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
    }

    protected function candidate(): CandidateProfile
    {
        return User::factory()->create()->candidateProfile()->create();
    }

    protected function recruiter(): RecruiterProfile
    {
        return User::factory()->recruiter()->create()->recruiterProfile()->create();
    }

    protected function job(array $attributes = []): Job
    {
        return Job::create([
            'company_id' => Company::create(['name' => 'Công ty '.fake()->unique()->word()])->id,
            'created_by' => User::factory()->recruiter()->create()->id,
            'industry_id' => Industry::create(['name' => 'Ngành '.fake()->unique()->word()])->id,
            'title' => 'Lập trình viên PHP',
            'description' => 'Mô tả',
            'status' => Job::STATUS_PENDING,
            ...$attributes,
        ]);
    }

    protected function application(Job $job, CandidateProfile $candidate): void
    {
        $resume = $candidate->resumes()->create(['title' => 'CV', 'file_path' => 'cv.pdf']);
        $candidate->applications()->create(['job_id' => $job->id, 'resume_id' => $resume->id]);
    }
}
