<?php

namespace Cleanse\Feast\Models;

use Model;

class FeastSettings extends Model
{
    protected $table = 'cleanse_feast_settings';

    protected $fillable = ['mode', 'season'];
}
