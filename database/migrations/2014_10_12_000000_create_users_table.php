<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('email')->unique();
            $table->string('username')->unique();

            // Keep column name as "password" so Laravel Auth works easily
            $table->string('password');

            $table->string('fname');
            $table->string('mname')->nullable();
            $table->string('lname');

            $table->string('gender')->nullable();
            $table->date('bday')->nullable();

            $table->enum('role', ['user', 'doctor'])->default('user');
            $table->enum('doctor_status', ['none', 'pending', 'approved', 'rejected'])->default('none');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
