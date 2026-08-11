<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateDummyPpv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy:ppv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dummy data for tutulnaj in ClickBucks (ppvbucks.com)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::where('email', 'tutulnaj@gmail.com')->first();
        if (!$user) {
            $this->error("User not found!");
            return Command::FAILURE;
        }

        $totalViewsTarget = rand(450000, 500000);
        $currentViews = DB::table('ptc_views')->where('user_id', $user->id)->count();

        $this->info("Current views: $currentViews, Target: $totalViewsTarget");

        if ($currentViews < $totalViewsTarget) {
            $viewsToAdd = $totalViewsTarget - $currentViews;
            $this->info("Inserting $viewsToAdd views...");

            $batch = [];
            $batchSize = 2000;
            $now = Carbon::now();
            
            for ($i = 1; $i <= $viewsToAdd; $i++) {
                $date = $now->copy()->subDays(rand(1, 150))->subMinutes(rand(1, 1440));
                
                $batch[] = [
                    'ptc_id' => rand(1, 10),
                    'user_id' => $user->id,
                    'view_date' => $date->format('Y-m-d'),
                    'amount' => 0.01,
                    'created_at' => $date,
                    'updated_at' => $date
                ];
                
                if (count($batch) >= $batchSize) {
                    DB::table('ptc_views')->insert($batch);
                    $batch = [];
                    $this->info("Inserted $i / $viewsToAdd");
                }
            }
            
            if (count($batch) > 0) {
                DB::table('ptc_views')->insert($batch);
            }
        }

        // Clear previous dummy withdrawals and transactions
        Withdrawal::where('user_id', $user->id)->delete();
        Transaction::where('user_id', $user->id)->delete();

        // Add meaningful withdrawals spanning July and August 2026
        $this->info("Generating withdrawals for July and August...");
        
        $dates = [
            '2026-07-02', '2026-07-05', '2026-07-08', '2026-07-11', '2026-07-15', 
            '2026-07-18', '2026-07-21', '2026-07-25', '2026-07-28', '2026-07-31',
            '2026-08-03', '2026-08-05', '2026-08-08', '2026-08-10'
        ];

        foreach ($dates as $dateStr) {
            $date = Carbon::parse($dateStr)->addHours(rand(9, 20))->addMinutes(rand(1, 59));
            $withdrawAmount = rand(50, 150) + (rand(0, 99) / 100);
            
            $withdraw = new Withdrawal();
            $withdraw->method_id = 1; // Assuming 1 is a valid method ID
            $withdraw->user_id = $user->id;
            $withdraw->advertiser_id = 0;
            $withdraw->amount = $withdrawAmount;
            $withdraw->currency = 'USD';
            $withdraw->rate = 1;
            $withdraw->charge = 0;
            $withdraw->trx = Str::upper(Str::random(12));
            $withdraw->final_amount = $withdrawAmount;
            $withdraw->after_charge = $withdrawAmount;
            $withdraw->withdraw_information = '{"email":"tutulnaj@gmail.com"}';
            $withdraw->status = 1; // 1 = Approved
            $withdraw->admin_feedback = 'Approved';
            $withdraw->created_at = $date;
            $withdraw->updated_at = $date->copy()->addDays(rand(1, 2));
            $withdraw->save();
            
            // Generate corresponding transaction log
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $withdrawAmount;
            $transaction->post_balance = $user->balance - $withdrawAmount; // Fake post balance
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Withdraw via PayPal';
            $transaction->trx = $withdraw->trx;
            $transaction->remark = 'withdraw';
            $transaction->created_at = $date;
            $transaction->updated_at = $date;
            $transaction->save();
        }

        $totalPtcAmount = DB::table('ptc_views')->where('user_id', $user->id)->sum('amount');
        $totalWithdraw = DB::table('withdrawals')->where('user_id', $user->id)->sum('amount');
        
        $user->balance = max(0, $totalPtcAmount - $totalWithdraw);
        $user->save();

        $this->info("User balance updated to {$user->balance}");
        $this->info("Done! Meaningful withdrawals added to ppvbucks.com.");
        return Command::SUCCESS;
    }
}
