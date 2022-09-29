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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->integer('gateway_id')->comment('ID que vem do gateway de pagamento');
            $table->foreignId('user_id')->constrained()->comment('Usuário que fez o deposito');
            $table->unsignedDecimal('value', 10, 2)->comment('valor que foi depositado');
            $table->enum('method', ['credit', 'bill', 'pix'])->comment('Metodo de pagamento, pix, credito ou boleto');
            $table->longText('result')->comment('Resultado retornado pelo gateway de pagamento');
            $table->enum('status', ['approved', 'in_process', 'rejected', 'pending'])->default('in_process')->comment('Status do deposito');
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
        Schema::dropIfExists('deposits');
    }
};
