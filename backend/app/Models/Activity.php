<?php

namespace App\Models;

use App\Enums\TrilioStatus;
use App\Traits\Sluggable;
use App\Traits\Uuidable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, Sluggable, SoftDeletes, Uuidable;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'uuid',
        'status',
        'slug',
        'start_date',
        'end_date',
        'project_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => TrilioStatus::class,
    ];

    /**
     * Get the owner of this project
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
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
     * @param  Project[]|string[]  ...$projects
     */
    public function scopeActivityFor(Builder $builder, ...$projects): Builder
    {
        return $builder->when(array_filter($projects), function ($q) use ($projects) {
            $q->where(function ($q) use ($projects) {
                collect($projects)->map(function ($project) use ($q) {
                    return $project instanceof Project ?
                        $q->orWhere('project_id', $project->id) : (
                            is_numeric($project) ? $q->orWhere('project_id', $project) :
                                $q->orWhere('project_id', function ($q) use ($project) {
                                    $q->from('projects')
                                        ->select('id')
                                        ->where('uuid', $project);
                                })
                        );
                });
            });
        });
    }
}
