<?php

use App\Models\Office;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficesTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offices_tags', function (Blueprint $table) {
            $table->foreignIdFor(Office::class)->index()->constrained();
            $table->foreignIdFor(\App\Models\Tag::class)->index()->constrained();
            $table->unique(['office_id' , 'tag_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offices_tags');
    }
}
