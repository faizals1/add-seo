<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\ProductVariationType;
use App\Models\{Products,ProductsVariations,ProductImagesModel,ProductsVariationsOptions};
use App\Models\{ProductCategoryModel,ProductSubCategory};
use App\Models\{Cartlist,Orderlist};
use App\Exports\ProductsExport;
use App\Exports\UserProductsExport;
use App\Exports\ProductVariationTypeExport;
use App\Exports\AdminProductVariationTypeExport;
use App\Imports\ProductVariationTypeImport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use Helper;
use File;
use Image;
use Auth;

class ProductVariationTypeController extends Controller
{

    public function index(Request $request,$id='')
    {

        // Urls
        $perpage = config('app.perpage');
        $userType = auth()->user()->role()->first()->name;
        $editUrl = 'superadmin.product-variation-type.edit';
        $deleteUrl = 'superadmin.product-variation-type.delete';
        $paginationUrl = 'superadmin.product-variation-type.index';
        $importUrl = 'superadmin.product-variation-type-import';
        $exportUrl = 'product-variation-type-export';
        $breadcrumbs = [
            ['link' => "/superadmin", 'name' => "Home"], ['link' => "superadmin/product variation type", 'name' => 'Product Variation Type'], ['name' => "List"],
        ];
        
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.product variation type list');
            
        $productvariation_List = ProductVariationType::with('companyname')->orderBy('id','DESC');

        // echo '<pre>'; print_r($productvariation_List); die; 

        if($userType!=config('custom.superadminrole')){
            $editUrl = 'product-variation-type.edit';
            $deleteUrl = 'product-variation-type.delete';
            $paginationUrl = 'product-variation-type.index';
            $importUrl = 'product-variation-type-import';
            $exportUrl = 'product-variation-type-export';
            $company_id = Helper::loginUserCompanyId();

            $productvariation_List = $productvariation_List->whereHas('companyname',function($query) use ($company_id) {
                $query->where('company_id',$company_id);
            });
        }
        if($request->ajax()){

            $query = $request->get('query');

            $product_variation_List = $productvariation_List->where('name','LIKE', '%'.$query . '%')->paginate($perpage);
            return view('pages.product-variation-type.ajax-list', compact('product_variation_List','editUrl','deleteUrl'))->render();

        }

        $product_variation_List = $productvariation_List->paginate($perpage);
        
        return view('pages.product-variation-type.list',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'product_variation_List'=>$product_variation_List,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'userType'=>$userType,'importUrl'=>$importUrl,'exportUrl'=>$exportUrl]);
    }

    public function create()
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' =>route('superadmin.product-variation-type.index'),'name' => __('locale.Product Variation Type')], ['name' => "Add"],
        ];

        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'superadmin.product-variation-type.store';
        if($userType!=config('custom.superadminrole')){
            $formUrl = 'product-variation-type.store';
            
        }
       
        //Pageheader set true for breadcrumbs
        // $category = ProductCategoryModel::get();
        $company = Company::get();

        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Product Variation Type Add');
        return view('pages.product-variation-type.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'formUrl'=>$formUrl,'company'=>$company,'userType'=>$userType]);
    }

    public function store(Request $request)
    {

        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.product-variation-type.index';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'product-variation-type.index';
        }       
    
        $validator = Validator::make($request->all(), [
            'name' => 'required',
          
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $checkCatgoryName = ProductVariationType::where('company_id',$request->company_id)->where('name','like',$request->name);
        if($checkCatgoryName->count()>0){
            return redirect()->back()
            ->withErrors(__('locale.name_exits'))
            ->withInput();
            // return redirect()->back()->with('error',__('locale.name_exits'))->withInput();
        }
        $insert_data=[];
        
        $insert_data['company_id'] = $request['company_id'];
        $insert_data['name'] = $request['name'];
        $create = ProductVariationType::create($insert_data);
        
        // echo '<pre>';print_r($request->all());  exit();
        return redirect()->route($listUrl)->with('success',__('locale.product_variation_type_success'));  
       
    }

    public function show($id)
    {
        exit('show');
    }

    public function edit($id=0)
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => route("superadmin.product-variation-type.index"), 'name' => __('locale.Product Variation Type')], ['name' => "Edit"],
        ];

        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'superadmin.product-variation-type.update';
            
        if($userType!=config('custom.superadminrole')){
            $formUrl = 'product-variation-type.update';
        }

        $company = Company::get();
        
        $productvariantResult = ProductVariationType::findOrFail($id);

        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Product Variation Type');
        return view('pages.product-variation-type.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'result'=>$productvariantResult,'formUrl'=>$formUrl,'company'=>$company,'userType'=>$userType]);
        
    }
    public function update(Request $request, $id=0)
    {
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.product-variation-type.index';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'product-variation-type.index';
        }
        
        $checkCatgoryName = ProductVariationType::where('company_id',$request->company_id)->where('name','like',$request->name);
        if($checkCatgoryName->count()>0){
            return redirect()->back()
            ->withErrors(__('locale.name_exits'))
            ->withInput();
            // return redirect()->back()->with('error',__('locale.name_exits'))->withInput();
        }

        $result = ProductVariationType::findOrFail($id);

        $result->name = $request->input('name');
        $result->company_id = $request->input('company_id');


        $result->save();
        
        return redirect()->route($listUrl)->with('success',__('locale.product_variation_type_update_success'));  
        
    }

    public function destroy($id)
    {

        ProductVariationType::find($id)->delete();
        return redirect()->back()->with('success',__('locale.product_variation_type_delete_successmessage'));

    }

    public function productvariationtypeimport(Request $request){
        
        try{
            $import = new ProductVariationTypeImport;
            Excel::import($import, request()->file('importfile'));
            // print_r($import); exit();
            return redirect()->back()->with('success', __('locale.import_message'));
        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            $listUrl = 'superadmin.product-variation-type.index';
        
            if($userType!=config('custom.superadminrole')){
                $listUrl = 'product-variation-type.index';
            }
            return redirect()->route($returnUrl)->with('error', __('locale.try_again'));
        }
    }
    public function productvariationtypeexport($type=''){
        
        if($type=='superadmin'){
            $categoryAdmin = new AdminProductVariationTypeExport;   
        }else{
            $categoryAdmin = new ProductVariationTypeExport;
        }
        return Excel::download($categoryAdmin, 'product-variation-type-'.$type.time().'.xlsx');
    }
}    