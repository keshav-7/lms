<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeaveMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_master', function (Blueprint $table) {
            $table->increments('id');
            $table->string('staff_id');
            $table->float('pl')->default(0);
            $table->float('cl')->default(0);
            $table->float('sl')->default(0);
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
