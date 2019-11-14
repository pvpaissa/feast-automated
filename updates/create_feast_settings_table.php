<?php

namespace Cleanse\Feast\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateFeastSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('cleanse_feast_settings', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('mode');
            $table->integer('season')->default(1);
            $table->integer('take')->default(50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('cleanse_feast_settings');
    }
}
