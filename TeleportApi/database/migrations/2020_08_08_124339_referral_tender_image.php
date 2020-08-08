<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReferralTenderImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('referral_tenders', function (Blueprint  $table) {
            $table->string('image_ru')->nullable();
            $table->string('image_uz')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('referral_tenders', function (Blueprint  $table) {
            $table->dropColumn('image_ru');
            $table->dropColumn('image_uz');
        });
    }
}
