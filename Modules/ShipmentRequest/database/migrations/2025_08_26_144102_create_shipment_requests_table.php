<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\ShipmentRequest\Enums\ShipmentRequestStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipment_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('package_id')->constrained('packages');
            $table->foreignUuid('carrier_id')->constrained('users');

            $table->enum('status', ShipmentRequestStatus::values())->default(ShipmentRequestStatus::PENDING->value);
            $table->string('reject_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['package_id', 'carrier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_requests');
    }
};
