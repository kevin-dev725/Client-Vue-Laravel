<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');
            foreign($table, 'user_id', 'id', 'users', 'cascade');
            foreign($table, 'client_id', 'id', 'clients', 'cascade');
            $table->dateTimeTz('service_date');
            $table->unsignedTinyInteger('star_rating');
            $table->enum('payment_rating', ['No opinion', 'Thumbs up', 'Thumbs down'])->default('No opinion');
            $table->enum('character_rating', ['No opinion', 'Thumbs up', 'Thumbs down'])->default('No opinion');
            $table->enum('repeat_rating', ['No opinion', 'Thumbs up', 'Thumbs down'])->default('No opinion');
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('reviews');
    }
}
