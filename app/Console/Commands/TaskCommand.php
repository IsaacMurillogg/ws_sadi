<?php

namespace App\Console\Commands;

use App\Http\Controllers\LogController;
use App\Http\Controllers\UnitController;
use App\Models\Unit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TaskCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta la tarea que sincroniza wialon con la tabla de la base de datos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    /* public function __construct()
    {
        parent::__construct();
    } */

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Inicio de proceso');
        //    $ctrlU = new UnitController();
        //    $ctrlU->indexApiWialon();
        echo UnitController::guardarUnidades();
        Log::info("Cierre de proceso con " . sizeof(Unit::all()) . " datos");
        LogController::updateLog();
    }
}
