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
    protected $description = "Start Slot Machine with random board values";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("[slotCommand] entry");
        $this->info("Executing Slot machine...\n");
        $boardConfig = ['rows' => 3, 'cols' => 5];
        $game = new Board($boardConfig);
        $paylines = [
            "8 5 8 11 10", // <-- bug, notice the duplicate '8', should match '5 8 11' but first 8 was skipped
            "9 5 8 11 10",
            "0 3 6 9 12",
            "1 4 7 10 13",
            "2 5 8 11 14",
            "0 4 8 10 12",
            "2 4 6 10 14",
            "0 5 8 11 14",
            "2 5 8 2 2",
        ];
        $betAmount = 100;
        $bet = new Bet($betAmount, $paylines);
        $betResult = $game->placeBet($bet);
        $payout = $betResult->getDetailWinnings();
        $boardValues = $game->values();
        $arrayP = $payout['paylines'];
        $result = "{\n".
            "\tboard: [" . implode(', ', $boardValues) . "],\n".
            "\tpaylines: " . json_encode($arrayP, JSON_UNESCAPED_SLASHES) . ",\n".
            "\tbet_amount: " . $betAmount . ",\n".
            "\ttotal_win: " . $payout['total_win'] . "\n".
        "}\n";
        $this->info($result);
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