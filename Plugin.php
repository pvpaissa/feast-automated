<?php

namespace Cleanse\Feast;

use Event;
use DateTime;
use System\Classes\PluginBase;

use Cleanse\Feast\Classes\Scheduler;
use Cleanse\Feast\Models\Party;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'PvPaissa Feast Plugin',
            'description' => 'Adds FFXIV Feast Rankings to PvPaissa.',
            'author' => 'Paul Lovato',
            'icon' => 'icon-shield'
        ];
    }

    public function registerComponents()
    {
        return [
            'Cleanse\Feast\Components\Solo'         => 'cleanseFeastSolo',
            'Cleanse\Feast\Components\Party'        => 'cleanseFeastParty',
            'Cleanse\Feast\Components\Profile'      => 'cleanseFeastProfile',
            'Cleanse\Feast\Components\PartyList'    => 'cleanseFeastPartyList',
            'Cleanse\Feast\Components\PartyProfile' => 'cleanseFeastPartyProfile',
        ];
    }

    public function boot()
    {
        Event::listen('offline.sitesearch.query', function ($query) {

            $items = Party::where('name', 'like', "%${query}%")
                ->get();

            $results = $items->map(function ($item) use ($query) {

                $relevance = mb_stripos($item->name, $query) !== false ? 2 : 1;

                return [
                    'title' => $item->name,
                    'text' => $item->dc_group,
                    'url' => '/light-party/profile/' . $item->lodestone,
                    'relevance' => $relevance,
                ];
            });

            return [
                'provider' => 'Feast',
                'results' => $results,
            ];
        });
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'yearweek' => [$this, 'makeDateFromYearWeek']
            ]
        ];
    }

    public function makeDateFromYearWeek($yearWeek)
    {
        $year = substr($yearWeek, 0, -2);
        $week = substr($yearWeek, 4);

        $date = new DateTime();
        $date->setISODate($year, $week);

        return $date->format('Y-m-d');
    }

    public function registerSchedule($schedule)
    {
        $schedule->call(function () {
            $getSolo = new Scheduler('solo');
            $getSolo->checkLodestone();
        })->cron('3 4 * * *');

        $schedule->call(function () {
            $getLP = new Scheduler('solo');
            $getLP->calculateRankings();
        })->cron('33 4 * * *');

        $schedule->call(function () {
            $getLP = new Scheduler('party');
            $getLP->checkLodestone();
        })->cron('3 5 * * *');

        $schedule->call(function () {
            $getLP = new Scheduler('party');
            $getLP->calculateRankings();
        })->cron('33 5 * * *');
    }
}
