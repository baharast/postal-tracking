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

            $table->foreignUuid('sender_id')->constrained('users');
            $table->foreignUuid('carrier_id')->nullable()->constrained('users');

            $table->uuid('tracking_code')->unique();
            $table->enum('status', PackageStatus::values())->default(PackageStatus::CREATED->value);
            $table->string('origin_city');
            $table->string('origin_address');
            $table->string('destination_city');
            $table->string('destination_address');
            $table->unsignedInteger('weight_grams')->default(0);

            $table->timestamps();
            $table->softDeletes();
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
