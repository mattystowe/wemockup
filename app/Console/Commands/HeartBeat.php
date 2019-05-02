<?php
/**
 *Command to generate a heart beat for host up
 *
 * takes the AWS EC2 public hostname
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Host;

class HeartBeat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heartbeat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a heartbeat to signal host up.';

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
     * @return mixed
     */
    public function handle()
    {

      //save the host heartbeat
        $host = new Host;
        $host->instance_id = $host->getInstanceId();
        $host->instance_type = $host->getInstanceType();
        $host->hostname = $host->getHostname();
        $host->save();
        $this->info('Sending Heartbeat Host Up for host: ' . $host->hostname . ' with instance-id: ' . $host->instance_id . ' type: ' . $host->instance_type);

    }
}
