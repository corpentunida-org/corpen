<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corr_plantillas', function (Blueprint $table) {

            $table->id();

            $table->string('nombre_plantilla', 150);

            // HTML largo → usar longText
            $table->longText('html_base');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corr_plantillas');
    }
};
