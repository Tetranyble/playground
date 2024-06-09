<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralRequest;
use App\Http\Resources\MessageCollection;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users/{user}/messages",
     *     tags={"Messages"},
     *     security={ * {"sanctum": {} } * },
     *     summary="The resource collection",
     *     description="The resource collection",
     *     operationId="Api/MessageController::index",
     *
     *     @OA\Parameter(
     *          name="user",
     *          in="path",
     *          description="The user id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
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
     *             @OA\Items(ref="#/components/schemas/MessageResource")
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
     * @return MessageCollection
     */
    public function index(GeneralRequest $request, $user)
    {
        $messages = Message::query()
            ->where('user_id', function ($q) use ($user) {
                $q->from('users')
                    ->select('id')
                    ->where('email', $user)
                    ->orWhere('id', $user)
                    ->limit(1);
            })
            ->paginate($request->quantity ?? 10);

        return new MessageCollection($messages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @OA\Get (
     *     path="/users/{user}/messages/{messageId}",
     *     summary="The employee resource",
     *     description="The employee resource",
     *     operationId="Api/MessageController::show",
     *     security={ * {"sanctum": {} } * },
     *     description="The employee resource.",
     *     tags={"Messages"},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="The user Id or email",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *          name="messageId",
     *          in="path",
     *          description="The message Id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer",
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Schema(ref="#/components/schemas/MessageResource"),
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
    public function show($user, $message)
    {
        $messages = Message::query()
            ->where('id', $message)
            ->where('user_id', function ($q) use ($user) {
                $q->from('users')
                    ->select('id')
                    ->where('email', $user)
                    ->orWhere('id', $user)
                    ->limit(1);
            })->first();
        $messages->update(['is_read' => true]);

        return $this->success(
            new MessageResource($messages->refresh()),
            'success'
        );
    }

    /**
     * @OA\Patch (
     *     path="/users/{user}/messages/{messageId}",
     *     summary="The employee resource",
     *     description="The employee resource",
     *     operationId="Api/MessageController::show",
     *     security={ * {"sanctum": {} } * },
     *     description="The employee resource.",
     *     tags={"Messages"},
     *
     *     @OA\RequestBody(
     *          description="Update message",
     *          required=true,
     *
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              example={
     *                  "is_read": true,
     *              },
     *
     *          )
     *      ),
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="The user Id or email",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *          name="messageId",
     *          in="path",
     *          description="The message Id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer",
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Schema(ref="#/components/schemas/MessageResource"),
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
    public function update(Request $request, $user, $message)
    {
        $messages = Message::query()
            ->where('id', $message)
            ->where('user_id', function ($q) use ($user) {
                $q->from('users')
                    ->select('id')
                    ->where('email', $user)
                    ->orWhere('id', $user)
                    ->limit(1);
            })->first();
        $messages->update(['is_read' => $request->is_read]);

        return $this->success(
            new MessageResource($messages->refresh()),
            'success'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
