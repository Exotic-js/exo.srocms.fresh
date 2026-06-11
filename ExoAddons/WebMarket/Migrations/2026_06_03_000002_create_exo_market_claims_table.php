<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exo_market_claims', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->index(); // Account JID entitled to claim
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->integer('ref_obj_id');          // RefObjID
            $table->integer('plus_opt')->default(0);
            $table->longText('item_data_json');     // Full attributes to restore SRO _Items row
            $table->string('type', 32);             // purchase, return
            $table->string('status', 32)->default('pending')->index(); // pending, claimed
            $table->integer('claimed_char_id')->nullable(); // Character JID who claimed
            $table->dateTime('claimed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exo_market_claims');
    }
};
