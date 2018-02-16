<?php

namespace Uatthaphon\ActivityMonitor\Enums;

class LogLevel
{
    const DEBUG = 'debug';
    const ERROR = 'error';
    const FATAL = 'fatal';
    const INFO = 'info';
    const WARNING = 'warning';

    public static function levels()
    {
        return [
            self::DEBUG,
            self::ERROR,
            self::FATAL,
            self::INFO,
            self::WARNING,
        ];
    }
}
