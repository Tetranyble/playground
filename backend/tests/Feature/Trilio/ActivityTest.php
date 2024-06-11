<?php

namespace Tests\Feature\Trilio;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Supports\PermissionSupport;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use PermissionSupport, RefreshDatabase , WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->role = Role::factory()->create([
            'label' => 'Manager',
            'name' => 'manager',
        ]);
        $this->user->assignRoles('manager');
        $this->permissions('Activity');
        $this->project = Project::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function a_activities_owner_may_view_projects(): void
    {
        Activity::factory(20)->create([
            'project_id' => $this->project->id,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->getJson(route('v1.trilio.activities.index',[
                'project' => $this->project->uuid
            ]))
            ->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json->has('status')
            ->has('message')
            ->has('meta')
            ->has('links')
            ->has('data', 10)

            ->etc()
        );

    }

    /** @test */
    public function given_an_activity_uuid_aan_activity_may_be_viewed(): void
    {
        $activity = Activity::factory()->create([
            'project_id' => $this->project->id,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->getJson(route('v1.trilio.activities.show', [
                'activity' => $activity->uuid,
            ]))
            ->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('status', true)
            ->where('message', 'success')
            ->where('data.uuid', $activity->uuid)
            ->where('data.id', $activity->id)
            ->where('data.name', $activity->name)
            ->etc()
        );

    }

    /** @test */
    public function a_user_may_create_new_activity(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('v1.trilio.activities.store',[
                'project' => $this->project->uuid
            ]),
                [
                    'name' => 'new project',
                    'description' => 'new project description',
                    'start_date' => now()->format('Y-m-d H:i:s'),
                    'end_date' => now()->addYear()->format('Y-m-d H:i:s'),
                ]
            )->assertStatus(201);

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('status', true)
            ->where('message', 'success')
            ->where('data.name', 'new project')
            ->where('data.projectId', $this->project->id)

            ->etc()
        );

    }

    /** @test */
    public function given_an_activity_uuid_may_be_patch(): void
    {
        $activity = Activity::factory()->create([
            'project_id' => $this->project->id,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->patchJson(route('v1.trilio.activities.update', ['activity' => $activity->uuid]),
                [
                    'name' => 'Update name',
                    'description' => 'syndication',
                ]
            )->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('status', true)
            ->where('message', 'success')
            ->where('data.name', 'Update name')
            ->etc()
        );

    }

    /** @test */
    public function given_an_activity_uuid_it_may_be_deleted(): void
    {
        $activity = Activity::factory()->create([
            'project_id' => $this->project->id,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(route('v1.trilio.activities.destroy', [
                'activity' => $activity->uuid,
            ]))
            ->assertStatus(204);
        $this->assertSoftDeleted('activities',[
            'id' => $activity->id
        ]);

    }
}
