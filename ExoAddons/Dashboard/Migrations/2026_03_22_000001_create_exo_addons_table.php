<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exo_addons', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->string('name', 128);
            $table->string('version', 20)->default('1.0.0');
            $table->string('provider', 255);
            $table->boolean('enabled')->default(true);
            $table->timestamp('installed_at')->useCurrent();
        });

        // exo_addon_configs already exists from Dashboard — ensure it does
        if (!Schema::hasTable('exo_addon_configs')) {
            Schema::create('exo_addon_configs', function (Blueprint $table) {
                $table->id();
                $table->string('addon_name', 64);
                $table->string('config_key', 128);
                $table->text('config_value')->nullable();
                $table->timestamps();
                $table->unique(['addon_name', 'config_key']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exo_addons');
    }
};
