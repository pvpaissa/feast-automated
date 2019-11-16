<?php

namespace Cleanse\Feast\Classes\Jobs;

use Cleanse\Feast\Classes\Updaters\SoloRankingsUpdate;

class QueueSoloDaily
{
    public function fire($job, $data)
    {
        $crawl = new SoloRankingsUpdate();
        $crawl->updateDay($data);

        $job->delete();
    }
}
