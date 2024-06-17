<?php

namespace App\Http\Controllers\Trilio\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Activity;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/trilio/activities/{activityUid}/tasks",
     *     tags={"Tasks", "Trilio"},
     *     security={ * {"sanctum": {} } * },
     *     summary="The resource collection",
     *     description="The resource collection",
     *     operationId="Trilio/Api/TaskController::index",
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search the resource by name or description",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *          name="activityUid",
     *          in="query",
     *          description="Scope the tasks to task uuid. pass false if not required",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *
     *     @OA\Parameter(
     *         name="quantity",
     *         in="query",
     *         description="The quantity",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="The resource collection",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/TaskResource")
     *         )
     *     ),
     *
     *    @OA\Response(response=400, ref="#/components/responses/400"),
     *    @OA\Response(response=403, ref="#/components/responses/403"),
     *    @OA\Response(response=404, ref="#/components/responses/404"),
     *    @OA\Response(response=422, ref="#/components/responses/422"),
     *    @OA\Response(response="default", ref="#/components/responses/500")
     * )
     * Display a listing of the resource.
     *
     * @return TaskCollection
     */
    public function index(GeneralRequest $request, Activity $activity)
    {

        $tasks = (new Task())
            ->taskFor($activity)
            ->search($request->search ?? '')
            ->paginate($request->quantity);

        return new TaskCollection($tasks);
    }

    /**
     * @OA\Post(
     * path="/trilio/activities/{activityUid}/tasks",
     * operationId="Trilio/Api/TaskController::store",
     * tags={"Tasks", "Trilio"},
     * summary="Create new task",
     * description="Create new task",
     *
     *     @OA\Parameter(
     *          name="activityUid",
     *          in="path",
     *          description="The task uuid",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *
     *    @OA\RequestBody(
     *         description="Create new task",
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             example={
     *                 "name": "The brown fox",
     *                 "name": "The brown fox",
     *                 "due_date": "2025-06-11 10:25:46",
     *                 "status": "PENDING|INPROGRESS|COMPLETD",
     *                 "priority": "LOW|MEDIUM|HIGH",
     *                 "description": "The quick brown fox task test creation",
     *             },
     *
     *             @OA\Schema(ref="#/components/schemas/TaskRequest")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="success",
     *
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "message":"success.",
     *                 "status": true,
     *                 "data": {
     *                     "uuid": "hcusyadcdsuvcjghsdvcdsyus78s7sgshvjcds",
     *                     "name": "The brown fox",
     *                     "due_date": "2025-06-11 10:25:46",
     *                     "status": "PENDING|INPROGRESS|COMPLETD",
     *                     "priority": "LOW|MEDIUM|HIGH",
     *                     "description": "The quick brown fox task test creation",
     *                     "createdAt" : "2022-09-08T12:29:54.000000Z",
     *                     "updatedAt" : "2022-09-08T12:29:54.000000Z"
     *                 }
     *             }
     *         )
     *     ),
     *
     *    @OA\Response(response=400, ref="#/components/responses/400"),
     *    @OA\Response(response=404, ref="#/components/responses/404"),
     *    @OA\Response(response=422, ref="#/components/responses/422"),
     *    @OA\Response(response="default", ref="#/components/responses/500")
     * )
     *
     *  Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store(TaskRequest $request, Activity $activity)
    {
        $task = Task::create([
            ...$request->only(['name', 'description', 'priority', 'due_date', 'status']),
            'activity_id' => $activity->id,
        ]);

        return $this->created(
            new TaskResource($task->refresh()),
            'success'
        );
    }

    /**
     * @OA\Get (
     *     path="/trilio/activities/tasks/{activityid}",
     *     summary="The task resource",
     *     description="The task resource",
     *     operationId="Trilio/Api/TaskController::show",
     *     security={ * {"sanctum": {} } * },
     *     description="The task resource.",
     *     tags={"Tasks", "Trilio"},
     *
     *     @OA\Parameter(
     *         name="Taskuid",
     *         in="path",
     *         description="The task uuid",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Schema(ref="#/components/schemas/TaskResource"),
     *             type="object",
     *             example={
     *                 "message":"success",
     *                 "status": true,
     *             }
     *         )
     *     ),
     *
     *    @OA\Response(response=400, ref="#/components/responses/400"),
     *    @OA\Response(response=404, ref="#/components/responses/404"),
     *    @OA\Response(response=422, ref="#/components/responses/422"),
     *    @OA\Response(response="default", ref="#/components/responses/500")
     * )
     * Store an applicant newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function show(Task $task)
    {
        return $this->success(
            new TaskResource($task),
            'success'
        );
    }

    /**
     * @OA\Patch (
     * path="/trilio/activities/tasks/{activityuid}",
     * operationId="Trilio/Api/TaskController::update",
     * tags={"Tasks", "Trilio"},
     * summary="Update task record",
     * security={ * {"sanctum": {} } * },
     * description="Update task record.",
     *
     *     @OA\Parameter(
     *          name="activityuid",
     *          in="path",
     *          description="The task resource uuid",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *
     *    @OA\RequestBody(
     *         description="Update task resource.",
     *         required=false,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             example={
     *                 "name": "The brown fox",
     *                 "due_date": "2025-06-11 10:25:46",
     *                 "status": "PENDING|INPROGRESS|COMPLETD",
     *                 "priority": "LOW|MEDIUM|HIGH",
     *                 "description": "The quick brown fox task test creation",
     *             },
     *
     *             @OA\Schema(ref="#/components/schemas/TaskRequest")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="The updated resource.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "message":"success",
     *                 "status": true,
     *                 "data": {
     *                     "name": "The brown fox",
     *                     "description": "The quick brown fox task test creation",
     *                 }
     *             }
     *         )
     *     ),
     *
     *    @OA\Response(response=400, ref="#/components/responses/400"),
     *    @OA\Response(response=404, ref="#/components/responses/404"),
     *    @OA\Response(response=422, ref="#/components/responses/422"),
     *    @OA\Response(response="default", ref="#/components/responses/500")
     * )
     *
     * * Update the specified resource in storage and database.
     *
     * @return mixed
     */
    public function update(Request $request, Task $task)
    {
        $task->fill(
            array_filter($request->except('__token'))
        )->save();

        return $this->success(
            new TaskResource($task->refresh()),
            'success'
        );
    }

    /**
     * @OA\Delete (
     *     path="/trilio/activities/tasks/{activityuid}",
     *     summary="The delete task resource",
     *     description="The delete task resource",
     *     operationId="Api/Trilio/Api/TaskController::destroy",
     *     security={ * {"sanctum": {} } * },
     *     description="The task resource.",
     *     tags={"Tasks", "Trilio"},
     *
     *     @OA\Parameter(
     *         name="taskuid",
     *         in="path",
     *         description="The task uuid",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="success",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Schema(ref="#/components/schemas/TaskResource"),
     *             type="object",
     *             example={
     *                 "message":"success",
     *                 "status": true,
     *             }
     *         )
     *     ),
     *
     *    @OA\Response(response=400, ref="#/components/responses/400"),
     *    @OA\Response(response=404, ref="#/components/responses/404"),
     *    @OA\Response(response=422, ref="#/components/responses/422"),
     *    @OA\Response(response="default", ref="#/components/responses/500")
     * )
     * Store an applicant newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return $this->delete([], 'success');
    }
}
