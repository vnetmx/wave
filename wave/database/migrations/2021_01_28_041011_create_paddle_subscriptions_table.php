<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaddleSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paddle_subscriptions', function(Blueprint $table)
		{
			$table->id();
			$table->unsignedBigInteger('subscription_id')->unique();
			$table->unsignedBigInteger('plan_id')->nullable();
			$table->unsignedBigInteger('user_id')->nullable();
			$table->string('status')->nullable();
			$table->string('update_url')->nullable();
			$table->string('cancel_url')->nullable();
			$table->dateTime('cancelled_at')->nullable();
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
        Schema::dropIfExists('paddle_subscriptions');
    }
}
