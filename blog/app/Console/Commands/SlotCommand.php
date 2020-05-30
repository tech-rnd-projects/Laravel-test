<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

use App\Models\Bet;
use App\Models\Board;

class SlotCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'slot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Start Slot Machine";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("Executing Slot machine...");

        $boardConfig = ['rows' => 3, 'cols' => 5];
        $game = new Board($boardConfig);
        $payline = [];// new Payline();
        $game->play($payline);
        $boardStr = $game->print();
        $this->info("Print Board: \n" . $boardStr);

        $this->info("Finished Slot Machine.");
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }

}