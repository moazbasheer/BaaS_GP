<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routes', function(Blueprint $table) {
            $table->string('name')->default('route');
            $table->decimal('source_longitude', 25, 20)->default('0.00');
            $table->decimal('source_latitude', 25, 20)->default('0.00');
            $table->decimal('destination_longitude', 25, 20)->default('0.00');
            $table->decimal('destination_latitude', 25, 20)->default('0.00');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
