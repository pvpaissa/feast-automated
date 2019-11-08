<?php

namespace Cleanse\Feast\Classes\Scheduler;

use Cleanse\Feast\Classes\FeastHelper;
use Cleanse\Feast\Models\FeastSettings;

class FinalizeSeason
{
    public $mode;
    public $season;

    public function __construct($mode, $season)
    {
        $this->mode = $mode;
        $this->season = $season;
    }

    //Queue up the final standings
    public function complete()
    {
        $this->queueUpResults();

        $this->progressSeason();

        return true;
    }

    private function queueUpResults()
    {
        if ($this->mode === 'party') {
            $this->finalizeParty();
        } else {
            $this->finalizeSolo();
        }
    }

    private function progressSeason()
    {
        $advanceSeason = $this->season + 1;

        $update = FeastSettings::where(['mode' => $this->mode])->first();
        $update->season = $advanceSeason;
        $update->save();
    }

    private function finalizeSolo()
    {
        $feast = new FeastHelper;

        $day = $feast->yearDay();
        $dcs = $feast->datacenters;

        foreach ($dcs as $dc) {
            $data = [
                'datacenter' => $dc,
                'day'        => $day,
                'season'     => $this->season
            ];

            Queue::push('\Cleanse\Feast\Classes\Jobs\QueueSoloUpdate', $data);
        }
    }

    private function finalizeParty()
    {
        $feast = new FeastHelper;

        $day = $feast->yearDay();
        $dcs = $feast->datacenters;

        foreach ($dcs as $dc) {
            $data = [
                'datacenter' => $dc,
                'day'        => $day,
                'season'     => $this->season
            ];

            Queue::push('\Cleanse\Feast\Classes\Jobs\QueuePartyUpdate', $data);
        }
    }
}
