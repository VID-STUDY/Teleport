<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SupportTextImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('support_ru')->nullable();
            $table->text('support_uz')->nullable();
            $table->string('support_image_ru')->nullable();
            $table->string('support_image_uz')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('support_ru');
            $table->dropColumn('support_uz');
            $table->dropColumn('support_image_ru');
            $table->dropColumn('support_image_uz');
        });
    }
}
