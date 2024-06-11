<?php

namespace App\Http\Controllers\Trilio\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/trilio/projects",
     *     tags={"Projects", "Trilio"},
     *     security={ * {"sanctum": {} } * },
     *     summary="The resource collection",
     *     description="The resource collection",
     *     operationId="Trilio/Api/ProjectController::index",
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
     *             @OA\Items(ref="#/components/schemas/ProjectResource")
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
     * @return ProjectCollection
     */
    public function index(GeneralRequest $request)
    {
        $projects = (new Project())
            ->search($request->search ?? '')
            ->paginate($request->quantity);

        return new ProjectCollection($projects);
    }

    /**
     * @OA\Post(
     * path="/trilio/projects",
     * operationId="Trilio/Api/ProjectController::store",
     * tags={"Projects", "Trilio"},
     * summary="Create new projects",
     * description="Create new project",
     *
     *    @OA\RequestBody(
     *         description="Create new project",
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             example={
     *                 "name": "The brown fox",
     *                 "description": "The quick brown fox project test creation",
     *             },
     *
     *             @OA\Schema(ref="#/components/schemas/ProjectRequest")
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
     *                     "uuid" : "ashjvdHJSVDXSHGVDGHJVGHD-AJBHGD",
     *                     "id" : "2",
     *                     "status" : "null",
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
    public function store(ProjectRequest $request)
    {
        $project = Project::create([
            ...$request->only(['name', 'description']),
            'user_id' => $request->user('api')->id,
        ]);

        return $this->created(
            new ProjectResource($project),
            'success'
        );
    }

    /**
     * @OA\Get (
     *     path="/trilio/projects/{projectUuid}",
     *     summary="The project resource",
     *     description="The project resource",
     *     operationId="Trilio/Api/ProjectController::show",
     *     security={ * {"sanctum": {} } * },
     *     description="The project resource.",
     *     tags={"Projects", "Trilio"},
     *
     *     @OA\Parameter(
     *         name="projectUuid",
     *         in="path",
     *         description="The project uuid",
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
     *             @OA\Schema(ref="#/components/schemas/ProjectResource"),
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
    public function show(Project $project)
    {
        return $this->success(
            new ProjectResource($project),
            'success'
        );
    }

    /**
     * @OA\Patch (
     * path="/trilio/projects/{projectUuid}",
     * operationId="Trilio/Api/ProjectController::update",
     * tags={"Projects", "Trilio"},
     * summary="Update project record",
     * security={ * {"sanctum": {} } * },
     * description="Update project record.",
     *
     *     @OA\Parameter(
     *          name="projectUuid",
     *          in="path",
     *          description="The project resource uuid",
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
     *                 "description": "The quick brown fox project test creation",
     *             },
     *
     *             @OA\Schema(ref="#/components/schemas/ProjectRequest")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="The updated resource.",
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
    public function update(ProjectRequest $request, Project $project)
    {

        $project->fill(
            array_filter($request->all())
        )->save();

        return $this->success(
            new ProjectResource($project->refresh()),
            'success'
        );
    }

    /**
     * @OA\Delete (
     *     path="/trilio/projects/{projectUuid}",
     *     summary="The delete project resource",
     *     description="The delete project resource",
     *     operationId="Trilio/Api/ProjectController::destroy",
     *     security={ * {"sanctum": {} } * },
     *     description="The project resource.",
     *     tags={"Projects", "Trilio"},
     *
     *     @OA\Parameter(
     *         name="projectUuid",
     *         in="path",
     *         description="The project uuid",
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
     *         @OA\JsonContent(
     *
     *             @OA\Schema(ref="#/components/schemas/ProjectResource"),
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
    public function destroy(Project $project)
    {
        $project->delete();

        return $this->delete([], 'success');
    }
}
