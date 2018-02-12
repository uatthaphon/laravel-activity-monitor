<?php

namespace Uatthaphon\ActivityMonitor\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Uatthaphon\ActivityMonitor\ActivityMonitorLog;

trait LogHistoryModelEvent
{
    protected static $oldHistory;

    /**
     * Add history data ot oldHistory if updated allowed
     */
    protected static function bootLogHistoryModelEvent()
    {
        foreach (static::getModelEvents() as $eventName) {
            if ($eventName == 'updated') {
                static::updating(function (Model $model) {
                    static::setOldHistory($model);
                });
            }
        }
    }

    protected static function setOldHistory(Model $model)
    {
        static::$oldHistory = array_only($model->getOriginal(), static::$loggable);
    }

    protected static function perpareHistory(Model $model, $eventName)
    {
        if (!isset(static::$loggable)) {
            return null;
        }

        $history = array_only($model->attributes, static::$loggable);

        if (isset(static::$oldHistory)) {
            $history = array_diff(static::$oldHistory, $history);
        }

        return $history;
    }

    protected static function isSomeChange(Model $model)
    {
        if (isset(static::$oldHistory)) {
            $history = array_only($model->attributes, static::$loggable);

            return !empty(array_filter(array_diff($history, static::$oldHistory)));
        }

        return false;
    }
}
