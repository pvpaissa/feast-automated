<?php

namespace Cleanse\Feast\Classes\Jobs;

use Cleanse\Feast\Classes\Updaters\SoloRankingsUpdate;
use Cleanse\Feast\Classes\Updaters\PartyRankingsUpdate;

class QueueDailyRankings
{
    public function fire($job, $data)
    {
        if ($data['type'] == 'solo') {
            $crawl = new SoloRankingsUpdate;
            $crawl->dailyPlayerSort($data);
        }

        if ($data['type'] == 'party') {
            $crawl = new PartyRankingsUpdate;
            $crawl->dailyPartySort($data);
        }

        $job->delete();
    }
}
