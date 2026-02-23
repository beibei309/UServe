<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicePackagesTable extends Migration
{
    public function up()
    {
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->foreignId('student_service_id')->constrained('student_services')->onDelete('cascade'); // Foreign key
            $table->enum('package_type', ['basic', 'standard', 'premium']); // Package type
            $table->string('duration'); // e.g., '1 Hour', '2 Hours'
            $table->decimal('price', 8, 2); // Price for the package
            $table->text('description')->nullable(); // Description for the package
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_packages');
    }
}
