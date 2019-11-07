<?php

namespace Cleanse\Feast\Classes\Scheduler;

use GuzzleHttp\Client as GuzzleClient;

class LodestoneChecker
{
    public $mode;
    public $season;

    private $url;

    /**
     * LodestoneChecker constructor.
     * @param integer $season
     * @param string  $mode
     */
    public function __construct($mode, $season)
    {
        $this->season = $season;
        $this->mode   = $mode;
    }

    /**
     * @return bool
     */
    public function checkResults()
    {
        $resultStatus = $this->guzzleResults();

        if ($resultStatus == '200') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    private function guzzleResults()
    {
        if ($this->mode === 'party') {
            $this->url = 'https://na.finalfantasyxiv.com/lodestone/ranking/thefeast/team/result/'.$this->season.'/';
        } else {
            $this->url = 'https://na.finalfantasyxiv.com/lodestone/ranking/thefeast/result/'.$this->season.'/';
        }

        $client = new GuzzleClient;

        $response = $client->get($this->url, ['exceptions' => false]);

        return $response->getStatusCode();
    }
}
