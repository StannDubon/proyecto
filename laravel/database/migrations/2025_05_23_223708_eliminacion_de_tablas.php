<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints(); 

        Schema::dropIfExists('areas_doctores');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('inventario');

        Schema::enableForeignKeyConstraints(); 
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
