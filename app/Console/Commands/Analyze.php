<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TestController;

class Analyze extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analyze:data {store?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates data for statistics';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $store_id = $this->argument('store');

        $controller = new TestController();
        $controller->test($store_id);

        $this->info('The command was successful!');
    }
}
