<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain', 255);
            $table->unsignedInteger('parent_domain_id')->nullable()->default(null);
            $table->foreign('parent_domain_id')
                ->references('id')
                ->on('domain')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('ip', 20)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domain');
    }
}
