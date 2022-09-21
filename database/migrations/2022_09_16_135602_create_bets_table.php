<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->comment('usuário que fez a aposta');
            $table->integer('match_id')->comment('id da partida');
            $table->integer('home_id')->comment('id do time da casa');
            $table->integer('away_id')->comment('id do time visitante');
            $table->unsignedDecimal('odd', 8, 2)->comment('odd da aposta');
            $table->integer('bet')->comment('id da equipe apostada (-1 para empate)');
            $table->unsignedDecimal('bet_value', 10, 2)->comment('valor que foi apostado');
            $table->integer('winning_tem')->nullable()->comment('id da equipe que ganhou (-1 para empate)');
            $table->enum('status', ['opened', 'processed'])->default('opened')->comment('status da aposta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bets');
    }
};
