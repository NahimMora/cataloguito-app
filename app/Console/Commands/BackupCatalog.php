<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use ZipArchive;

class BackupCatalog extends Command
{
    protected $signature = 'catalog:backup {--output= : Archivo ZIP fuera del directorio público}';

    protected $description = 'Crear un respaldo privado de base de datos e imágenes procesadas';

    public function handle(): int
    {
        if (config('database.default') !== 'mysql') {
            $this->error('Este respaldo requiere MySQL/MariaDB.');

            return self::FAILURE;
        }
        $local = base_path('.tools/mariadb-11.8.9-winx64/bin/mariadb-dump.exe');
        $binary = config('vitrina.dump_binary') ?: (is_file($local) ? $local : ((new ExecutableFinder)->find('mariadb-dump') ?: (new ExecutableFinder)->find('mysqldump')));
        if (! $binary) {
            $this->error('Configurá DB_DUMP_BINARY con mariadb-dump o mysqldump.');

            return self::FAILURE;
        }
        $folder = storage_path('app/private/backups');
        if (! is_dir($folder)) {
            mkdir($folder, 0700, true);
        }
        $output = $this->option('output') ?: $folder.'/vitrina-'.now()->format('Ymd-His').'.zip';
        $parent = realpath(dirname($output));
        if ($parent === false) {
            $this->error('La carpeta de destino no existe.');

            return self::FAILURE;
        }
        $output = $parent.DIRECTORY_SEPARATOR.basename($output);
        $normalized = strtolower(str_replace('\\', '/', $output));
        $public = strtolower(str_replace('\\', '/', realpath(public_path()))).'/';
        if (str_starts_with($normalized, $public)) {
            $this->error('El respaldo debe guardarse fuera de public/.');

            return self::FAILURE;
        }
        if (file_exists($output)) {
            $this->error('Ese archivo ya existe. Elegí un nombre nuevo.');

            return self::FAILURE;
        }
        $temp = tempnam($folder, 'db-');
        $settings = tempnam($folder, 'cnf-');
        $handle = fopen($temp, 'wb');
        $db = config('database.connections.mysql');
        $escape = fn ($v) => str_replace(['\\', '"', "\r", "\n"], ['\\\\', '\\"', '', ''], (string) $v);
        file_put_contents($settings, "[client]\nhost=\"".$escape($db['host'])."\"\nport=".(int) $db['port']."\nuser=\"".$escape($db['username'])."\"\npassword=\"".$escape($db['password'])."\"\n");
        @chmod($settings, 0600);
        @chmod($temp, 0600);
        try {
            $process = new Process([$binary, '--defaults-extra-file='.$settings, '--single-transaction', '--quick', '--skip-lock-tables', '--skip-comments', '--hex-blob', $db['database']]);
            $process->setTimeout(180);
            $process->run(function ($type, $data) use ($handle) {
                if ($type === Process::OUT) {
                    fwrite($handle, $data);
                }
            });
            fclose($handle);
            $handle = null;
            if (! $process->isSuccessful()) {
                throw new \RuntimeException('Falló el volcado SQL. Revisá DB_DUMP_BINARY y los permisos del usuario de base de datos.');
            }
            $zip = new ZipArchive;
            if ($zip->open($output, ZipArchive::CREATE) !== true) {
                throw new \RuntimeException('No se pudo crear el ZIP privado.');
            }
            $zip->addFile($temp, 'database.sql');
            $count = 0;
            $hashes = [];
            foreach (Media::query()->lazy(100) as $media) {
                foreach ($media->variants as $path) {
                    if (! Storage::disk('local')->exists($path)) {
                        throw new \RuntimeException('Falta una imagen referenciada: '.$media->id);
                    }
                    $zip->addFile(Storage::disk('local')->path($path), 'images/'.$path);
                    $hashes['images/'.$path] = hash_file('sha256', Storage::disk('local')->path($path));
                    $count++;
                }
            }
            $zip->addFromString('manifest.json', json_encode(['format' => 1, 'created_at' => now()->toAtomString(), 'database_driver' => 'mysql', 'images' => $count, 'file_hashes' => $hashes, 'sql_sha256' => hash_file('sha256', $temp)], JSON_PRETTY_PRINT));
            if (! $zip->close()) {
                throw new \RuntimeException('No se pudo cerrar el respaldo.');
            } @chmod($output, 0600);
            $this->info('Respaldo creado: '.$output);
            $this->line('Imágenes: '.$count.' · Transferilo a un destino externo cifrado. No incluye .env ni APP_KEY.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            if (file_exists($output)) {
                unlink($output);
            } $this->error($e->getMessage());

            return self::FAILURE;
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            } if (is_file($settings)) {
                unlink($settings);
            } if (is_file($temp)) {
                unlink($temp);
            }
        }
    }
}
