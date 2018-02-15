
<?php

namespace Uatthaphon\ActivityMonitor\Traits;

use Uatthaphon\ActivityMonitor\Models\ActivityMonitor;

trait ActivityMonitorRelations
{
    public function activity()
    {
        return $this->morphMany(ActivityMonitor::class, 'act_by');
    }

    public function activityOn()
    {
        return $this->morphMany(ActivityMonitor::class, 'happen_to');
    }
}
