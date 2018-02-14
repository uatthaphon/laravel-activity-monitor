<?php

namespace Uatthaphon\ActivityMonitor\Transformers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Uatthaphon\ActivityMonitor\Models\ActivityMonitor;

class ActivityMonitorViewTransformer implements ActivityMonitorViewTransformerInterface
{

    public function transform(ActivityMonitor $activityMonitor)
    {
        return $activityMonitor;
    }

    public function transforms(Collection $activitiesMonitor)
    {
        return $activitiesMonitor->each(function ($activityMonitor) {
            $activityMonitor = $this->transform($activityMonitor);
        });
    }

    public function transformPaginator(LengthAwarePaginator $activitiesMonitor)
    {
        $queryString = [];

        parse_str($_SERVER['QUERY_STRING'], $queryString);

        $activityMonitorCollection = new Collection($activitiesMonitor->items());

        return new LengthAwarePaginator(
            $this->transforms($activityMonitorCollection),
            $activitiesMonitor->total(),
            $activitiesMonitor->perPage(),
            $activitiesMonitor->currentPage(),
            [
                'path' => \URL::current(),
                'query' => $queryString
            ]
        );
    }
}
