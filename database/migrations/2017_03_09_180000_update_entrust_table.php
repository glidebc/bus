<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class UpdateEntrustTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Model::unguard();
        if (Schema::hasTable('entrust')) {
            Schema::table('entrust', function (Blueprint $table) {
                $table->integer('customer_id')->change();
                $table->string('name', 30)->change();
                $table->integer('owner_user')->change();
                // $table->engine = 'InnoDB';
                // $table->integer('agent_id')->unsigned()->nullable()->change();
                // $table->dropForeign(['agent_id']);
                // $table->dropColumn('agent_id');
                // $table->foreign('agent_id')->references('id')->on('agent')->onDelete('cascade')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('customer', function (Blueprint $table) {
        //     $table->integer('agent_id')->unsigned()->nullable()->change();
        // });
    }

}