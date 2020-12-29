<?php


namespace App\Console\Commands\Patch;

use App\Console\Commands\Database\MigrationInteractions;

abstract class Command extends \Illuminate\Console\Command
{
    use MigrationInteractions;
}
