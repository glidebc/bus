<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateCustomerTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Model::unguard();
        Schema::create('customer',function(Blueprint $table){
            $table->increments("id");
            $table->integer('agent_id')->unsigned()->nullable();
            $table->string('name',10);
            $table->string("tax_title")->nullable();
            $table->string("tax_num")->nullable();
            $table->string("address")->nullable();
            $table->string("contact")->nullable();
            $table->string("com_tel")->nullable();
            $table->string("com_fax")->nullable();
            $table->string("mobile")->nullable();
            $table->string("note")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('customer');
    }

}