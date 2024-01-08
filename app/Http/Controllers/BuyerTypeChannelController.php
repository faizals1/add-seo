<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\BuyerTypeChannel;
use App\Models\{Products,ProductsVariations,ProductImagesModel,ProductsVariationsOptions};
use App\Models\{ProductCategoryModel,ProductSubCategory};
use App\Models\{Cartlist,Orderlist};
use App\Exports\ProductsExport;
use App\Exports\UserProductsExport;
use App\Exports\BuyerTypeChannelExport;
use App\Exports\AdminBuyerTypeChannelExport;
use App\Imports\BuyerTypeChannelImport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use Helper;
use File;
use Image;
use Auth;

class BuyerTypeChannelController extends Controller
{

    public function index(Request $request,$id='')
    {
        
        // Urls
        $perpage = config('app.perpage');
        $userType = auth()->user()->role()->first()->name;
        $editUrl = 'superadmin.buyer-type-channel.edit';
        $deleteUrl = 'superadmin.buyer-type-channel.delete';
        $paginationUrl = 'superadmin.buyer-type-channel.index';
        $importUrl = 'superadmin.buyer-type-channel-import';
        $exportUrl = 'buyer-type-channelexport';
        $breadcrumbs = [
            ['link' => "/superadmin", 'name' => "Home"], ['link' => "superadmin/buyer type channel", 'name' => 'Buyer Type Channel'], ['name' => "List"],
        ];
        
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.buyer type channel list');

        $buyertypechannel_List = BuyerTypeChannel::with('companyname')->orderBy('id','DESC');

        if($userType!=config('custom.superadminrole')){
            $editUrl = 'buyer-type-channel.edit';
            $deleteUrl = 'buyer-type-channel.delete';
            $paginationUrl = 'buyer-type-channel.index';
            $importUrl = 'buyer-type-channel-import';
            $exportUrl = 'buyer-type-channelexport';
            $company_id = Helper::loginUserCompanyId();

            $buyertypechannel_List = $buyertypechannel_List->whereHas('companyname',function($query) use ($company_id) {
                $query->where('company_id',$company_id);
            });
           

        }

        if($request->ajax()){

            $query = $request->get('query');

            $buyer_typechannel_List = $buyertypechannel_List->where('name','LIKE', '%'.$query . '%')->paginate($perpage);
            return view('pages.buyer-type-channel.ajax-list', compact('buyer_typechannel_List','editUrl','deleteUrl'))->render();

        }

        $buyer_typechannel_List = $buyertypechannel_List->paginate($perpage);
        
        return view('pages.buyer-type-channel.list',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'buyer_typechannel_List'=>$buyer_typechannel_List,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'userType'=>$userType,'importUrl'=>$importUrl,'exportUrl'=>$exportUrl]);
    }

    public function create()
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => route('superadmin.buyer-type-channel.index'), 'name' => __('locale.Buyer Type Channel')], ['name' => "Add"],
        ];

        $userType = auth()->user()->role()->first()->name;

        $formUrl = 'superadmin.buyer-type-channel.store';

        if($userType!=config('custom.superadminrole')){
            $formUrl = 'buyer-type-channel.store';
            
        }
       
        $company = Company::get();

        //Pageheader set true for breadcrumbs
        // $category = ProductCategoryModel::get();

        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Buyer Type Channel Add');
        return view('pages.buyer-type-channel.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'formUrl'=>$formUrl,'userType'=>$userType,'company'=>$company]);

    }

    public function store(Request $request)
    {
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.buyer-type-channel.index';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'buyer-type-channel.index';
        }
    
        $validator = Validator::make($request->all(), [
            'name' => 'required',
          
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        
        $checkCatgoryName = BuyerTypeChannel::where('company_id',$request->company_id)->where('name','like',$request->name);
        if($checkCatgoryName->count()>0){
            return redirect()->back()
            ->withErrors(__('locale.name_exits'))   //
            ->withInput();
            // return redirect()->back()->with('error',__('locale.name_exits'))->withInput();
        }

        $insert_data=[];
        
        $insert_data['company_id'] = $request['company_id'];
        $insert_data['name'] = $request['name'];

        $create = BuyerTypeChannel::create($insert_data);
        
        // echo '<pre>';print_r($request->all());  exit();
        return redirect()->route($listUrl)->with('success',__('locale.buyer_type_channel_success'));  
       
    }

    public function show($id)
    {
        exit('show');
    }

    public function edit($id=0)
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => route("superadmin.buyer-type-channel.index"), 'name' => __('locale.Buyer Type Channel')], ['name' => "Edit"],
        ];

        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'superadmin.buyer-type-channel.update';
            
        if($userType!=config('custom.superadminrole')){
            $formUrl = 'buyer-type-channel.update';
        }

        $company = Company::get();


        $BuyerTypeResult = BuyerTypeChannel::findOrFail($id);

        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Buyer Type Channel');
        return view('pages.buyer-type-channel.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'result'=>$BuyerTypeResult,'formUrl'=>$formUrl,'userType'=>$userType,'company'=>$company]);
        
    }
    public function update(Request $request, $id=0)
    {

        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.buyer-type-channel.index';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'buyer-type-channel.index';
        }

        $checkCatgoryName = BuyerTypeChannel::where('company_id',$request->company_id)->where('name','like',$request->name);
        if($checkCatgoryName->count()>0){
            return redirect()->back()
            ->withErrors(__('locale.name_exits'))
            ->withInput();
            // return redirect()->back()->with('error',__('locale.name_exits'))->withInput();
        }

        $result = BuyerTypeChannel::findOrFail($id);

        $result->name = $request->input('name');
        $result->company_id = $request->input('company_id');


        $result->save();
        
        return redirect()->route($listUrl)->with('success',__('locale.buyer_type_channel_update_success'));  
        
    }

    public function destroy($id)
    {

        BuyerTypeChannel::find($id)->delete();
        return redirect()->back()->with('success',__('locale.buyer type channel delete successmessage'));

    }

    public function buyertypechannelimport(Request $request){
        
        try{
            $import = new BuyerTypeChannelImport;
        
             Excel::import($import, $request->file('importfile'));
            return redirect()->back()->with('success', __('locale.import_message'));
        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            $listUrl = 'superadmin.buyer-type-channel.index';
        
            if($userType!=config('custom.superadminrole')){
                $listUrl = 'buyer-type-channel.index';
            }
            return redirect()->route($returnUrl)->with('error', __('locale.try_again'));
        }
    }
    public function buyertypechannel_export($type=''){
        
        if($type=='superadmin'){
            $buyertypeAdmin = new AdminBuyerTypeChannelExport;   
        }else{
            $buyertypeAdmin = new BuyerTypeChannelExport;
        }
        return Excel::download($buyertypeAdmin, 'buyer-type-channel-'.$type.time().'.xlsx');
    }

}    