<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Asigna el rol 'admin' a los usuarios cuyos emails estén en la variable
     * de entorno ADMIN_EMAILS (separados por comas).
     *
     * Pensado para producción (Render free, sin acceso a shell): se ejecuta
     * en cada despliegue desde el entrypoint y es idempotente.
     */
    public function run(): void
    {
        $emails = array_filter(array_map('trim', explode(',', (string) env('ADMIN_EMAILS'))));

        if (empty($emails)) {
            return; // No hay nada configurado: no hacemos nada.
        }

        Role::firstOrCreate(['name' => 'admin']);

        foreach ($emails as $email) {
            $user = User::where('email', $email)->first();

            if ($user && ! $user->hasRole('admin')) {
                $user->assignRole('admin');
            }
        }
    }
}
