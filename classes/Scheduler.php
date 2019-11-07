<?php

namespace Cleanse\Feast\Classes;

use Cleanse\Feast\Classes\Scheduler\LodestoneChecker;
use Cleanse\Feast\Classes\Scheduler\FinalizeSeason;

class Scheduler
{
    /**
     * @param $mode
     * @param $season
     * @return bool
     *
     * Check result page of current season before the daily update
     * if no result page, update the daily standings
     * if a result page, update final results and then advance the season.
     * If no season, do nothing.
     */
    public function checkLodestone($mode, $season)
    {
        //Check results page of current season
        $checker = new LodestoneChecker($mode, $season);
        $check = $checker->checkResults();

        //*Update final standings
        //if there is a response of '200' (true)
        //queue up the final standings and advance the season #
        if ($check) {
            $finalize = new FinalizeSeason;

            return $finalize->complete($mode, $season);
        }

        return false;

        //No results yet = queue jobs for a new day**
        //return $this->checkStandingsPage();
    }

    /** queue jobs for a new day */
//    private function checkStandingsPage()
//    {
//        return $this->crawlLodestoneResults();
//    }
//
//    public function calculateRankings($mode, $season)
//    {
//        return $this->calculateDatabaseData($mode, $season);
//    }
}
