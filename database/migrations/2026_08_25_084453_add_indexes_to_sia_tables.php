<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sia_tables', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sia_tables', function (Blueprint $table) {
            //
        });
    }
};
//ERROR: The migration file `2026_08_25_084453_add_indexes_to_sia_tables.php` is currently empty and does not contain any logic to add indexes to the `sia_tables`. You may want to implement the necessary index creation logic in the `up()` method and the corresponding index removal logic in the `down()` method.
