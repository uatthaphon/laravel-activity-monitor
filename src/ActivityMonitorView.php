<?php
namespace Uatthaphon\ActivityMonitor;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Uatthaphon\ActivityMonitor\ActivityMonitorLog as AMLog;
use Uatthaphon\ActivityMonitor\Models\ActivityMonitor;
use Uatthaphon\ActivityMonitor\Transformers\ActivityMonitorViewTransformerInterface;

class ActivityMonitorView
{
    protected $activityMonitortransformer;

    protected $activityMonitor;

    protected $logName;

    protected $logNames = null;

    protected $limit = null;

    protected $sortBy = 'asc';

    protected $buildable = ['logName', 'inLogName', 'limit', 'sortBy'];

    public function __construct()
    {
        $this->activityMonitor = new ActivityMonitor;
        $this->activityMonitortransformer = app(ActivityMonitorViewTransformerInterface::class);
    }

    public function logName($logName)
    {
        $this->logName = $logName;

        $this->logNames = null;

        return $this;
    }

    public function inLogName($logNames)
    {
        $this->logNames = is_array($logNames) ? $logNames : func_get_args();

        $this->logName = null;

        return $this;
    }

    public function debug()
    {
        $this->logName(AMLog::DEBUG);

        return $this;
    }

    public function error()
    {
        $this->logName(AMLog::ERROR);

        return $this;
    }

    public function fatal()
    {
        $this->logName(AMLog::FATAL);

        return $this;
    }

    public function info()
    {
        $this->logName(AMLog::INFO);

        return $this;
    }

    public function warning()
    {
        $this->logName(AMLog::WARNING);

        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function sortBy($sortBy)
    {
        $this->sortBy = $this->filterSortOrder($sortBy);

        return $this;
    }

    public function sortByDesc()
    {
        $this->sortBy = 'desc';

        return $this;
    }

    public function sortByAsc()
    {
        $this->sortBy = 'asc';

        return $this;
    }

    public function get()
    {
        $builder = $this->activityMonitor;

        $builder = $builder->newQuery();

        $this->build($builder);

        return $this->activityMonitortransformer
            ->transforms($builder->get());
    }

    public function all()
    {
        return $this->get();
    }

    public function paginate($perPage = 5)
    {
        $builder = $this->activityMonitor;

        $builder = $builder->newQuery();

        $this->build($builder);

        return $this->activityMonitortransformer
            ->transformPaginator($builder->paginate($perPage));
    }

    /**
     * Apply available condition from bilder
     *
     * @param  Builder $builder The tracks builder
     * @return Builder
     */
    protected function build(Builder $builder)
    {
        foreach ($this->buildable as $query) {
            $methodName = 'apply'.ucfirst($query);

            if (method_exists($this, $methodName)) {
                $this->{$methodName}($builder);
            }
        }
    }

    protected function filterSortOrder($sortOrder)
    {
        return in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'asc';
    }

    protected function applyLogName(Builder $builder)
    {
        return ($this->logName) ? $builder->logName($this->logName) : null;
    }

    protected function applyInLogName(Builder $builder)
    {
        return ($this->logNames) ? $builder->inLogNames($this->logNames) : null;
    }

    protected function applyLimit(Builder $builder)
    {
        return (!is_null($this->limit)) ? $builder->take($this->limit) : $builder;
    }

    protected function applySortBy(Builder $builder)
    {
        return $builder->orderBy('id', $this->sortBy);
    }

}
