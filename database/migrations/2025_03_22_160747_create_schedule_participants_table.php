<?php

use App\Models\ProjectMember;
use App\Models\ProjectSchedule;
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
        Schema::create('schedule_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProjectSchedule::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ProjectMember::class)->constrained()->cascadeOnDelete();
            $table->time('call_time')->nullable();
            $table->time('wrap_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_participants');
    }
};
