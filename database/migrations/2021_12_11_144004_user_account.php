<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('user_account', function (Blueprint $table) {

        $table->increments('auto_id');
        $table->string('staff_id');
        $table->string('username');
        $table->string('password');
        $table->enum('user_type', ['0','1']);
        $table->string('account_type');

      });

      // Insert some stuff
      DB::table('user_account')->insert(
         array(
             'staff_id' => "00001",
             'username' => "admin",
             'password' => "admin123",
             'account_type' => "admin",
             'user_type'  => '0',
         )
      );

      DB::table('user_account')->insert(
        array(
            'staff_id' => "00002",
            'username' => "staff",
            'password' => "staff123",
            'account_type' => "admin",
            'user_type'  => '1',
        )
     );      

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
