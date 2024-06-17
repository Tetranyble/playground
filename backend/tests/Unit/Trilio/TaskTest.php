<?php

namespace Tests\Unit\Trilio;

use App\Enums\TrilioStatus;
use App\Models\Activity;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_required_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('tasks', [
            'due_date',
            'name',
            'description',
            'status',
            'priority',
            'activity_id',
            'uuid',
            'deleted_at',
        ]));
    }

    /** @test */
    public function it_has_an_activity()
    {
        $activity = Activity::factory()->create();
        $task = Task::factory()->create([
            'activity_id' => $activity->id,
        ]);

        $this->assertEquals($activity->id, $task->activity_id);
        $this->assertInstanceOf(Activity::class, $task->activity);
    }

    /** @test */
    public function task_status_instance_of_trilio_status()
    {
        $task = Task::factory()->create();

        $this->assertInstanceOf(TrilioStatus::class, $task->status);
    }
}
