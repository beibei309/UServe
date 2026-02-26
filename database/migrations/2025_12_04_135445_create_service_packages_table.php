<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicePackagesTable extends Migration
{
    public function up()
    {
        Schema::create('h2u_service_packages', function (Blueprint $table) {
            $table->bigIncrements('hsp_id');  // Primary key
            $table->foreignId('hsp_student_service_id')->constrained('h2u_student_services', 'hss_id')->onDelete('cascade'); // Foreign key
            $table->enum('hsp_package_type', ['basic', 'standard', 'premium']); // Package type
            $table->string('hsp_duration'); // e.g., '1 Hour', '2 Hours'
            $table->decimal('hsp_price', 8, 2); // Price for the package
            $table->text('hsp_description')->nullable(); // Description for the package
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('h2u_service_packages');
    }
}
