<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\TrilioStatus;
use App\Traits\Uuidable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes, Uuidable;

    protected $fillable = [
        'due_date',
        'name',
        'description',
        'status',
        'priority',
        'activity_id',
        'uuid',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'priority' => Priority::class,
        'status' => TrilioStatus::class,
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function scopeSearch(Builder $builder, ?string $terms = null): Builder
    {
        return $builder->where(function ($builder) use ($terms) {
            collect(explode(' ', $terms))->filter()->each(function ($term) use ($builder) {
                $term = '%'.$term.'%';
                $builder->orWhere('name', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('uuid', $term);
            });
        });
    }

    /**
     * Scopes the query to given project(s))
     *
     * @param  Activity[]|string[]  ...$activities
     */
    public function scopeTaskFor(Builder $builder, ...$activities): Builder
    {
        return $builder->when(array_filter($activities), function ($q) use ($activities) {
            $q->where(function ($q) use ($activities) {
                collect($activities)->map(function ($activity) use ($q) {
                    return $activity instanceof Activity ?
                        $q->orWhere('activity_id', $activity->id) : (
                            is_numeric($activity) ? $q->orWhere('activity_id', $activity) :
                                $q->orWhere('activity_id', function ($q) use ($activity) {
                                    $q->from('activities')
                                        ->select('id')
                                        ->where('uuid', $activity);
                                })
                        );
                });
            });
        });
    }
}
