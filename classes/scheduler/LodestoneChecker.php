<?php

namespace Cleanse\Feast\Classes\Scheduler;

use GuzzleHttp\Client as GuzzleClient;

class LodestoneChecker
{
    public $mode;
    public $season;
    public $result;

    private $url;

    /**
     * LodestoneChecker constructor.
     * @param integer $season
     * @param string  $mode
     * @param bool    $result
     */
    public function __construct($mode, $season, $result = true)
    {
        $this->season = $season;
        $this->mode   = $mode;
        $this->result = $result;
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
        $this->getUrl();

        $client = new GuzzleClient;

        $response = $client->get($this->url, ['exceptions' => false]);

        return $response->getStatusCode();
    }

    private function getUrl()
    {
        if ($this->result) {
            if ($this->mode === 'party') {
                $this->url = 'https://na.finalfantasyxiv.com/lodestone/ranking/thefeast/team/result/'.$this->season.'/';
            } else {
                $this->url = 'https://na.finalfantasyxiv.com/lodestone/ranking/thefeast/result/'.$this->season.'/';
            }
        } else {
            $this->url = 'https://na.finalfantasyxiv.com/lodestone/ranking/thefeast/';
        }
    }
}
