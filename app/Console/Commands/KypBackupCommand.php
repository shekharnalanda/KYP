<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use SQLite3;
use Throwable;

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
        if (! is_file($source) || ! class_exists(SQLite3::class)) {
            $this->error('SQLite database file or backup extension is unavailable.');
            return self::FAILURE;
        }

        $directory = storage_path('app/private/backups');
        File::ensureDirectoryExists($directory, 0750);
        $target = $directory.'/kyp-'.now()->format('Ymd-His').'.sqlite';

        try {
            $sourceDb = new SQLite3($source, SQLITE3_OPEN_READONLY);
            $backupDb = new SQLite3($target, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
            $completed = $sourceDb->backup($backupDb);
            $backupDb->close();
            $sourceDb->close();

            if (! $completed || ! is_file($target) || filesize($target) === 0) {
                throw new \RuntimeException('SQLite backup did not complete.');
            }
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $this->info('Backup created: '.basename($target));
        $this->line('Size: '.number_format((int) filesize($target)).' bytes');
        $this->line('SHA-256: '.hash_file('sha256', $target));

        return self::SUCCESS;
    }
}
