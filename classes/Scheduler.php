<?php

namespace Cleanse\Feast\Classes;

use Cleanse\Feast\Classes\Scheduler\LodestoneChecker;
use Cleanse\Feast\Classes\Scheduler\UpdateSeason;
use Cleanse\Feast\Models\FeastSettings;

/**
 * i. Check result page of current season
 * ii. if a result page, update final results and then advance the season.
 * iii. if no result page, update the daily standings
 * iv. If no season, do nothing.
 */
class Scheduler
{
    public $schedule;

    //Get the current season, mode, and amount.
    public function __construct($mode)
    {
        $this->schedule = FeastSettings::find($mode);
    }

    public function checkLodestone()
    {
        if (!isset($this->schedule)) {
            return false;
        }

        //i. Check "results" page of current season
        $checker = new LodestoneChecker($this->schedule->mode, $this->schedule->season);
        $check = $checker->checkResults();

        /**
         * ii. If there was a "results" page:
         */
        if ($check) {
            $finalize = new UpdateSeason($this->schedule->mode, $this->schedule->season, true);

            return $finalize->update();
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
        $checker = new LodestoneChecker($this->schedule->mode, $this->schedule->season, false);
        $check = $checker->checkResults();

        /**
         * iii. If there are current standings:
         */
        if ($check) {
            $daily = new UpdateSeason($this->schedule->mode, $this->schedule->season);

            $daily->update();

            return true;
        }

        //iv. Do nothing between seasons.
        return false;
    }
}
