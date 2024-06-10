<?php

namespace App\Models;

use App\Traits\Sluggable;
use App\Traits\Uuidable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory, Sluggable, Uuidable;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'uuid',
        'status',
        'slug',
    ];

    /**
     * Get the owner of this project
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check for ownership this project
     */
    public function isOwner(User $user): bool
    {
        return $user->id === $this->user_id;

    }

    public function scopeSearch(Builder $builder, ?string $terms = null): Builder
    {
        return $builder->where(function ($builder) use ($terms) {
            collect(explode(' ', $terms))->filter()->each(function ($term) use ($builder) {
                $term = '%'.$term.'%';
                $builder->orWhere('name', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('id', $term);
            });
        });
    }
}
