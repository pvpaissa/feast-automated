<?php

namespace Cleanse\Feast;

use Event;
use DateTime;
use System\Classes\PluginBase;

use Cleanse\Feast\Classes\Scheduler;
use Cleanse\Feast\Classes\FeastSearchProvider;

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
            'Cleanse\Feast\Components\Demo' => 'cleanseFeastDemo',
        ];
    }

    public function boot()
    {
        Event::listen('offline.sitesearch.extend', function ($query) {
            return [new FeastSearchProvider('solo'), new FeastSearchProvider('party')];
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
