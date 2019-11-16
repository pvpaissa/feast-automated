<?php

namespace Cleanse\Feast\Classes\Jobs;

use Cleanse\Feast\Classes\Rankings\OutdatedRankings;

class QueueOutdatedRankings
{
    public function fire($job, $data)
    {
        if ($data['type'] == 'solo') {
            $fix = new OutdatedRankings;
            $fix->updateSolo($data);
        }

        if ($data['type'] == 'party') {
            $fix = new OutdatedRankings;
            $fix->updateLightParty($data);
        }

        if ($data['type'] == 'legacy-party') {
            $fix = new OutdatedRankings;
            $fix->updateLegacyParty($data);
        }

        $job->delete();
    }
}
