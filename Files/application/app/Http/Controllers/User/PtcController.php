<?php

namespace App\Http\Controllers\User;

use App\Models\Ptc;
use App\Models\PtcView;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PtcController extends Controller
{
    public function index(){

        $pageTitle      ='Ads List';
        $user           = auth()->user();
        $ads            = Ptc::where('status',1)->where('remain','>',0)->inRandomOrder()->orderBy('remain','desc')->limit(100)->get();
        $todayTotalView = PtcView::where('user_id',$user->id)->wheredate('view_date', now())->count();
        $viewed         = PtcView::where('user_id',auth()->user()->id)->where('view_date',Date('Y-m-d'))->pluck('ptc_id')->toArray();
        return view($this->activeTemplate.'user.ptc.index',compact('ads','pageTitle','viewed','todayTotalView'));
    }

    public function show($hash){

        $pageTitle = 'Show Advertisement';
        $user  = auth()->user();

        $id = $this->checkEligibleAd($hash,$user);

        if(!$id){
            $notify[] = ['error',"You are not eligible for this link"];
            return redirect()->route('user.home')->withNotify($notify);
        }

        $ptc        = Ptc::where('id',$id)->where('remain','>',0)->where('status',1)->firstOrFail();
        $viewAds    = PtcView::where('user_id',$user->id)->where('view_date',Date('Y-m-d'))->get();

         if($viewAds->count() >= gs()->per_day_ptc){
            $notify[] = ['error','Opps! Your limit is over. You cannot see more ads today'];
            return back()->withNotify($notify);
        }

        if ($viewAds->where('ptc_id',$ptc->id)->first()) {
            $notify[] = ['error','You cannot see this add before 24 hour'];
            return back()->withNotify($notify);
        }

        return view($this->activeTemplate.'user.ptc.show',compact('ptc','pageTitle'));

    }

    public function todayClick(){
        $pageTitle      =   'Today Click';
        $user           =   auth()->user();
        $totalClicks     =   PtcView::where('user_id', $user->id)->where('view_date',Date('Y-m-d'))->selectRaw('DATE(view_date) as date')->groupBy('date')->selectRaw('count(id) as clicks, sum(amount) as earned')->orderBy('date', 'desc')->paginate(getPaginate());

        return view($this->activeTemplate.'user.ptc.today_click',compact('totalClicks','pageTitle'));

    }

    public function confirm(Request $request,$hash){

        $user = auth()->user();
        $id = $this->checkEligibleAd($hash,$user);

        if(!$id){
            $notify[] = ['error',"You are not eligible for this link"];
            return redirect()->route('user.home')->withNotify($notify);
        }


        $ptc = Ptc::where('id',$id)->where('remain','>',0)->where('status',1)->firstOrFail();
        $viewAds = PtcView::where('user_id',$user->id)->where('view_date',Date('Y-m-d'))->get();

         if($viewAds->count() >= gs()->per_day_ptc){
            $notify[] = ['error','Opps! Your limit is over. You cannot see more ads today'];
            return back()->withNotify($notify);
        }

        if ($viewAds->where('ptc_id',$ptc->id)->first()) {
            $notify[] = ['error','You cannot see this add before 24 hour'];
            return back()->withNotify($notify);
        }

        $ptc->increment('showed');
        $ptc->decrement('remain');
        $ptc->save();

        $user->balance += gs()->ptc_amount;
        $user->save();


        $trx                            =   getTrx();
        $transection                    =   new Transaction();
        $transection->user_id           =   $user->id;
        $transection->amount            =   gs()->ptc_amount;
        $transection->post_balance      =   $user->balance;
        $transection->charge            =   0;
        $transection->trx_type          =   '+';
        $transection->trx               =   $trx;
        $transection->details           =   'Earn amount from ads';
        $transection->remark            =   'PPV earn';
        $transection->save();

        $PtcView                        =   New PtcView();
        $PtcView->user_id               =   $user->id;
        $PtcView->ptc_id                =   $ptc->id;
        $PtcView->amount                =   gs()->ptc_amount;
        $PtcView->view_date             =   Date('Y-m-d');
        $PtcView->save();

        $notify[] = ['success','Successfully viewed this ads'];
        return redirect()->route('user.ptc.index')->withNotify($notify);

    }

    protected function checkEligibleAd($hash, $user){
        $decrypted          =   decrypt($hash);
        $decryptData        =   explode('|',$decrypted);
        $id                 =   $decryptData[0];

        if($decryptData[1]!=$user->id){
            return false;
        }

        return $id;
    }


}
