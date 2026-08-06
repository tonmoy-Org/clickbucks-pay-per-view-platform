<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use App\Models\Referral;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlanController extends Controller
{
    public function  index(){
        $pageTitle = 'Plans';
        $plans =  Plan::latest()->paginate(getPaginate());
        return view('admin.plan.index',compact('pageTitle','plans'));

    }

    public function store(Request $request){

        $request->validate([
            'name'=>'required',
            'price'=>'required|numeric|min:1',
            'point'=>'required|numeric|min:1',
        ]);

        $plan = new Plan();
        $plan->name  =  $request->name;
        $plan->price  =  $request->price;
        $plan->point   =  $request->point;
        $plan->status  = isset($request->status)? 1:0;
        $plan->save();

        $notify[] = ['success', 'Plan has been created Successfully.'];
        return back()->withNotify($notify);
    }



    public function update(Request $request){

        $request->validate([
            'name'=>'required',
            'price'=>'required|numeric|min:1',
            'point'=>'required|numeric|min:1',
        ]);


        $plan=Plan::findOrFail($request->id);
        $plan->name  =  $request->name;
        $plan->price  =  $request->price;
        $plan->point   =  $request->point;
        $plan->status  = isset($request->status)? 1:0;
        $plan->save();

        $notify[] = ['success', 'Plan has been updated Successfully.'];
        return back()->withNotify($notify);
     }
}
