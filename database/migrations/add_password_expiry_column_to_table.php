<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table(config('password-expiry.table_name'), function (Blueprint $table) {
            $table->dateTime(config('password-expiry.column_name'))->nullable();
        });
    }

    public function down()
    {
        Schema::table(config('password-expiry.table_name'), function (Blueprint $table) {
            $table->dropColumn(config('password-expiry.column_name'));
        });
    }
};
