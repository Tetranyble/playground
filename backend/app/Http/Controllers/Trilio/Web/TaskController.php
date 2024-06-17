<?php

namespace App\Http\Controllers\Trilio\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralRequest;
use App\Models\Activity;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GeneralRequest $request, Activity $activity)
    {
        $tasks = (new Task())
            ->taskFor($activity)
            ->search($request->search ?? '')
            ->paginate($request->quantity);

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Activity $activity)
    {
        return view('tasks.create', compact('activity'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $taskData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'activity_id' => 'required|numeric',
        ]);
        Task::create($taskData);

        return redirect()->route('activities.tasks.index', ['activity' => $request->activity_id])
            ->with('success', 'Task create successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $taskName = $task->name;
        $task->delete();

        return redirect()->back()->with('success', "Task: {$taskName} delete successfully.");
    }
}
