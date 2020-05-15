<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlaggedReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flagged_reviews', function (Blueprint $table) {
            $table->increments('id');
            foreign($table, 'review_id', 'id', 'reviews', 'cascade');
            $table->boolean('is_resolved')->default(false)->index();
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
        Schema::dropIfExists('flagged_reviews');
    }
}
