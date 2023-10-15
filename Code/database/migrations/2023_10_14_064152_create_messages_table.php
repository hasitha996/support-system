<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('support_ticket_id');  
            $table->text('message');
            $table->unsignedBigInteger('user_id');  
            $table->timestamps();
            $table->foreign('support_ticket_id')->references('id')->on('support_tickets');
            $table->foreign('user_id')->references('id')->on('users');  
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
