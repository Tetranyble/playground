<?php

namespace Tests\Unit\Trilio;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_required_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('projects', [
            'user_id',
            'name',
            'description',
            'status',
            'deleted_at',
            'slug',
        ]));
    }

    /** @test */
    public function it_has_project_owner()
    {
        $owner = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $owner->id,
        ]);

        $this->assertEquals($owner->id, $project->user_id);
        $this->assertInstanceOf(User::class, $project->user);
    }
}
