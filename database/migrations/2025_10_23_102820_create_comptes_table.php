<?php

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
        Schema::create('comptes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_compte')->unique();
            $table->string('type_compte')->default('cheque'); // cheque, epargne
            $table->string('devise')->default('CFA');
            $table->enum('statut', ['actif', 'bloque', 'ferme'])->default('actif');
            $table->integer('version')->default(1);
            $table->foreignUuid('client_id')->constrained('clients')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comptes');
    }
};
