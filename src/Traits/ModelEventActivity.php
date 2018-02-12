<?php

namespace Uatthaphon\ActivityMonitor\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Uatthaphon\ActivityMonitor\ActivityMonitorLog;
use Uatthaphon\ActivityMonitor\Traits\LogHistoryModelEvent;

trait ModelEventActivity
{
    use LogHistoryModelEvent;

    private static $description = 'Eloquent models event';
    protected static function bootModelEventActivity()
    {
        foreach (static::getModelEvents() as $eventName) {
            static::$eventName(function (Model $model) use ($eventName) {
                if (!static::isLoggable()) {
                    return;
                }

                if (!static::isSomeChange($model)) {
                    return;
                }

                app(ActivityMonitorLog::class)
                    ->logName($eventName)
                    ->description(static::$description)
                    ->on($model)
                    ->history(static::perpareHistory($model, $eventName))
                    ->save();
            });
        }
    }

    protected static function getModelEvents()
    {
        if (isset(static::$eventsToLog) &&
            !empty(array_filter(static::$eventsToLog))
        ) {
            return static::$eventsToLog;
        }

        return ['created','updated', 'deleted'];
    }

    protected static function isLoggable()
    {
        if (!isset(static::$loggable)) {
            return false;
        }

        if (empty(array_filter(static::$loggable))) {
            return false;
        }

        return true;
    }
}
