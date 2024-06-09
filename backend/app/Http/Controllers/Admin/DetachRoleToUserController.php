<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleCollection;
use App\Models\User;
use Illuminate\Http\Request;

class DetachRoleToUserController extends Controller
{
    /**
     * @OA\Patch(
     * path="/admin/users/{user}/roles/attach",
     * operationId="Api/Admin/DetachRoleToUserController::__invoke",
     * tags={"Users Administration"},
     * security={ * {"sanctum": {} } * },
     * summary="DeAttach role to user.",
     * description="DeAttach role to user.",
     *
     *    @OA\RequestBody(
     *         description="DeAttach role to user",
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             example={
     *                 "role": 1,
     *             }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "message":"success",
     *                 "status": true,
     *                 "data": {
     *                     "name": "The program resource",
     *                     "created_at" : "2022-09-08T12:29:54.000000Z",
     *                     "updated_at" : "2022-09-08T12:29:54.000000Z"
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
     *  Store a newly created resource in storage.
     *
     * @return RoleCollection
     */
    public function __invoke(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|numeric',
        ]);

        $user->removeRoles(
            $request->role
        );

        return new RoleCollection($user->roles);
    }
}
