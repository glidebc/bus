<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class UpdatePublishTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Model::unguard();
        if (Schema::hasTable('publish')) {
            Schema::table('publish', function (Blueprint $table) {
                $table->integer('entrust_id')->change();
                $table->string('date', 8)->change();
                $table->integer('publish_position_id')->change();
                $table->integer('status')->nullable(false)->unsigned()->default(0)->change();
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