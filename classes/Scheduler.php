<?php

namespace Cleanse\Feast\Classes;

use Cleanse\Feast\Classes\Scheduler\LodestoneChecker;
use Cleanse\Feast\Classes\Scheduler\FinalizeSeason;
use Cleanse\Feast\Classes\Scheduler\UpdateRankings;

/**
 * i. Check result page of current season
 * ii. if a result page, update final results and then advance the season.
 * iii. if no result page, update the daily standings
 * iv. If no season, do nothing.
 */
class Scheduler
{
    public $mode;
    public $season;

    public function __construct($mode, $season)
    {
        $this->mode = $mode;
        $this->season = $season;
    }

    public function checkLodestone()
    {
        //i. Check results page of current season
        $checker = new LodestoneChecker($this->mode, $this->season);
        $check = $checker->checkResults();

        /**
         * ii. If there was a results page:
         */
        if ($check) {
            $finalize = new FinalizeSeason($this->mode, $this->season);

            return $finalize->complete();
        }

        /**
         * Else check the daily standings:
         */
        return $this->checkStandingsPage();
    }

    /**
     * Check standings page of current season
     */
    private function checkStandingsPage()
    {
        $checker = new LodestoneChecker($this->mode, $this->season, false);
        $check = $checker->checkResults();

        /**
         * iii. If there are current standings:
         */
        if ($check) {
            $daily = new UpdateRankings($this->mode, $this->season);

            $daily->update();

            return true;
        }

        //iv. Do nothing between seasons.
        return false;
    }
}
