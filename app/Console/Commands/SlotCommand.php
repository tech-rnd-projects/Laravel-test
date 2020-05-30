<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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
        Log::info("[slotCommand] entry");
        $this->info("Executing Slot machine...");
        $boardConfig = ['rows' => 3, 'cols' => 5];
        $game = new Board($boardConfig);
        $payline = [
            "0 3 6 9 12",
            "1 4 7 10 13",
            "2 5 8 11 14",
            "0 4 8 10 12",
            "2 4 6 10 14",
        ];
        $bet = new Bet(1, $payline);
        $betResult = $game->placeBet($bet);
        $payout = $betResult->getDetailWinnings();
        $result = json_encode($payout);
        $this->info("payout: " . $result . "\n\n");
        $boardStr = $game->print();
        $this->info("Print Board: \n" . $boardStr);

        $this->info("Finished Slot Machine.");
    }

    function payout($winnings) {

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