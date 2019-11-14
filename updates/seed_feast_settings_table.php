<?php

namespace Cleanse\Feast\Updates;

use Seeder;
use Cleanse\Feast\Models\FeastSettings;

class SeedFeastSettingsTable extends Seeder
{
    public function run()
    {
        $solo = FeastSettings::create([
            'mode'   => 'solo',
            'season' => 1,
            'take' => 50
        ]);

        $party = FeastSettings::create([
            'mode'   => 'party',
            'season' => 1,
            'take' => 50
        ]);
    }
}
