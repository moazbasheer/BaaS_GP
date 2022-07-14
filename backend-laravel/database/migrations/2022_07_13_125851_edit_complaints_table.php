<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('complaints', function(Blueprint $table) {
            $table->dropForeign(['complaints_passenger_id_foreign', 'complaints_client_id_foreign']);
            $table->dropColumn(['passenger_id', 'client_id']);
            $table->biginteger('passenger_id')->unsigned()->nullable();
            $table->foreign('passenger_id')->references('id')->on('passengers')->onDelete('cascade');
            $table->biginteger('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
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
