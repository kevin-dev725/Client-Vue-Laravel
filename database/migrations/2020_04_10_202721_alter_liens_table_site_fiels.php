<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLiensTableSiteFiels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('liens', function (Blueprint $table) {
            //
            foreign($table, 'user_id', 'id', 'users', 'cascade');
            $table->string('file_urls');
            $table->string('instrument_id', 250)->nullable();
            $table->string('site', 50)->nullable();
            $table->string('pdf_url')->nullable();
            $table->string('first_direct_name')->nullable();
            $table->string('first_indirect_name')->nullable();
            $table->date('record_date')->nullable();
            $table->string('doc_type')->nullable();
            $table->string('book_type')->nullable();
            $table->string('book_page')->nullable();
            $table->tinyInteger('row_in_db')->default(0);
            $table->string('grantor')->nullable();
            $table->string('grantee')->nullable();
            $table->string('case_number')->nullable();
            $table->boolean('is_binary')->default(0);
            $table->string('pdf')->nullable();
            $table->string('cross_party')->nullable();
            $table->string('record_fee')->nullable();
            $table->string('deed_tax')->nullable();
            $table->string('mortgage_tax')->nullable();
            $table->string('intangible_tax')->nullable();
            $table->string('status')->nullable();
            $table->unique(array('instrument_id', 'site','state','county', 'lienor'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('liens', function (Blueprint $table) {
            //+
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('file_urls');
            $table->dropColumn('instrument_id');
            $table->dropColumn('site');
            $table->dropColumn('first_direct_name');
            $table->dropColumn('first_indirect_name');
            $table->dropColumn('record_date');
            $table->dropColumn('doc_type');
            $table->dropColumn('book_type');
            $table->dropColumn('book_page');
            $table->dropColumn('row_in_db');
            $table->dropColumn('grantor');
            $table->dropColumn('grantee');
            $table->dropColumn('case_number');
            $table->dropColumn('is_binary');
            $table->dropColumn('pdf');
            $table->dropColumn('cross_party');
            $table->dropColumn('record_fee');
            $table->dropColumn('deed_tax');
            $table->dropColumn('mortgage_tax');
            $table->dropColumn('intangible_tax');
            $table->dropColumn('status');
        });
    }
}
