<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exo_market_listings', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->index(); // Account JID
            $table->integer('char_id')->index();    // Character ID
            $table->string('char_name', 64);
            $table->bigInteger('sro_item_id');       // Original SRO _Items.ItemID
            $table->integer('ref_obj_id');          // SRO _Items.RefItemID / RefObjID
            $table->integer('plus_opt')->default(0); // Enhancement level (+plus)
            $table->longText('item_data_json');     // Full serialized item row attributes
            $table->bigInteger('price');
            $table->string('currency', 32);         // gold, silk, points
            $table->string('status', 32)->default('active')->index(); // active, sold, cancelled, claimed
            $table->dateTime('expires_at');
            $table->timestamps();
        });

        // Add web_points to users table if not exists
        if (!Schema::hasColumn('users', 'web_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->bigInteger('web_points')->default(0);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exo_market_listings');
        
        if (Schema::hasColumn('users', 'web_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('web_points');
            });
        }
    }
};
