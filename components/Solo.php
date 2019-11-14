<?php

namespace Cleanse\Feast\Components;

use ValidationException;
use Cms\Classes\ComponentBase;

use Cleanse\Feast\Models\FeastSettings;
use Cleanse\Feast\Models\FeastSolo;

class Solo extends ComponentBase
{
    public $settings;
    public $rankings;
    public $season;
    public $amount;

    public function componentDetails()
    {
        return [
            'name' => 'Overall Solo Feast Rankings',
            'description' => 'Grabs the rankings for feast solo players.'
        ];
    }

    public function defineProperties()
    {
        return [
            'season' => [
                'title' => 'Feast Season',
                'description' => 'If you wish to specify a specific season.',
                'default' => '{{ :season }}',
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'Please enter a season number.'
            ]
        ];
    }

    public function onRun()
    {
        $this->settings = FeastSettings::where(['mode' => 'solo'])->first();

        $this->amount = $this->settings->take;
        $this->season = $this->page['season'] = $this->property('season') ?: $this->settings->season;

        $this->rankings = $this->page['rankings'] = $this->loadRankings();
    }

    private function loadRankings()
    {
        return FeastSolo::where('season', $this->season)
            ->orderBy('rating', 'desc')
            ->paginate($this->amount);
    }
}
