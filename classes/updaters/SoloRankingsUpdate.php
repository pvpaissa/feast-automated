<?php

namespace Cleanse\Feast\Classes\Updaters;

use Queue;

use Cleanse\PvPaissa\Classes\UpdateOrCreatePlayer;
use Cleanse\PvPaissa\Classes\HelperRankSort;
use Cleanse\Feast\Classes\Solo\SoloCrawler;
use Cleanse\Feast\Models\FeastSolo;
use Cleanse\Feast\Models\FeastSoloDaily;

class SoloRankingsUpdate
{
    public function updateDay($data)
    {
        $list = new SoloCrawler($data['season'], $data['datacenter'], $data['tier'], $data['day'], $data['result']);

        $players = $list->crawl();

        if (empty($players)) {
            return;
        }

        foreach ($players as $player) {
            $solo = new UpdateOrCreatePlayer('solo', $player);
            $solo->update($data['season']);
        }
    }

    public function dailyPlayerSort($data)
    {
        $players = FeastSoloDaily::where('day', $data['day'])->get(['id', 'rating']);
        $players = $players->toArray();

        if (!empty($players)) {
            $sort = new HelperRankSort;
            $updatedPlayers = $sort->sortRanks($players, 'rating');

            FeastSoloDaily::where('day', $data['day'])
                ->orderBy('rating', 'desc')
                ->chunk(200, function ($players) use ($updatedPlayers) {
                    foreach ($players as $player) {
                        if (isset($updatedPlayers[$player->id]) && !empty($updatedPlayers[$player->id])) {
                            $player->rank = $updatedPlayers[$player->id];

                            $player->save();
                        }
                    }
                });
        }

        $typeSeason = ['season' => $data['season'], 'type' => 'solo'];
        Queue::push('\Cleanse\Feast\Classes\Jobs\QueueOutdatedRankings', $typeSeason);
        Queue::push('\Cleanse\Feast\Classes\Jobs\QueueSeasonalRankings', $typeSeason);
    }

    public function seasonPlayerSort($data)
    {
        $players = FeastSolo::where('season', $data['season'])->get(['id', 'rating']);
        $players = $players->toArray();

        if (!empty($players)) {
            $sort = new HelperRankSort;
            $updatedPlayers = $sort->sortRanks($players, 'rating');

            FeastSolo::where('season', $data['season'])
                ->orderBy('rating', 'desc')
                ->chunk(200, function ($players) use ($updatedPlayers) {
                    foreach ($players as $player) {
                        if (isset($updatedPlayers[$player->id]) && !empty($updatedPlayers[$player->id])) {
                            $player->rank = $updatedPlayers[$player->id];

                            $player->save();
                        }
                    }
                });
        }
    }
}
