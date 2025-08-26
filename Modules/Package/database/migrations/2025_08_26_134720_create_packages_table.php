<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Package\Enums\PackageStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('sender_id');
            $table->uuid('carrier_id')->nullable();
            $table->uuid('tracking_code')->unique();
            $table->enum('status', PackageStatus::values())->default(PackageStatus::CREATED->value);
            $table->string('origin_city');
            $table->string('origin_address');
            $table->string('destination_city');
            $table->string('destination_address');
            $table->unsignedInteger('weight_grams')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sender_id')->references('id')->on('users');
            $table->foreign('carrier_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
