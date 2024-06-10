<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Str;

trait Sluggable
{
    protected static function bootSluggable(): void
    {
        static::creating(function (self $model) {
            $model->resolveSLugOrUsername($model);
        });
    }

    /**
     * Slug and username resolver
     */
    public function resolveSLugOrUsername($model): void
    {
        if ($model instanceof User) {
            $this->checkUsername($model);
        } else {
            $this->checkSlug($model);
        }
    }

    /**
     * resolve the slug the given model
     *
     * @param  Sluggable  $model
     */
    public function checkSlug(self $model): void
    {
        $slug = $model->slug ?? Str::slug($model->name);
        while (self::where('slug', $slug)->exists()) {
            $slug = Str::slug($model->name).'-'.$model?->id;
        }

        $this->setAttribute('slug', $slug);
    }

    /**
     * Resolve the username of the model
     *
     * @param  Sluggable  $model
     */
    protected function checkUsername(self $model): void
    {
        $username = Str::slug($model->firstname).'.'.Str::slug($model->lastname);
        while (self::where('username', $username)->exists()) {
            $username = Str::slug($model->firstname).'.'.Str::slug($model->lastname).'-'.$model?->id;
        }

        $this->setAttribute('username', $username);
    }
}
