<?php

use App\Models\Location;
use App\Models\Project;
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
        Schema::create('project_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Project::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Location::class)->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_schedule');
    }
};
