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

    public function happenTo()
    {
        return $this->morphTo();
    }

    public function actBy()
    {
        return $this->morphTo();
    }

    public function getMetaData($key)
    {
        return array_get($this->meta->get(), $key, null);
    }


    public function setTracesAttribute($value)
    {
        $this->attributes['traces'] = empty(array_filter($value)) ? null :json_encode($value);
    }

    public function getTracesAttribute($value)
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

    public function scopeInLogName($query, $logNames)
    {
        $logNames = is_array($logNames) ? $logNames : func_get_args();

        return $query->whereIn('log_name', $logNames);
    }

    public function scopeHappenTo($query, Model $model)
    {
        return $qeury
            ->where('happen_to_type', $model->getMorphClass())
            ->where('happen_to_id', $model->getKey());
    }

    public function scopeActBy($query, Model $model)
    {
        return $qeury
            ->where('act_by_type', $model->getMorphClass())
            ->where('act_by_id', $model->getKey());
    }
}
