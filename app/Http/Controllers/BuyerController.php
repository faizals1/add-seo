<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuyerGroup;
use Illuminate\Support\Facades\Validator;

class BuyerController extends Controller
{

    //
    public function create()
    {
        //dd('hi');
        // Breadcrumbs
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "buyer", 'name' => "Buyer"], ['name' => "Add"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Buyer Type Add');
        $buyergroup = BuyerGroup::get(["type","currency_code"]);
        return view('pages.buyer.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'buyergroup'=>$buyergroup]);
    }

    public function store(Request $request)
    {
        //dd('hi');
        
        $validator = Validator::make($request->all(), [
            'group_code' => 'required|max:250',
            'group_name' => 'required|max:10',
            'type' => 'required|max:250',
            'currency_code' => 'max:250',
            
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        // echo '<pre>';print_r($request->all());  exit();
        $buyer = BuyerGroup::create($request->all());
        //dd('hi');
        if($buyer){
            return redirect()->to('superadmin/buyer-type')->with('success',__('locale.buyer_create_success'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
    }

    public function index(Request $request)
    {
        //exit('inserted');
        $perpage = 20;//config('app.perpage');
        $BuyerResultResponse = [];
        // Breadcrumbs
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "buyer", 'name' => "Buyer"], ['name' => "Add"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Buyer Type List');
        $BuyerResult = BuyerGroup::select(['id','group_code','group_name','type','currency_code'])->orderBy('id','DESC')->paginate($perpage);

        if($request->ajax()){
            $BuyerResult = BuyerGroup::select(['id','group_code','group_name','type','currency_code'])->orderBy('id','DESC')
                        ->when($request->seach_term, function($q)use($request){
                            $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('group_name', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('group_code', 'like', '%'.$request->seach_term.'%');
                        })
                        ->paginate($perpage);
                        // ->when($request->status, function($q)use($request){
                        //     $q->where('status',$request->status);
                        // })
            return view('pages.buyer.buyer-table-list', compact('BuyerResult'))->render();
        }
        if($BuyerResult->count()>0){
            $BuyerResultResponse = $BuyerResult;
        }
        return view('pages.buyer.list',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'BuyerResult'=>$BuyerResultResponse]);
    }

    public function edit($id)
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "buyer", 'name' => "Buyer"], ['name' => "Add"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Buyer Type Add');
        $buyer_result = BuyerGroup::find($id);
        //$buyer_type= BuyerGroup::where('type',$buyer_result->type)->get(["id","type"]);
        $buyer_type=BuyerGroup::get(["id","type","currency_code"]);
        //echo"<pre>";print_r($buyer_type);die;
        $buyer_currency= BuyerGroup::where('currency_code',$buyer_result->currency_code)->get(["id","currency_code"]);
        // $states = State::where('country_id',$company_result->country)->get(["name", "id"]);
        // $cities = City::where('state_id',$company_result->state)->get(["name", "id"]);
        if(!$buyer_result){
            return redirect()->route('buyer-type')->with('error','Company id not match');
        }
        return view('pages.buyer.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'buyer_result'=>$buyer_result,'buyer_type'=>$buyer_type,'buyer_currency'=>$buyer_currency]);
    }

    public function update(Request $request, $id)
    {
       // print_r($request->all());die;
        $validator = Validator::make($request->all(), [
            'group_code' => 'required|max:250',
            'group_name' => 'required|max:10',
            'type' => 'required|max:250',
            'currency_code' => 'max:250',
            
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        unset($request['_method']);
        unset($request['_token']);
        unset($request['action']);
        // echo '<pre>';print_r($request->input()); exit();
        $buyer = BuyerGroup::where('id',$id)->update($request->input());
        if($buyer){
            return redirect()->route('buyer-type')->with('success',__('locale.company_update_success'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
        
    }

    public function destroy($id)
    {
       // dd('hi');
       
        if(BuyerGroup::where('id',$id)->delete()){
            return redirect()->back()->with('success',__('locale.delete_message'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
    }

    // public function show($id)
    // {
    //     //exit('show');
    //     return redirect()->back()->with('success',__('locale.delete_message'));
    // }


}




