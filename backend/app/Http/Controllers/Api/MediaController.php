<?php

namespace App\Http\Controllers\Api;

use App\Enums\Disk;
use App\Http\Controllers\Controller;
use App\Http\Requests\MediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Services\FileSystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * @OA\Post(
     * path="/media",
     * operationId="MediaController::__invoke",
     * tags={"Media"},
     * summary="Upload media",
     * security={ * {"sanctum": {} } * },
     * description="Create new media file",
     *
     *    @OA\RequestBody(
     *         description="Create new media file",
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             example={
     *                 "photo": "Ugbanawaji",
     *             },
     *
     *             @OA\Schema(ref="#/components/schemas/MediaRequest")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="The uploaded media file",
     *
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "message":"success",
     *                 "status": "success",
     *                 "status_code": 200,
     *                 "data": {
     *                     "disk" : "s3-public",
     *                     "path": "http://localhost:8000/storage/images/DU8Y739YQWHDLKuhluwqehdluiwked.png",
     *                     "id" : 24,
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
     * @return JsonResponse
     */
    public function post(MediaRequest $request, FileSystem $system)
    {

        $url = $system->store(
            $request->file('media'),
            'media',
            $system->disk
        );
        $media = Media::create([
            'path' => $url,
            'size' => $request->file('media')->getSize(),
            'disk' => $system->disk,
            'current' => true,
            'mime_type' => $request->file('media')->getMimeType(),
        ]);

        //        if (filter_var($request->media, FILTER_VALIDATE_URL)) {
        //            // Process the image URL
        //            $imageUrl = $request->media;
        //            // Your logic to handle the URL
        //        } elseif ($request->hasFile('image')) {
        //            // Process the uploaded image file
        //            $imageFile = $request->file('image');
        //            $filePath = $imageFile->store('images', 'public');
        //            // Your logic to handle the file upload
        //        }

        return $this->success(
            new MediaResource($media), 'success');
    }

    /**
     * @OA\Get (
     *     path="/media/{mediaUuid}",
     *     summary="The media resource",
     *     description="The media resource",
     *     operationId="Api/MediaController::show",
     *     description="The media resource.",
     *     tags={"Media"},
     *
     *     @OA\Parameter(
     *         name="mediaUuid",
     *         in="path",
     *         description="The media resource uuid",
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
     *             @OA\Schema(ref="#/components/schemas/MediaResource"),
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
    public function show(Media $media)
    {
        return $this->success(
            new MediaResource($media),
            'success'
        );

    }
}
