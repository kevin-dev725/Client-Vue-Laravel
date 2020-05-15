<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFlaggedReviewsTableAddPhraseColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flagged_reviews', function (Blueprint $table) {
            $table->string('phrase');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flagged_reviews', function (Blueprint $table) {
            $table->dropColumn('phrase');
        });
    }
}
