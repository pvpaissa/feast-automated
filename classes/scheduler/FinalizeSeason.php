<?php

namespace Cleanse\Feast\Classes\Scheduler;

use Cleanse\Feast\Models\FeastSettings;

class FinalizeSeason
{
    //Queue up the final standings
    public function complete($mode, $season)
    {
        $this->queueUpResults($mode, $season);

        $this->progressSeason($mode, $season);

        return true;
    }

    private function queueUpResults($mode, $season)
    {
        return true;
    }

    private function progressSeason($mode, $season)
    {
        $advanceSeason = $season + 1;

        $update = FeastSettings::where(['mode' => $mode])->first();
        $update->season = $advanceSeason;
        $update->save();
    }
}
