<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallApplicationCommand extends Command
{
    protected $signature = 'app:install {--fresh : Réinitialiser la base de données}';

    protected $description = 'Prépare Smart Emergency AI (dossiers, clé, migrations, seeders, storage)';

    /** @var array<string, string> */
    private const REQUIRED_EXTENSIONS = [
        'pdo' => 'PDO',
        'mbstring' => 'Mbstring',
        'openssl' => 'OpenSSL',
        'fileinfo' => 'Fileinfo',
        'curl' => 'cURL',
        'json' => 'JSON',
        'tokenizer' => 'Tokenizer',
        'xml' => 'XML',
    ];

    public function handle(): int
    {
        $this->components->info('Smart Emergency AI — Installation');

        if (! $this->checkExtensions()) {
            return self::FAILURE;
        }

        $this->ensureDirectories();

        if (config('database.default') === 'sqlite') {
            $this->ensureSqliteDatabase();
        }

        if (empty(config('app.key'))) {
            Artisan::call('key:generate', ['--force' => true]);
            $this->components->info('Clé application générée.');
        }

        $migrateOptions = ['--force' => true];
        if ($this->option('fresh')) {
            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        } else {
            Artisan::call('migrate', $migrateOptions);
            Artisan::call('db:seed', ['--force' => true]);
        }
        $this->components->info('Base de données migrée et seedée.');

        if (! File::exists(public_path('storage'))) {
            Artisan::call('storage:link');
            $this->components->info('Lien symbolique storage créé.');
        }

        Artisan::call('optimize:clear');
        $this->newLine();
        $this->components->info('Installation terminée.');
        $this->line('  Citoyen : ben.said@email.ne / password');
        $this->line('  Admin   : admin@smartemergency.ne / password');
        $this->line('  Démarrer : serve.bat  ou  php artisan serve:app');

        return self::SUCCESS;
    }

    private function checkExtensions(): bool
    {
        $missing = [];
        foreach (self::REQUIRED_EXTENSIONS as $ext => $label) {
            if (! extension_loaded($ext)) {
                $missing[] = $label." ({$ext})";
            }
        }

        if ($missing) {
            $this->components->error('Extensions PHP manquantes : '.implode(', ', $missing));
            $this->line('Activez-les dans php.ini puis relancez l\'installation.');

            return false;
        }

        return true;
    }

    private function ensureDirectories(): void
    {
        $dirs = [
            storage_path('tmp'),
            storage_path('app/public/signalements/photos'),
            storage_path('app/public/signalements/videos'),
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
        ];

        foreach ($dirs as $dir) {
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
        }
    }

    private function ensureSqliteDatabase(): void
    {
        $path = database_path('database.sqlite');
        if (! File::exists($path)) {
            File::put($path, '');
            $this->components->info('Fichier SQLite créé : database/database.sqlite');
        }
    }
}
