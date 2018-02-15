<?php

namespace Uatthaphon\ActivityMonitor\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Uatthaphon\ActivityMonitor\ActivityMonitorLog;
use Uatthaphon\ActivityMonitor\Traits\LogTracesModelEvent;

trait ModelEventActivity
{
    use LogTracesModelEvent;

    private static $description = 'eloquent models event';

    protected static function bootModelEventActivity()
    {
        foreach (static::getModelEvents() as $eventName) {
            static::$eventName(function (Model $model) use ($eventName) {
                if (!static::isLoggable()) {
                    return;
                }

                if (!static::isSomeChangeOnModelByEvent($model, $eventName)) {
                    return;
                }

                app(ActivityMonitorLog::class)
                    ->logName($eventName)
                    ->description(static::$description)
                    ->happenTo($model)
                    ->traces(static::perpareTraces($model, $eventName))
                    ->meta(static::prepareMeta($eventName))
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

    protected static function prepareMeta($eventName)
    {
        if (isset(static::$createdEventMeta) && $eventName == 'created') {
            return static::$createdEventMeta;
        } elseif (isset(static::$updatedEventMeta) && $eventName == 'updated') {
            return static::$updatedEventMeta;
        } elseif (isset(static::$deletedEventMeta) && $eventName == 'deleted') {
            return static::$deletedEventMeta;
        }

        return null;
    }
}
