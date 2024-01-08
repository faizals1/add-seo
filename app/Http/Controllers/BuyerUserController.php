<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\{Country, State, City};
use App\Models\{User,Role};
use App\Models\Company;
use App\Models\CompanyUserMapping;
use App\Models\BuyerTypeChannel;
use App\Imports\BuyerImport;
use App\Exports\BuyerExport;
use App\Exports\BuyerCompanyExport;
use Maatwebsite\Excel\Facades\Excel;
use Helper;
use DB;

class BuyerUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userType = auth()->user()->role()->first()->name;
        $perpage = config('app.perpage');
        $samplefile = 'buyer-import.csv';
        $paginationUrl = 'superadmin.buyer.index';
        $editUrl = 'superadmin.buyer.edit';
        $deleteUrl = 'superadmin.buyer.delete';
        $importUrl = 'superadmin.buyer.import';
        $exportUrl = 'superadmin.buyer.export';
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.Buyer')], ['name' => __('locale.add')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Buyer');
        // $usersResult = User::with('buyertypechannelName')->get();
        $usersResult = User::with('buyertypechannelName')->whereHas(
            'role', function($q){
                $q->where('name', 'general');
            }
            )->select(['id','name','email','phone','blocked','user_type','invite_code'])->orderBy('id','DESC');
            
            //  echo '<pre>'; print_r($usersResult); die;


        if(isset($userType) && $userType!=config('custom.superadminrole')){
            $company_id = Helper::loginUserCompanyId();

            //dd($company_id);
            
            $usersResult = $usersResult->whereHas(
                'company', function($q) use ($company_id){
                    $q->where('company_id', $company_id);
                }
            );
            
            $paginationUrl = 'buyer.index';
            $editUrl = 'buyer.edit';
            $deleteUrl = 'buyer.delete';
            $samplefile = 'buyer-company-import.csv';
            $importUrl = 'buyer.import';
            $exportUrl = 'buyer.export';

            //  //f
            // $usersResult = User::with('buyertypechannelName')->whereHas(
            //     'role', function($q){
            //         $q->where('name', 'general');
            //     })->orderBy('id','DESC');
            // //ef

        }
        
        if($request->ajax()){
            $usersResult = $usersResult->when($request->seach_term, function($q)use($request){
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%'.$request->seach_term.'%')
                        ->orWhere('email', 'like', '%'.$request->seach_term.'%');
                });
            })->paginate($perpage);
            return view('pages.buyer-users.ajax-list', compact('usersResult','editUrl','deleteUrl','userType'))->render();
        }
                    
        $usersResult = $usersResult->paginate($perpage);
        
        // echo '<pre>'; print_r($usersResult); die; 

        return view('pages.buyer-users.list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'usersResult'=>$usersResult,'pageTitle'=>$pageTitle,'userType'=>$userType,'editUrl'=>$editUrl,'paginationUrl'=>$paginationUrl,'deleteUrl'=>$deleteUrl,'importUrl'=>$importUrl,'samplefile'=>$samplefile,'exportUrl'=>$exportUrl]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'superadmin.buyer.store';
        $user_result=$states=$cities=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' =>route('superadmin.buyer.index'), 'name' =>  __('locale.Buyer')], ['name' => __('locale.Create')]];
      
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $pageTitle = __('locale.Buyer');
        $buyer_type_channel_data = BuyerTypeChannel::get();

        if(isset($userType) && $userType!=config('custom.superadminrole')){
            $formUrl = 'buyer.store';
        
            $company_id = Helper::loginUserCompanyId();

            $buyer_type_channel_data = BuyerTypeChannel::where('company_id', $company_id)->get();

           // echo '<pre>'; print_r($buyer_type_channel_data); die; 
        }

        // $editMode = false; 

        return view('pages.buyer-users.create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'buyer_type_channel_data'=>$buyer_type_channel_data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        //echo '<pre>'; print_r($request->all()); die;

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'email' => 'required|unique:users|max:250',
            'user_type' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        $userType = auth()->user()->role()->first()->name;

        $role = Role::where('name', 'general')->first();
        
        $request['password'] = 'null';
        $request['invite_code'] = Str::random(8);
        // dd($request->all()); exit();
  
        //echo '<pre>'; print_r($request['user_type']); die;
        $user = User::create($request->all());
        $user->company()->attach($request->company);
        $user->role()->attach( $role->id);
        
        $list = 'superadmin.buyer.index';
        if(isset($userType) && $userType!=config('custom.superadminrole')){
            $list = 'buyer.index';
        }
        
        return redirect()->route($list)->with('success',__('locale.success common add'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $editMode = true; 

        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'superadmin.buyer.update';
        $buyer_type_channel_edit = BuyerTypeChannel::get();
        if(isset($userType) && $userType!=config('custom.superadminrole')){
            $formUrl = 'buyer.update';
            $company_id = Helper::loginUserCompanyId();

            $buyer_type_channel_edit = BuyerTypeChannel::where('company_id', $company_id)->get();

        }
        $user_result=$states=$cities=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' =>route("superadmin.buyer.index"), 'name' =>  __('locale.Buyer')], ['name' => __('locale.Edit')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $pageTitle = __('locale.Buyer');
        $user_result = User::with('company')->whereHas(
            'role', function($q){
                $q->where('name', 'general');
            })->select('id','name','email','user_type','blocked')->where('id',$id)->first();

            //echo '<pre>'; print_r($buyer_type_channel_edit); die; 
            
        return view('pages.buyer-users.create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'user_result'=>$user_result,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl,'buyer_type_channel_edit'=>$buyer_type_channel_edit,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //echo '<pre>'; print_r($request->all()); die;
        $userType = auth()->user()->role()->first()->name;
        $buyer = User::find($id);
        $buyer->name = $request->name;
        $buyer->user_type = $request->user_type;
        $buyer->blocked = $request->blocked;
        $buyer->save();
        $buyer->company()->sync($request->company);
        $listUrl = 'superadmin.buyer.index';
        if(isset($userType) && $userType!=config('custom.superadminrole')){
            $listUrl = 'buyer.index';
        }
        return redirect()->route($listUrl)->with('success',__('locale.success common update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $buyer = User::find($id);
        if($buyer){
            $buyer->delete();
            return redirect()->back()->with('success',__('locale.delete_message'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
    }

    public function buyerImport(Request $request){
        try{
            $import = new BuyerImport;
            Excel::import($import, request()->file('importfile'));
            return redirect()->back()->with('success', __('locale.import_message'));
        }catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            
            return redirect()->route('company.index')->with('error', __('locale.try_again'));
        }
            
    }

    public function buyerExport($type) 
    {
        if($type=='superadmin'){
            $companyUser = new BuyerCompanyExport;
        }else{
            $companyUser = new BuyerExport;   
        }
        return Excel::download($companyUser, 'buyer-'.$type.time().'.xlsx');
        
    }
}
