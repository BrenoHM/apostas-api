<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\Bet;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessBetsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'processbets:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process bets';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //etapas do processo
        // 1 - buscar na tabela bets as apostas que estao com status opened
        // 2 - consultar a partida e verificar se o status dela esta completed
        // 3 - verificar se o id do winning team é igual o da winning id team da tabela de bets
        // 4 - se for igual, pega o valor apostado * pelo odd do time, atualiza a tabela de users com o amount
        // 5 - atualiza o stauts da tabela bets com processed
        $bets = Bet::where('status', 'opened')->get();
        if( $bets->count() ) {
            foreach($bets as $bet) {
                //consulta partida
                $match = Helper::matchDetalhe($bet->match_id);
                if( $match ) {
                    if( $match->status == "complete" ) {
                        if( $match->winningTeam === $bet->bet ){
                            $amount = $bet->bet_value * $bet->odd;
                            $user = User::find($bet->user_id);
                            if( $user ) {
                                $user->amount = $user->amount + $amount;
                                $user->save();
                            }
                        }

                        Bet::where('id', $bet->id)->update([
                            'winning_tem' => $match->winningTeam,
                            'status' => 'processed'
                        ]);
                    }
                }
            }
        }
        Log::info('Process bets success!');
        // $this->info('processbets:cron Cummand Run successfully!');
        // return 0;
    }
}
