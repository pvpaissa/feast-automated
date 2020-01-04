<?php

namespace Cleanse\Feast\Classes\Scheduler;

use Carbon\Carbon;
use Queue;

use Cleanse\Feast\Classes\FeastHelper;
use Cleanse\Feast\Models\FeastSettings;

class UpdateSeason
{
    public $mode;
    public $season;
    public $result;

    private $dcs;
    private $day;
    private $tiers;

    public function __construct($mode, $season, $result = false)
    {
        $this->mode = $mode;
        $this->season = $season;
        $this->result = $result;
    }

    public function update()
    {
        $feast = new FeastHelper;

        $this->day = $feast->yearDay();
        $this->dcs = $feast->datacenters;
        $this->tiers = $feast->tiers;

        $this->queueUpResults();

        if ($this->result) {
            $this->progressSeason();
        }

        return true;
    }

    private function queueUpResults()
    {
        if ($this->mode === 'party') {
            $this->queueParty();
        } else {
            $this->queueSolo();
        }

        //After we get the daily data, queue up the daily/overall rankings
        $this->queueUpRankings();
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
                    'result' => $this->result
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
                'result' => $this->result
            ];

            Queue::push('\Cleanse\Feast\Classes\Jobs\QueuePartyDaily', $data);
        }
    }

    private function queueUpRankings()
    {
        $data = [
            'day' => $this->day,
            'season' => $this->season,
            'type' => $this->mode
        ];

        //Fix for Primal Daily Rankings.
        $future = Carbon::now()->addMinutes(15);

        Queue::later($future, '\Cleanse\Feast\Classes\Jobs\QueueDailyRankings', $data);
    }

    private function progressSeason()
    {
        $advanceSeason = $this->season + 1;

        $update = FeastSettings::where(['mode' => $this->mode])->first();
        $update->season = $advanceSeason;

        $update->save();
    }
}
