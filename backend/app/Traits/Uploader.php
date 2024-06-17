<?php

namespace App\Traits;

use App\Enums\Disk;
use App\Enums\MediaPurpose;
use App\Models\Media;
use App\Services\FileSystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;

trait Uploader
{
    public function upload(UploadedFile $file, string $description, string $attribution, string $location = 'media',
        MediaPurpose $purpose = MediaPurpose::BANNER, ?Disk $disk = null, ?callable $callback = null)
    {

        $media = $this->media()
            ->create([
                'path' => $this->uploadFile($file, $location),
                'size' => $file->getSize(),
                'disk' => $disk ?? app(FileSystem::class)->disk,
                'current' => true,
                'description' => $description,
                'attribution' => $attribution,
                'mime_type' => $file->getMimeType(),
                'mediable_id' => $this->id,
                'mediable_type' => get_class($this),
                'use' => $purpose,
            ]);

        return $callback ? $callback($this, $media) : $media;

    }

    public function uploadFile(UploadedFile $file, string $location = 'media', ?Disk $disk = null)
    {
        $file = app(FileSystem::class)
            ->store(
                $file,
                $location,
                $disk ?? app(FileSystem::class)->disk
            );

        return $file;
    }

    /**
     * Remove product file
     */
    public function recursiveRemoveMedia(): mixed
    {
        return $this->media->map(fn ($media) => $this->removeMedia($media));
    }

    public function removeFileIfExist(int $media): void
    {

    }

    public function updateMediaMeta(string $description, string $attribution, string $id)
    {

        return $this->media()
            ->updateOrCreate([
                'mediable_id' => $this->id,
                'mediable_type' => get_class($this),
                'uuid' => $id,
            ], [
                'description' => $description,
                'attribution' => $attribution,
            ]);
    }

    public function uploadMedia(UploadedFile $file, string $att, $des, ?int $id = null): Model
    {

        if ($id && $file) {

            $this->removeMedia($id);

            return $this->upload(
                $file,
                $des,
                $att,
                'media'
            );

        } else {
            $this->removeImages();

            return $this->upload(
                $file,
                $des,
                $att,
                'media'
            );

        }
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->where('mime_type', 'like', '%'.'image'.'%');
    }

    public function videos(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->where('mime_type', 'like', '%'.'video'.'%');
    }
}
