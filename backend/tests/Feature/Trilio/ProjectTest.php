<?php

namespace Tests\Feature\Trilio;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Supports\PermissionSupport;
use Tests\TestCase;

class ProjectTest extends TestCase
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
        $this->permissions('Project');
    }

    /** @test */
    public function a_project_owner_may_view_projects(): void
    {
        Project::factory(20)->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->getJson(route('v1.trilio.projects.index'))
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
    public function given_a_project_uuid_a_project_owner_may_view_project(): void
    {
        $project = Project::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->getJson(route('v1.trilio.projects.show', [
                'project' => $project->uuid,
            ]))
            ->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('status', true)
            ->where('message', 'success')
            ->where('data.uuid', $project->uuid)
            ->where('data.id', $project->id)
            ->where('data.ownerId', $this->user->id)
            ->etc()
        );

    }

    /** @test */
    public function a_user_may_create_new_project(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('v1.trilio.projects.store'),
                [
                    'name' => 'new project',
                    'description' => 'new project description',
                ]
            )->assertStatus(201);

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('status', true)
            ->where('message', 'success')
            ->where('data.name', 'new project')
            ->etc()
        );

    }

    /** @test */
    public function given_a_project_uuid_project_owner_may_update_owns_project(): void
    {
        $project = Project::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->patchJson(route('v1.trilio.projects.update', ['project' => $project->uuid]),
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
    public function given_a_project_uuid_a_project_owner_may_delete_project(): void
    {
        $project = Project::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(route('v1.trilio.projects.destroy', [
                'project' => $project->uuid,
            ]))
            ->assertStatus(204);

    }
}
