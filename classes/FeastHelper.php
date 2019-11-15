<?php

namespace Cleanse\Feast\Classes;

class FeastHelper
{
    public $tiers = [5, 4, 3, 2, 1];

    public $datacenters = [
        'Aether',
        'Chaos',
        'Crystal',
        'Elemental',
        'Gaia',
        'Light',
        'Mana',
        'Primal'
    ];

    public function yearDay()
    {
        return date("Yz");
    }

    public function classifyString($string)
    {
        $classify = str_replace('-', ' ', $string);
        $classify = ucwords($classify);

        return str_replace(' ', '', $classify);
    }
}
