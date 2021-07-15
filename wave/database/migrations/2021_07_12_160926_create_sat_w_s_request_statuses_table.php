<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSatWSRequestStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_ws_request_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->unique();
            $table->string('status')->index()->nullable();
            $table->string('downloadType')->nullable();
            $table->string('requestType')->nullable();
            $table->dateTime('from')->nullable();
            $table->dateTime('to')->nullable();
            $table->string('package_ids')->nullable();
            $table->string('file')->nullable();
            $table->string('message')->nullable();
            $table->boolean('imported')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sat_ws_request_statuses');
    }
}
