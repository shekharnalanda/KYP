<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class KypBackupCommand extends Command
{
    protected $signature = 'kyp:backup';
    protected $description = 'Create a consistent timestamped backup of the KYP SQLite database';

    public function handle(): int
    {
        if (DB::getDriverName() !== 'sqlite') {
            $this->error('Automatic backup currently supports the production SQLite connection.');
            return self::FAILURE;
        }

        $source = (string) config('database.connections.sqlite.database');
        if (! is_file($source)) {
            $this->error('SQLite database file was not found.');
            return self::FAILURE;
        }

        $directory = storage_path('app/private/backups');
        File::ensureDirectoryExists($directory, 0750);
        $target = $directory.'/kyp-'.now()->format('Ymd-His').'.sqlite';
        $escaped = str_replace("'", "''", $target);

        DB::statement("VACUUM INTO '{$escaped}'");

        $this->info('Backup created: '.basename($target));
        $this->line('Size: '.number_format((int) filesize($target)).' bytes');
        $this->line('SHA-256: '.hash_file('sha256', $target));

        return self::SUCCESS;
    }
}
