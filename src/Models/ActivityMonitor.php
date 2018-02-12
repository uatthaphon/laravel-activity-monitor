<?php
namespace Uatthaphon\ActivityMonitor\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class ActivityMonitor extends Model
{
    protected $table = 'activity_monitors';

    protected $casts = [
        'meta' => 'collection',
    ];

    public function event()
    {
        return $this->morphTo();
    }

    public function by()
    {
        return $this->morphTo();
    }

    public function getMetaData($key)
    {
        return array_get($this->meta->get(), $key, null);
    }


    public function setHistoryAttribute($value)
    {
        $this->attributes['history'] = empty(array_filter($value)) ? null :json_encode($value);
    }

    public function getHistoryAttribute($value)
    {
        return collect(json_decode($value, true));
    }

    public function setMetaAttribute($value)
    {
        $this->attributes['meta'] = empty(array_filter($value)) ? null :json_encode($value);
    }

    public function getMetaAttribute($value)
    {
        return collect(json_decode($value, true));
    }

    public function scopeInAttribute($query, $attributes)
    {
        $attributes = is_array($attributes) ? $attributes : func_get_args();

        return $query->whereIn('attribute', $attributes);
    }

    public function scopeByBy($query, Model $by)
    {
        return $qeury
            ->where('by_type', $by->getMorphClass())
            ->where('by_id', $by->getKey());
    }

    public function scopeForEvent($query, Model $event)
    {
        return $qeury
            ->where('event_type', $event->getMorphClass())
            ->where('event_id', $event->getKey());
    }
}
