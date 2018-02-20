<?php
namespace Uatthaphon\ActivityMonitor;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Uatthaphon\ActivityMonitor\Enums\LogLevel as LogLevelEnums;
use Uatthaphon\ActivityMonitor\Models\ActivityMonitor;

class ActivityMonitorLog
{

    /** @var boolean enabeled/Disable log */
    protected $enabled;

    /** @var string attribute name of the log */
    protected $logName = '';

    /** @var string [description] */
    protected $description = '';

    /** @var \Illuminate\Database\Eloquent\Model event happen to ... */
    protected $happenTo;

    /** @var \Illuminate\Database\Eloquent\Model event caused by ... */
    protected $actBy;

    /** @var array keep changes of medel events */
    protected $traces = [];

    /** @var array additional infomation */
    protected $meta = [];

    /** @var string user ip address */
    protected $ip = '';

    /** @var string user browser agent information */
    protected $agent = '';

    public function __construct(Repository $config, Request $request)
    {
        $this->enabled = $config['activitymonitor']['enabled'] ?: true;

        $this->ip = htmlspecialchars($request->ip());

        $this->agent = htmlspecialchars($request->userAgent());
    }

    public function logName($logName)
    {
        if (empty($logName)) {
            $this->logName = $config['activitymonitor']['default_attribute'];
        }

        $this->logName = $logName;

        return $this;
    }

    private function defineLogName($logName, $description)
    {
        $this->logName($logName);

        if ($description) {
            $this->description($description);
        }

        return $this;
    }

    public function debug($description)
    {
        $this->defineLogName(LogLevelEnums::DEBUG, $description);

        return $this;
    }

    public function error($description)
    {
        $this->defineLogName(LogLevelEnums::ERROR, $description);

        return $this;
    }

    public function fatal($description)
    {
        $this->defineLogName(LogLevelEnums::FATAL, $description);

        return $this;
    }

    public function info($description)
    {
        $this->defineLogName(LogLevelEnums::INFO, $description);

        return $this;
    }

    public function warning($description)
    {
        $this->defineLogName(LogLevelEnums::WARNING, $description);

        return $this;
    }

    public function description($description)
    {
        $this->description = $description;

        return $this;
    }

    public function happenTo(Model $model)
    {
        $this->happenTo = $model;

        return $this;
    }

    public function actBy(Model $model)
    {
        $this->actBy = $model;

        return $this;
    }

    public function traces(array $traces)
    {
        $this->traces = $traces;

        return $this;
    }

    public function metaReplace(array $meta)
    {
        $this->meta = $meta;

        return $this;
    }

    public function metaByKeyValue($key, $value)
    {
        $this->meta = array_add($this->meta, $key, $value);

        return $this;
    }

    public function metaByArray($meta)
    {
        if (empty($meta)) {
            return $this;
        }

        foreach ($meta as $key => $value) {
            $this->metaByKeyValue($key, $value);
        }

        return $this;
    }

    public function __call($method, $arguments)
    {
        if ($method == 'meta') {
            if (count($arguments) == 1) {
                return call_user_func_array(
                    [$this, 'metaByArray'],
                    $arguments
                );
            } elseif (count($arguments) == 2) {
                return call_user_func_array(
                    [$this, 'metaByKeyValue'],
                    $arguments
                );
            }
        }
    }

    public function save()
    {
        if (!$this->enabled) {
            return;
        }

        $activityMonitor = new ActivityMonitor;

        if ($this->happenTo) {
            $activityMonitor->happenTo()->associate($this->happenTo);
        }

        if ($this->actBy) {
            $activityMonitor->actBy()->associate($this->actBy);
        }

        $activityMonitor->log_name = $this->logName;

        $activityMonitor->traces = $this->traces;

        $activityMonitor->meta = $this->meta;

        $activityMonitor->description = $this->description;

        $activityMonitor->ip = $this->ip;

        $activityMonitor->agent = $this->agent;

        $activityMonitor->save();

        return $activityMonitor;
    }
}
