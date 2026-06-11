<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exo_addon_configs', function (Blueprint $table) {
            $table->id();
            $table->string('addon_name', 50);       // e.g. 'market'
            $table->string('config_key', 100);       // e.g. 'marketplace.fee_percent'
            $table->text('config_value')->nullable(); // e.g. '5'
            $table->timestamps();

            $table->unique(['addon_name', 'config_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exo_addon_configs');
    }
};
