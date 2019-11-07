<?php

namespace Cleanse\Feast;

use Event;
use DateTime;
use System\Classes\PluginBase;

use Cleanse\Feast\Models\FeastSettings;
use Cleanse\Feast\Classes\Scheduler;

class Plugin extends PluginBase
{
    public $solo;
    public $lp;

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
            'Cleanse\Feast\Components\Demo' => 'cleanseFeastDemo',
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

//    public function registerSchedule($schedule)
//    {
//        $this->solo = FeastSettings::where('season', 'solo')->first();
//        $this->lp = FeastSettings::where('season', 'party')->first();
//
//        $schedule->call(function () {
//            $getSolo = new Scheduler;
//            $getSolo->checkLodestone('solo', $this->solo);
//        })->cron('3 4 * * *');
//
//        $schedule->call(function () {
//            $getLP = new Scheduler;
//            $getLP->calculateRankings('solo', $this->solo);
//        })->cron('33 4 * * *');
//
//        $schedule->call(function () {
//            $getLP = new Scheduler;
//            $getLP->checkLodestone('party', $this->lp);
//        })->cron('3 5 * * *');
//
//        $schedule->call(function () {
//            $getLP = new Scheduler;
//            $getLP->calculateRankings('party', $this->lp);
//        })->cron('33 5 * * *');
//    }
}
