<?php

namespace Cleanse\Feast\Classes\Scheduler;

use Queue;

use Cleanse\Feast\Classes\FeastHelper;
use Cleanse\Feast\Models\FeastSettings;

class FinalizeSeason
{
    public $mode;
    public $season;

    private $dcs;
    private $day;
    private $tiers;
    private $classify;

    public function __construct($mode, $season)
    {
        $this->mode = $mode;
        $this->season = $season;
    }

    //Queue up the final standings
    public function complete()
    {
        $feast = new FeastHelper;

        $this->day = $feast->yearDay();
        $this->dcs = $feast->datacenters;
        $this->tiers = $feast->tiers;
        $this->classify = $feast->classifyString($this->mode);

        $this->queueUpFinalResults();

        $this->progressSeason();

        return true;
    }

    private function queueUpFinalResults()
    {
        if ($this->mode === 'party') {
            $this->queueParty();
        } else {
            $this->queueSolo();
        }

        //After we get the finalized data, queue up the final rankings
        $this->queueUpFinalRankings();
    }

    private function queueSolo()
    {
        foreach ($this->dcs as $dc) {
            foreach ($this->tiers as $tier) {
                $data = [
                    'datacenter' => $dc,
                    'day' => $this->day,
                    'tier' => $tier,
                    'season' => $this->season,
                    'result' => true
                ];

                Queue::push('\Cleanse\Feast\Classes\Jobs\QueueSoloDaily', $data);
            }
        }
    }

    private function queueParty()
    {
        foreach ($this->dcs as $dc) {
            $data = [
                'datacenter' => $dc,
                'day' => $this->day,
                'season' => $this->season,
                'result' => true
            ];

            Queue::push('\Cleanse\Feast\Classes\Jobs\QueuePartyDaily', $data);
        }
    }

    private function queueUpFinalRankings()
    {
        $data = [
            'day' => $this->day,
            'season' => $this->season
        ];

        Queue::push('\Cleanse\Feast\Classes\Jobs\QueueDailyRankings', $data);
    }

    private function progressSeason()
    {
        $advanceSeason = $this->season + 1;

        $update = FeastSettings::where(['mode' => $this->mode])->first();
        $update->season = $advanceSeason;

        $update->save();
    }
}
