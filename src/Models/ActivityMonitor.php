<?php

namespace Uatthaphon\ActivityMonitor\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Uatthaphon\ActivityMonitor\Enums\LogLevel as LogLevelEnums;

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

    public function scopeLogName($query, $logName)
    {
        return $query->where('log_name', $logNames);
    }

    public function scopeInLogNames($query, $logNames)
    {
        return $query->whereIn('log_name', $logNames);
    }

    public function scopeHappenTo($query, Model $model)
    {
        return $query
            ->where('happen_to_type', $model->getMorphClass())
            ->where('happen_to_id', $model->getKey());
    }

    public function scopeActBy($query, Model $model)
    {
        return $query
            ->where('act_by_type', $model->getMorphClass())
            ->where('act_by_id', $model->getKey());
    }

    public function scopeDebug($query)
    {
        return $query->where('log_name', LogLevelEnums::DEBUG);
    }

    public function scopeError($query)
    {
        return $query->where('log_name', LogLevelEnums::ERROR);
    }

    public function scopeFatal($query)
    {
        return $query->where('log_name', LogLevelEnums::FATAL);
    }

    public function scopeInfo($query)
    {
        return $query->where('log_name', LogLevelEnums::INFO);
    }

    public function scopeWarning($query)
    {
        return $query->where('log_name', LogLevelEnums::WARNING);
    }
}
