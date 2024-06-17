<?php

namespace App\Traits;

use App\Enums\MediaPurpose;
use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Mediable
{
    use Uploader;

    /**
     * Get Mediable media
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get image
     */
    public function getImageAttribute(): Media|Model
    {
        return $this->media()
            ->where('current', true)
            ->where('mime_type', 'like', '%'.'image'.'%')
            ->latest()
            ->first() ?? $this->default();
    }

    /**
     * Get video
     */
    public function getVideoAttribute(): Media|Model
    {
        return $this->media()
            ->where('current', true)
            ->where('mime_type', 'like', '%'.'video'.'%')
            ->latest()->first() ?? $this->default('video');
    }

    /**
     * Get Videos
     */
    public function videos(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->where('mime_type', 'like', '%'.'video'.'%');
    }

    /**
     * Get images
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->where('mime_type', 'like', '%'.'image'.'%');
    }

    /**
     * Erase the every connect media file
     */
    public function clearMedia(): mixed
    {
        return $this->media->map(fn ($media) => $media->delete());
    }

    /**
     * Remove on media object
     */
    public function removeMedia(Media|string $media): bool
    {
        return ($media instanceof Media) ?
            $media->delete() : $this->media()->where('uuid', $media)->delete();
    }

    /**
     * Create new media instance on the fly when one does not exist
     */
    public function default(string $type = 'image'): Model
    {

        return ($this instanceof User) ?
            new Media([
                'path' => config('backend.media.profile.path'),
                'disk' => config('backend.media.profile.disk'),
            ]) : (($type === 'image') ? new Media([
                'path' => config('backend.media.image.path'),
                'disk' => config('backend.media.image.disk'),
            ]) :

            new Media([
                'path' => config('backend.media.video.path'),
                'disk' => config('backend.media.video.disk'),
            ]));
    }

    public function getFaviconAttribute(): string
    {
        $fav = $this->media()
            ->where('use', MediaPurpose::FAVICON)
            ->latest()->first();

        return $fav ?? favicon();
    }

    public function getLogoAttribute(): string
    {
        $logo = $this->media()
            ->where('use', MediaPurpose::LOGO)
            ->latest()->first();

        return $logo?->url ?? logo(request()->path());
    }
}
