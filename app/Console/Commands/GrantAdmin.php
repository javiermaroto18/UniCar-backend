<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class GrantAdmin extends Command
{
    /**
     * Uso: php artisan admin:grant correo@ejemplo.com
     */
    protected $signature = 'admin:grant {email : Email del usuario que será administrador}';

    protected $description = 'Asigna el rol de administrador a un usuario por su email';

    public function handle(): int
    {
        $email = $this->argument('email');

        // Aseguramos que el rol exista (por si el seeder no se ha ejecutado)
        Role::firstOrCreate(['name' => 'admin']);

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No existe ningún usuario con el email: {$email}");

            return self::FAILURE;
        }

        if ($user->hasRole('admin')) {
            $this->info("El usuario {$user->name} ({$email}) ya era administrador.");

            return self::SUCCESS;
        }

        $user->assignRole('admin');
        $this->info("Rol de administrador asignado a {$user->name} ({$email}).");

        return self::SUCCESS;
    }
}
