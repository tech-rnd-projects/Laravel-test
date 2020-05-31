<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputOption;

use App\Models\Bet;
use App\Models\Board;

class SlotStaticCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'slot-static';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Start Slot Machine with static board values";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("[slotCommand] entry");
        $this->info("Executing Slot machine...\n");
        $testBoard = "J J J Q K cat J Q monkey bird bird bird J Q A";
        $boardConfig = ['rows' => 3, 'cols' => 5, 'boardValues' => $testBoard];
        $game = new Board($boardConfig);
        $paylines = [
            "8 5 8 11 10", // <-- bug, notice the duplicate '8', should match '5 8 11' but first 8 was skipped
            "9 5 8 11 10",
            "0 3 6 9 12",
            "2 5 8 2 2",
            "1 4 7 10 13", // samples
            "2 5 8 11 14", // samples
            "0 4 8 10 12", // samples
            "2 4 6 10 14", // samples
            "0 5 8 11 14", // samples
        ];
        $bet = new Bet(100, $paylines);
        $betResult = $game->placeBet($bet);
        $payout = $betResult->getDetailWinnings();
        $result = json_encode($payout, JSON_PRETTY_PRINT);
        $this->info($result . "\n");
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