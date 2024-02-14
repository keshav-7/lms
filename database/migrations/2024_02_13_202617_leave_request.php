<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeaveRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_request', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id');
            $table->enum('leave_type', ['PL','CL','SL']);
            $table->date('leave_date');
            $table->date('request_date');
            $table->string('remarks');
            $table->enum('status', ['0','1','3'])->default('0')->comment('0 represents leave request is pending, 1 means approved, 3 means rejected');
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
