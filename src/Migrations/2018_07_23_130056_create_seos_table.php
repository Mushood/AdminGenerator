<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('viewport')->default('width=device-width, initial-scale=1');
            $table->boolean('no_index')->default(false);

            $table->string('og_sitename')->nullable();
            $table->string('og_type')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_url')->nullable();

            $table->timestamps();
        });

        Schema::create('seo_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seo_id')->unsigned();
            $table->string('locale')->index();

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();

            $table->unique(['seo_id','locale']);
            $table->foreign('seo_id')->references('id')->on('seos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_translations');

        Schema::dropIfExists('seos');
    }
}
