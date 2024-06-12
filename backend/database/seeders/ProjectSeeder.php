<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::factory(10)
            ->recycle(User::first())
            ->create();

        $projects->map(function($project){
            Activity::factory(10)->create([
                'project_id' => $project->id,
            ])->map(fn($act)=> Task::factory(10)->create([
                'activity_id' => $act->id
            ]));
        });


    }
}
