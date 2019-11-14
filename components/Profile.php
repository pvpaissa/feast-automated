<?php

namespace Cleanse\Feast\Components;

use Cms\Classes\ComponentBase;

use Cleanse\PvPaissa\Models\Player;
use Cleanse\Feast\Models\FeastSettings;

class Profile extends ComponentBase
{
    public $character;
    public $season;

    public function componentDetails()
    {
        return [
            'name' => 'FFXIV Feast character profile.',
            'description' => 'Grabs the Season\'s Feast daily stats.'
        ];
    }

    public function defineProperties()
    {
        return [
            'character' => [
                'title' => 'Character Slug',
                'description' => 'Look up the character by their id.',
                'default' => '{{ :character }}',
                'type' => 'string'
            ],
            'season' => [
                'title' => 'Feast Season',
                'description' => 'Look up the season\'s stats.',
                'default' => '{{ :season }}',
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'Please enter a proper season number.'
            ]
        ];
    }

    public function onRun()
    {
        $this->season = $this->page['season'] = $this->property('season') ?: $this->getDefaultSettings();
        $this->character = $this->page['character'] = $this->loadRankings();
    }

    public function loadRankings()
    {
        $character = $this->property('character');

        return Player::with([
            'solo_daily' => function ($q) {
                $q->where('season', $this->season);
                $q->orderBy('created_at', 'desc');
            },
            'party_daily' => function ($q) {

                if ($this->season > 4) {
                    $OldPartySeason = 4;
                } else {
                    $OldPartySeason = $this->season;
                }

                $q->where('season', $OldPartySeason);
                $q->orderBy('created_at', 'desc');
            }
        ])
            ->where('character', $character)
            ->first();
    }

    private function getDefaultSettings()
    {
        $settings = FeastSettings::where(['mode' => 'solo'])->first();

        return $settings->season;
    }
}
