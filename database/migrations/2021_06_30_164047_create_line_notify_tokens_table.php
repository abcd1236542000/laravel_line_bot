<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineNotifyTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_notify_tokens', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('token', 255)->unique()->comment('token');
            $table->string('target_type', 64)->nullable(true)->comment('targetType');
            $table->string('target', 64)->nullable(true)->comment('target');
            $table->tinyInteger('enabled')->default(0)->comment('0 => 關閉, 1 => 啟用');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_notify_tokens');
    }
}
