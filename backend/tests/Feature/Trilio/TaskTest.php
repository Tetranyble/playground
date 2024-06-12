<?php

namespace Tests\Feature\Trilio;

use App\Models\Activity;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Supports\PermissionSupport;
use Tests\TestCase;

class TaskTest extends TestCase
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
        $this->permissions('Task');
        $this->activity = Activity::factory()->create();
    }

    /** @test */
    public function a_tasks_owner_may_view_tasks(): void
    {
        Task::factory(20)->create([
            'activity_id' => $this->activity->id,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->getJson(route('v1.trilio.tasks.index', [
                'activity' => $this->activity->uuid,
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
    public function given_a_task_uuid_a_task_may_be_viewed(): void
    {
        $task = Task::factory()->create([
            'activity_id' => $this->activity->id,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->getJson(route('v1.trilio.tasks.show', [
                'task' => $task->uuid,
            ]))
            ->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('status', true)
            ->where('message', 'success')
            ->where('data.uuid', $task->uuid)
            ->where('data.id', $task->id)
            ->where('data.name', $task->name)
            ->etc()
        );

    }

    /** @test */
    public function a_user_may_create_a_new_task(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('v1.trilio.tasks.store', [
                'activity' => $this->activity->uuid,
            ]),
                [
                    'name' => 'new activity',
                    'description' => 'new activity description',
                    'due_date' => now()->format('Y-m-d H:i:s'),
                    'priority' => 'LOW'
                ]
            )->assertStatus(201);

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('status', true)
            ->where('message', 'success')
            ->where('data.name', 'new activity')
            ->where('data.activityId', $this->activity->id)

            ->etc()
        );

    }

    /** @test */
    public function given_a_task_uuid_may_be_patch(): void
    {
        $task = Task::factory()->create([
            'activity_id' => $this->activity->id,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->patchJson(route('v1.trilio.tasks.update', ['task' => $task->uuid]),
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
    public function given_a_task_uuid_it_may_be_deleted(): void
    {
        $task = Task::factory()->create([
            'activity_id' => $this->activity->id,
        ]);
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson(route('v1.trilio.tasks.destroy', [
                'task' => $task->uuid,
            ]))
            ->assertStatus(204);
        $this->assertSoftDeleted('tasks', [
            'id' => $task->id,
        ]);

    }
}
