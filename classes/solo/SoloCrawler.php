<?php

namespace Cleanse\Feast\Classes\Solo;

use Config;
use Log;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;

class SoloCrawler
{
    public $players = [];
    public $season;
    public $datacenter;
    public $tier;
    public $day;

    private $result;
    private $url;

    /**
     * SoloCrawler constructor.
     * @param integer $season
     * @param string $datacenter
     * @param integer $tier
     * @param string $day
     * @param bool $result
     */
    public function __construct($season, $datacenter, $tier, $day, $result = false)
    {
        $this->season = $season;
        $this->datacenter = $datacenter;
        $this->tier = $tier;
        $this->day = $day;
        $this->result = $result;

        if ($this->result) {
            $this->url = 'https://na.finalfantasyxiv.com/lodestone/ranking/thefeast/result/'.$this->season.'/';
        } else {
            $this->url = 'https://na.finalfantasyxiv.com/lodestone/ranking/thefeast/';
        }
    }

    public function crawl()
    {
        $dataCenterPlayers = $this->guzzle();

        $crawler = new Crawler($dataCenterPlayers);

        //If this tier has no players
        if (!$crawler->filterXPath('//*[@id="ranking"]/div[3]/div/div[2]/article/table/tbody/tr')->count()) {
            Log::info('The DOM crawler found no data for solo queue: ' . $this->datacenter . ' ' . $this->day);
            return;
        }

        $crawler->filterXPath('//*[@id="ranking"]/div[3]/div/div[2]/article/table/tbody/tr')
            ->each(function (Crawler $node) {
                $player = [];

                $segments = explode('/', rtrim($node->attr('data-href'), '/'));
                $characterId = end($segments);

                $player['character'] = $characterId;
                $player['name'] = $node->filterXPath('//td[4]/div/h3')->text();
                $player['data_center'] = $this->datacenter;
                $player['server'] = $node->filterXPath('//td[4]/span')->text();
                $player['wins'] = 0;
                $player['matches'] = 0;
                $player['rating'] = $node->filterXPath('//td[5]/p[1]')->text();
                $player['change'] = $node->filterXPath('//td[5]/p[2]')->count() ? $node->filterXPath('//td[5]/p[2]')->text() : '0';
                $player['percent'] = 0;
                $player['avatar'] = $node->filterXPath('//td[3]/div/img')->attr('src');
                $player['season'] = $this->season;
                $player['day'] = $this->day;

                $this->players[] = $player;
            });

        return $this->players;
    }

    private function guzzle()
    {
        //Game Mode - note 'party' is legacy
        $urlVars = '?solo_party=solo';

        //Datacenter
        $urlVars .= '&dcgroup=' . $this->datacenter;

        //"tier", aka Diamond = 5, Platinum = 4, etc
        $urlVars .= '&rank_type=' . $this->tier;

        $client = new GuzzleClient;
        $res = $client->get($this->url . $urlVars);

        return $res->getBody()->getContents();
    }
}
