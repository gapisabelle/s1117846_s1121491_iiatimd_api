<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->integer('filmid');
            $table->foreignId('user1')->constrained('users')->onDelete('cascade');
            $table->foreignId('user2')->constrained('users')->onDelete('cascade');
            $table->string('chat_id');
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
    	Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['user1']);
            $table->dropForeign(['user2']);
        });
        Schema::dropIfExists('matches');
    }
}
