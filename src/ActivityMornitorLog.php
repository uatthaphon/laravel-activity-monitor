<?php
namespace Uatthaphon\ActivityMonitor;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Uatthaphon\ActivityMonitor\Models\ActivityMonitor;

class ActivityMonitorLog
{
    /** @var boolean enabeled/Disable log */
    protected $enabled;

    /** @var string attribute name of the log */
    protected $logName = '';

    /** @var string [description] */
    protected $description = '';

    /** @var \Illuminate\Database\Eloquent\Model event happened to ... */
    protected $event;

    /** @var \Illuminate\Database\Eloquent\Model event caused by ... */
    protected $by;

    /** @var array keep changes of medel events */
    protected $history = [];

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

    public function description($description)
    {
        $this->description = $description;

        return $this;
    }

    public function on(Model $model)
    {
        $this->event = $model;

        return $this;
    }

    public function by(Model $model)
    {
        $this->by = $model;

        return $this;
    }

    public function history(array $history)
    {
        $this->history = $history;

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

    public function metaByArray(array $meta)
    {
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

        if ($this->event) {
            $activityMonitor->event()->associate($this->event);
        }

        if ($this->by) {
            $activityMonitor->by()->associate($this->by);
        }

        $activityMonitor->log_name = $this->logName;

        $activityMonitor->history = $this->history;

        $activityMonitor->meta = $this->meta;

        $activityMonitor->description = $this->description;

        $activityMonitor->ip = $this->ip;

        $activityMonitor->agent = $this->agent;

        $activityMonitor->save();

        return $activityMonitor;
    }
}
