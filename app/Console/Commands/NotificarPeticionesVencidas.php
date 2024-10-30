<?php

namespace App\Console\Commands;

use App\Models\Peticion;
use App\Models\User;
use App\Notifications\PeticionVencidaNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class NotificarPeticionesVencidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificar:peticiones-vencidas';
    protected $description = 'Notifica sobre las peticiones vencidas a los directores y líderes de área';

    public function handle()
    {
        $peticionesVencidas = Peticion::where('fecha_vencimiento', '<', now())->get();

        foreach ($peticionesVencidas as $peticion) {
            $liderArea = $peticion->funcionario->where('es_lider_area', true)->first();
            $directores = User::where('es_director', true)->get();

            // Notificar al líder de área
            Notification::send($liderArea, new PeticionVencidaNotification($peticion));

            // Notificar a los directores
            Notification::send($directores, new PeticionVencidaNotification($peticion));
        }

        $this->info('Notificaciones enviadas correctamente.');
    }
}
