<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class PersonalCargar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'personal:cargar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lee un documento en excel y carga los usuarios al sistema';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->info("Cargando usuarios...");

        \DB::transaction(function() {
            $data = \CSVSeeder::csv_to_array('/home/asilva/Escritorio/DBusuarios.csv');
            //dd($data);
            array_map(function ($row){

                $user = User::where('usuarioRUN', $row["COD. USUARIO" ])->first();
                if($user){
                    $this->info("usuario: ".$row["COD. USUARIO"]." actualizando datos ");
                }else{
                    if($row["COMUNA"]==''){
                        $this->info("usuario: ".$row["COD. USUARIO"]." no tiene una comuna asignada, no se creara");
                        return;
                    }
                    $this->info("usuario: ".$row["COD. USUARIO"]." creando...");
                    $user = new User();
                    $user->usuarioRUN= trim($row["COD. USUARIO"]);
                }
                
                $user->nombre1 = trim(ucfirst(strtolower($row["NOMBRE (1)"])));
                $user->nombre2 = trim(ucfirst(strtolower($row["NOMBRE (2)"])));
                $user->apellidoPaterno = trim(ucfirst(strtolower($row["APELLIDO PATERNO"])));
                $user->apellidoMaterno = trim(ucfirst(strtolower($row["APELLIDO MATERNO"])));
                $user->usuarioDV = trim($row["D.V."]);
                $user->email = trim($row["CORREO CONTACTO "]);
                $user->emailPersonal = trim($row["CORREO PERSONAL"]);
                $user->direccion = trim($row["DIRECCION"]);
                if($row["COMUNA"]!='')
                    $user->cutComuna = trim($row["COMUNA"]);
                $user->telefono = $row["FONO"];
                $user->telefonoEmergencia = $row["FONO EMERGENCIA"];
                $user->fechaNacimiento =  $row["AÑO NAC."]."-".$row["MES NAC."]."-".$row["DIA NAC."];
                $user->tipoContrato =  $row["TIPO CONTRATO"];
                $fechaContrato = $row["INICIO CONTRATO"];
                $user->fechaInicioContrato = implode('-', array_reverse( explode('-', $fechaContrato) ));
                $fechaAntecedentes = $row["FECHA CERT. ANTECEDENTES"];
                $user->fechaCertificadoAntecedentes = implode('-', array_reverse( explode('-', $fechaAntecedentes) ));;
                $user->banco = $row["BANCO"];

                $user->save();
                return;
            }, $data);
        });
    }
}
