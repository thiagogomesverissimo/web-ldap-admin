<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Uspdev\Replicado\Pessoa;
use Uspdev\Replicado\Graduacao;
use Uspdev\Replicado\Posgraduacao;

use App\Ldap\User as LdapUser;
use App\Ldap\Group as LdapGroup;
use Adldap\Laravel\Facades\Adldap;

class SincronizaReplicado implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $unidade;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->unidade = env('REPLICADO_UNIDADE');  
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Sicroniza docentes
        $this->sync(Pessoa::docentes($this->unidade));

        // Sicroniza funcionári@s
        //$this->sync(Pessoa::servidores($this->unidade));

        // Sicroniza estagiarios
        //dd(Pessoa::docentes($this->unidade));
        //$this->sync(Pessoa::estagiarios($this->unidade));

        // Sicroniza designados
        //$this->sync(Pessoa::designados($this->unidade),'designados');
    }

    public function sync($pessoas)
    {
        if($pessoas){
            foreach($pessoas as $pessoa) {
                $grupos = Pessoa::vinculosSiglas($pessoa['codpes'],$this->unidade);
                LdapUser::createOrUpdate($pessoa['codpes'], [
                    'nome' => $pessoa['nompes'],
                    'email' => $pessoa['codema']
                ],
                $grupos);
            }
        }
    }
}
