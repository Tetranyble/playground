<?php

namespace App\Http\Controllers\Trilio\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityRequest;
use App\Http\Requests\GeneralRequest;
use App\Http\Resources\ActivityCollection;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/trilio/projects/{projectUuid}/activities",
     *     tags={"Activities", "Trilio"},
     *     security={ * {"sanctum": {} } * },
     *     summary="The resource collection",
     *     description="The resource collection",
     *     operationId="Trilio/Api/ActivityController::index",
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
     *          name="projectUuid",
     *          in="query",
     *          description="Scope the activities to project uuid. pass false if not required",
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
     *             @OA\Items(ref="#/components/schemas/ActivityResource")
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
     * @return ActivityCollection
     */
    public function index(GeneralRequest $request, Project $project)
    {
        $activities = (new Activity())
            ->activityFor($project)
            ->search($request->search ?? '')
            ->paginate($request->quantity);

        return new ActivityCollection($activities);
    }

    /**
     * @OA\Post(
     * path="/trilio/projects/{projectUuid}/activities",
     * operationId="Trilio/Api/ActivityController::store",
     * tags={"Activities", "Trilio"},
     * summary="Create new activity",
     * description="Create new activity",
     *
     *     @OA\Parameter(
     *          name="projectUuid",
     *          in="path",
     *          description="The project uuid",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *
     *    @OA\RequestBody(
     *         description="Create new activity",
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             example={
     *                 "name": "The brown fox",
     *                 "start_date": "2025-06-11 10:25:46",
     *                 "end_date": "2025-06-11 10:25:46",
     *                 "description": "The quick brown fox project test creation",
     *             },
     *
     *             @OA\Schema(ref="#/components/schemas/ActivityRequest")
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
     *                     "name" : "The brown fox",
     *                     "uuid" : "ashjvdHJSVDXSjdhHGVDGHJVGHD-AJBHGD",
     *                     "id" : "2",
     *                     "status" : "PENDING",
     *                     "start_date" : "2022-09-08T12:29:54.000000Z",
     *                     "end_date" : "2022-09-08T12:29:54.000000Z",
     *                     "description" : "The quick brown fox project test creation",
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
    public function store(ActivityRequest $request, Project $project)
    {
        $activity = Activity::create([
            ...$request->only(['name', 'description', 'project_id', 'start_date', 'end_date', 'status']),
            'project_id' => $project->id,
        ]);

        return $this->created(
            new ActivityResource($activity->refresh()),
            'success'
        );
    }

    /**
     * @OA\Get (
     *     path="/trilio/projects/activities/{activityid}",
     *     summary="The activity resource",
     *     description="The activity resource",
     *     operationId="Trilio/Api/ActivityController::show",
     *     security={ * {"sanctum": {} } * },
     *     description="The activity resource.",
     *     tags={"Activities", "Trilio"},
     *
     *     @OA\Parameter(
     *         name="Activityuid",
     *         in="path",
     *         description="The activity uuid",
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
     *             @OA\Schema(ref="#/components/schemas/ActivityResource"),
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
    public function show(Activity $activity)
    {
        return $this->success(
            new ActivityResource($activity),
            'success'
        );
    }

    /**
     * @OA\Patch (
     * path="/trilio/projects/activities/{activityuid}",
     * operationId="Trilio/Api/ActivityController::update",
     * tags={"Activities", "Trilio"},
     * summary="Update activity record",
     * security={ * {"sanctum": {} } * },
     * description="Update activity record.",
     *
     *     @OA\Parameter(
     *          name="activityuid",
     *          in="path",
     *          description="The activity resource uuid",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *
     *    @OA\RequestBody(
     *         description="Update activity resource.",
     *         required=false,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             example={
     *                 "name": "The brown fox",
     *                 "start_date": "2025-06-11 10:25:46",
     *                 "end_date": "2025-06-11 10:25:46",
     *                 "description": "The quick brown fox project test creation",
     *             },
     *
     *             @OA\Schema(ref="#/components/schemas/ActivityRequest")
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
     *                     "description": "The quick brown fox project test creation",
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
    public function update(Request $request, Activity $activity)
    {
        $activity->fill(
            array_filter($request->except('__token'))
        )->save();

        return $this->success(
            new ActivityResource($activity->refresh()),
            'success'
        );
    }

    /**
     * @OA\Delete (
     *     path="/trilio/projects/activities/{activityuid}",
     *     summary="The delete activity resource",
     *     description="The delete activity resource",
     *     operationId="Api/Trilio/Api/ActivityController::destroy",
     *     security={ * {"sanctum": {} } * },
     *     description="The activity resource.",
     *     tags={"Activities", "Trilio"},
     *
     *     @OA\Parameter(
     *         name="activityuid",
     *         in="path",
     *         description="The activity uuid",
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
     *             @OA\Schema(ref="#/components/schemas/ActivityResource"),
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
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return $this->delete([], 'success');
    }
}
