<?php

namespace App\Console\Commands\Patch;

use Illuminate\Support\Facades\DB;

class UserRankings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patch:user-rankings
        {--R|rollback : Rollback Data Changes}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Patch database and migrate ranking data to new rankings table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->ensureMigrationHasRan('2020_12_29_004719_create_rankings_table');
        $this->ensureMigrationHasRan('2020_12_29_010915_alter_users_add_latest_ranking_id_column');
        $this->ensureMigrationHasNotRan('2020_12_29_023404_alter_users_drop_ranking_column');

        // Use most performant way to modify since this will be ran with migrations

        if ($this->option('rollback') === true) {
            $this->line("Rolling back patch: User Ranks");
            // Seed latest rank into users table
            DB::update("
                update users u
                left join (
                    select * from rankings r where r.id = (select max(id) from rankings r2 where r2.user_id = r.user_id)
                ) r on u.id = r.user_id
                set u.ranking = r.ranking
                where r.id is not null;
            ");

            $this->line("<info>Rolled Back:</info> User Ranks");
        } else {
            $this->line("Patching: User Ranks");

            // Seed ranking data
            DB::insert("
                insert into rankings (user_id, ranking, created_at)
                select users.id as user_id, users.ranking, now() as creted_at from users;
            ");

            // Seed latest rank
            DB::update("
                update users u join rankings r on u.id = r.user_id set u.latest_ranking_id = r.id where u.ranking is not null;
            ");

            // Clear old ranking column
//            DB::update("update users u set u.ranking = null where u.ranking is not null");

            $this->line("<info>Patched:</info> User Ranks");
        }

    }
}
