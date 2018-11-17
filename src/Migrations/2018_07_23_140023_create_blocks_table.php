<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->increments('id');

            $table->string('type');
            $table->text('css')->nullable();

            $table->text('album')->nullable();

            $table->integer('media_id')->nullable()->unsigned();
            $table->foreign('media_id')->references('id')->on('media');

            $table->integer('page_id')->unsigned();
            $table->foreign('page_id')->references('id')->on('pages');

            $table->timestamps();
        });

        Schema::create('block_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('block_id')->unsigned();
            $table->string('locale')->index();

            $table->string('title')->nullable();
            $table->text('body')->nullable();

            $table->unique(['block_id','locale']);
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_translations');

        Schema::dropIfExists('blocks');
    }
}
