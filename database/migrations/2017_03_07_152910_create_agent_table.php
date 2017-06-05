<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateAgentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Model::unguard();
        if (Schema::hasTable('agent')) {
            Schema::table('agent', function (Blueprint $table) {
                $table->string('name',10)->change();// change() tells the Schema builder that we are altering a table
            });
        } else {
            Schema::create('agent',function(Blueprint $table){
                $table->increments("id");
                $table->string("name");
                // $table->string("name",10)->change();
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
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        //Schema::drop('agent');
    }

}