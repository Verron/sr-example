<?php


namespace App\Console\Commands\Database;


use Illuminate\Support\Facades\DB;

trait MigrationInteractions
{
    /**
     * @param string $table
     */
    protected function ensureMigrationHasRan(string $table): void
    {
        $migration = $this->getMigration($table);

        if (count($migration) === 0) {
            $this->warn("This command cannot be ran before this migration: $table");
            exit;
        }
    }

    protected function ensureMigrationHasNotRan(string $table): void
    {
        $migration = $this->getMigration($table);

        if (count($migration) === 1) {
            $this->warn("This command cannot be ran after this migration: $table");
            exit;
        }
    }

    /**
     * @param string $table
     * @return array
     */
    protected function getMigration(string $table): array
    {
        return DB::select("select * from migrations where migration = :table;", compact('table'));
    }
}
