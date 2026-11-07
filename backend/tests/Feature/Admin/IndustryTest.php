<?php

namespace Tests\Feature\Admin;

use App\Models\Industry;

class IndustryTest extends AdminTestCase
{
    public function test_crud(): void
    {
        $this->get('/admin/industries/create')->assertOk();

        $this->post('/admin/industries', ['name' => 'Công nghệ thông tin', 'status' => 'active'])->assertRedirect('/admin/industries');
        $industry = Industry::where('name', 'Công nghệ thông tin')->firstOrFail();
        $this->assertSame('cong-nghe-thong-tin', $industry->slug);

        $this->get('/admin/industries')->assertOk()->assertSee('Công nghệ thông tin');
        $this->get("/admin/industries/{$industry->id}/edit")->assertOk();

        $this->put("/admin/industries/{$industry->id}", ['name' => 'Tin học', 'status' => 'inactive'])->assertRedirect();
        $industry->refresh();
        $this->assertSame('tin-hoc', $industry->slug);
        $this->assertSame('inactive', $industry->status);

        $this->delete("/admin/industries/{$industry->id}")->assertRedirect('/admin/industries');
        $this->assertDatabaseMissing('industries', ['id' => $industry->id]);
    }

    public function test_name_must_be_unique_except_for_itself(): void
    {
        $industry = Industry::create(['name' => 'Marketing']);

        $this->post('/admin/industries', ['name' => 'Marketing', 'status' => 'active'])->assertSessionHasErrors('name');
        $this->put("/admin/industries/{$industry->id}", ['name' => 'Marketing', 'status' => 'active'])->assertSessionHasNoErrors();
        $this->assertSame('marketing', $industry->fresh()->slug);
    }

    public function test_parent_cannot_be_itself_or_a_descendant(): void
    {
        $root = Industry::create(['name' => 'Gốc']);
        $child = Industry::create(['name' => 'Con', 'parent_id' => $root->id]);
        $grandchild = Industry::create(['name' => 'Cháu', 'parent_id' => $child->id]);

        foreach ([$root->id, $grandchild->id] as $parentId) {
            $this->put("/admin/industries/{$root->id}", ['name' => 'Gốc', 'status' => 'active', 'parent_id' => $parentId])
                ->assertSessionHasErrors('parent_id');
        }

        $this->get("/admin/industries/{$root->id}/edit")->assertOk()->assertDontSee('Cháu');
    }

    public function test_destroy_is_blocked_with_jobs_or_children(): void
    {
        $withJob = $this->job()->industry;
        $parent = Industry::create(['name' => 'Cha']);
        Industry::create(['name' => 'Con', 'parent_id' => $parent->id]);

        $this->delete("/admin/industries/{$withJob->id}")->assertSessionHas('error');
        $this->delete("/admin/industries/{$parent->id}")->assertSessionHas('error');

        $this->assertDatabaseHas('industries', ['id' => $withJob->id]);
        $this->assertDatabaseHas('industries', ['id' => $parent->id]);
    }
}
