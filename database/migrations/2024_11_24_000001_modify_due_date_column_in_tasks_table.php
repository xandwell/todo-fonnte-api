<?php

// database/migrations/2024_11_24_000001_modify_due_date_column_in_tasks_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('reminder'); // Remove unused reminder column
            $table->dateTime('due_date')->nullable()->change(); // Change due_date to dateTime
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->text('reminder')->nullable();
            $table->date('due_date')->nullable()->change();
        });
    }
};

