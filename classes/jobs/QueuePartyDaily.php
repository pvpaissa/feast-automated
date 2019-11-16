<?php

namespace Cleanse\Feast\Classes\Jobs;

use Cleanse\Feast\Classes\Updaters\PartyRankingsUpdate;

class QueuePartyDaily
{
    public function fire($job, $data)
    {
        $crawl = new PartyRankingsUpdate;
        $crawl->updateDay($data);

        $job->delete();
    }
}
