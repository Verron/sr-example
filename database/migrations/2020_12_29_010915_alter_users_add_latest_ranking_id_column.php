<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class AlterUsersAddLatestRankingIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('latest_ranking_id')->after('id')->nullable();

            $table->foreign('latest_ranking_id')->references('id')->on('rankings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Artisan::call('patch:user-rankings', ['--rollback' => true]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_latest_ranking_id_foreign');
            $table->dropColumn('latest_ranking_id');
        });
    }
}
