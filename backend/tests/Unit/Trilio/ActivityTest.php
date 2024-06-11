<?php

namespace Tests\Unit\Trilio;

use App\Enums\TrilioStatus;
use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_required_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('activities', [
            'project_id',
            'name',
            'description',
            'status',
            'deleted_at',
            'slug',
            'uuid',
            'start_date',
            'end_date'
        ]));
    }

    /** @test */
    public function it_has_project_owner()
    {
        $project = Project::factory()->create();
        $activity = Activity::factory()->create([
            'project_id' => $project->id,
        ]);

        $this->assertEquals($activity->project_id, $project->id);
        $this->assertInstanceOf(Project::class, $activity->project);
    }
    /** @test */
    public function activity_status_instance_of_trilio_status()
    {

        $activity = Activity::factory()->create();


        $this->assertInstanceOf(TrilioStatus::class, $activity->status);
    }

    /** @test */
    public function activity_may_be_scoped_to_project_instance()
    {
        $projects = Project::factory(20)->create();

        $activities = $projects->map(fn($project) =>
        Activity::factory(10)->create(['project_id' => $project->id])
        );

        $this->assertCount(
            10,
            (new Activity())->activityFor($projects->first())->get()
        );

    }

    /** @test */
    public function activity_may_be_scoped_to_project_instances()
    {
        $projects = Project::factory(20)->create();
        $projects->map(fn($project) =>
        Activity::factory(10)->create(['project_id' => $project->id])
        );

        $this->assertCount(
            20,
            (new Activity())->activityFor($projects->first(), $projects->last())->get()
        );

    }

    /** @test */
    public function activity_may_be_scoped_to_project_instance_uuids()
    {
        $projects = Project::factory(20)->create();

        $projects->map(fn($project) =>
        Activity::factory(10)->create(['project_id' => $project->id])
        );

        $this->assertCount(
            20,
            (new Activity())->activityFor($projects->first()->uuid, $projects->last()->uuid)->get()
        );

    }

    /** @test */
    public function activity_may_be_scoped_to_project_ids()
    {
        $projects = Project::factory(20)->create();

        $projects->map(fn($project) =>
        Activity::factory(10)->create(['project_id' => $project->id])
        );
        $this->assertCount(
            20,
            (new Activity())->activityFor($projects->first()->id, $projects->last()->id)->get()
        );

    }

    /** @test */
    public function given_falsy_values_as_argument_to_scope_activities_ignore()
    {
        $projects = Project::factory(20)->create();

        $projects->map(fn($project) =>
        Activity::factory(10)->create(['project_id' => $project->id])
        );

        $this->assertCount(
            200,
            (new Activity())->activityFor(false)->get()
        );
    }
}
