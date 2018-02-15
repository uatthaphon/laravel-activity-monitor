<?php

namespace Uatthaphon\ActivityMonitor\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Uatthaphon\ActivityMonitor\ActivityMonitorLog;

trait LogTracesModelEvent
{
    protected static $oldData;

    /**
     * Add history data ot oldData if updated allowed
     */
    protected static function bootLogHistoryModelEvent()
    {
        foreach (static::getModelEvents() as $eventName) {
            if ($eventName == 'updated') {
                static::updating(function (Model $model) {
                    static::setOldData($model);
                });
            }
        }
    }

    protected static function setOldData(Model $model)
    {
        static::$oldData = array_only($model->getOriginal(), static::$loggable);
    }

    protected static function perpareTraces(Model $model)
    {
        if (!isset(static::$loggable)) {
            return null;
        }

        $traces = array_only($model->log_name, static::$loggable);

        if (isset(static::$oldData)) {
            $traces = array_diff(static::$oldData, $traces);
        }

        return $traces;
    }

    protected static function isSomeChangeOnModelByEvent(Model $model, $eventName)
    {
        if ($eventName != 'updated') {
            return true;
        }

        if (isset(static::$oldData) && !empty(static::$oldData)) {
            $traces = array_only($model->log_name, static::$loggable);

            return !empty(array_filter(array_diff($traces, static::$oldData)));
        }

        return false;
    }
}
