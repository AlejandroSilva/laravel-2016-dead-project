<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Log;

class NominasPendientes extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nominas:pendientes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa las nominas que se encuentran pendientes';

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
        $this->info('revisando nominas pendientes....');
        Log::info('revisando nominas pendientes....');
    }
}
