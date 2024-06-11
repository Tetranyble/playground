<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Uuidable
{
    protected static function bootUuidable()
    {
        static::creating(function (self $model) {
            $model->syncUuid($model);
        });
    }

    /**
     * sets the uuid field
     */
    public function syncUuid(mixed $model): void
    {
        $uuid = $model->uuid ?? Str::uuid()->toString();
        while (self::where('uuid', $uuid)->exists()) {
            $uuid = Str::uuid()->toString();
        }

        $this->setAttribute('uuid', $uuid);
    }
}
