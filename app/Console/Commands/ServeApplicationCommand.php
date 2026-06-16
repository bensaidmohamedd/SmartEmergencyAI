<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ServeApplicationCommand extends Command
{
    protected $signature = 'serve:app {--host=127.0.0.1} {--port=8000}';

    protected $description = 'Démarre le serveur de développement avec la configuration upload PHP du projet';

    public function handle(): int
    {
        require base_path('bootstrap/environment.php');

        $host = $this->option('host');
        $port = $this->option('port');

        $this->info("Serveur Smart Emergency AI sur http://{$host}:{$port}");
        $this->info('Dossier temporaire : storage/tmp | Upload max : 20 Mo');

        $process = new Process([
            PHP_BINARY,
            '-S',
            "{$host}:{$port}",
            '-t',
            public_path(),
            base_path('server.php'),
        ], base_path());

        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        return self::SUCCESS;
    }
}
