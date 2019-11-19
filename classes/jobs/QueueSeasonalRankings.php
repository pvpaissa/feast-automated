<?php

namespace Cleanse\Feast\Classes\Jobs;

use Cleanse\Feast\Classes\Updaters\SoloRankingsUpdate;
use Cleanse\Feast\Classes\Updaters\PartyRankingsUpdate;
use Cleanse\Feast\Classes\RankingsUpdate;

class QueueSeasonalRankings
{
    public function fire($job, $data)
    {
        if ($data['type'] == 'solo') {
            $crawl = new SoloRankingsUpdate;
            $crawl->seasonPlayerSort($data);
        }

        if ($data['type'] == 'party') {
            $crawl = new PartyRankingsUpdate;
            $crawl->seasonPartySort($data);
        }

        $job->delete();
    }
}
