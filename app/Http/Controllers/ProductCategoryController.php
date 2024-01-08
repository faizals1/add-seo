<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\{Products,ProductsVariations,ProductImagesModel,ProductsVariationsOptions};
use App\Models\{ProductCategoryModel,ProductSubCategory};
use App\Models\{Cartlist,Orderlist};
use App\Exports\ProductCategoryExport;
use App\Exports\AdminProductCategoryExport;
use App\Imports\ProductCategoryImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use Helper;
use File;
use Image;

class ProductCategoryController extends Controller
{
 
    public function index(Request $request,$id='')
    {
        // Urls
        $perpage = config('app.perpage');
        $userType = auth()->user()->role()->first()->name;
        $editUrl = 'superadmin.product-category.edit';
        $deleteUrl = 'superadmin.product-category.delete';
        $paginationUrl = 'superadmin.product-category.index';
        $importUrl = 'superadmin.product-category-import';
        $exportUrl = 'product-category-export';
        
        // Breadcrumbs
        $breadcrumbs = [
            ['link' => "/superadmin", 'name' => "Home"], ['link' => "superadmin/product category", 'name' => "product category"], ['name' => "List"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Company List');
        $productCategoryList = ProductCategoryModel::with('companyname')->orderBy('id','DESC');
        $companyResult = Company::get();

        
        if($userType!=config('custom.superadminrole')){
            $editUrl = 'product-category.edit';
            $deleteUrl = 'product-category.delete';
            $paginationUrl = 'product-category.index';
            $importUrl = 'product-category-import';
            $exportUrl = 'product-category-export';
            $company_id = Helper::loginUserCompanyId();
            $productCategoryList = $productCategoryList->whereHas('companyname',function($query) use ($company_id) {
                $query->where('company_id',$company_id);
            });

            $companyResult = Company::select('id','company_code','company_name')->where('id',$company_id)->get();
   
        }

        if($request->ajax()){
            if($userType==config('custom.superadminrole')){
                
                $productCategoryList = $productCategoryList->whereHas('companyname',function($query) use ($request) {
                    
                    $query->when($request->seach_term, function($q1)use($request){
                        $q1->where('company_id',$request->seach_term);
                    });
                });
            }else{
                $productCategoryList = $productCategoryList->when($request->seach_term, function($q)use($request){
                    $q->where('category_name', 'like', '%'.$request->seach_term.'%');
                });
            }
            $product_category_list = $productCategoryList->paginate($perpage); 
            
            return view('pages.product-category.ajax-list', compact('product_category_list','editUrl','deleteUrl'))->render();
        }

        $productCategoryList = $productCategoryList->paginate($perpage);
        // dd($companyResult);
        return view('pages.product-category.list',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'product_category_list'=>$productCategoryList, 'company_list'=>$companyResult,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'userType'=>$userType,'exportUrl'=>$exportUrl,'importUrl'=>$importUrl]);
    }
    public function create()
    {
        $userType = auth()->user()->role()->first()->name;

        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => route("superadmin.product-category.index"), 'name' => __('locale.product category')], ['name' => "Add"],
        ];
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'superadmin.product-category.store';
        if($userType!=config('custom.superadminrole')){
            $formUrl = 'product-category.store';
        }
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $formUrl = 'superadmin.product-category.store';

        $company = Company::get();

        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.product category');
        return view('pages.product-category.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'company'=>$company,'formUrl'=>$formUrl,'userType'=>$userType]);
    }

    public function store(Request $request)
    
        
    {        
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.product-category.index';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'product-category.index';
        }

        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        
        $checkCatgoryName = ProductCategoryModel::where('company_id',$request->company_id)->where('category_name','like',$request->category_name);
        if($checkCatgoryName->count()>0){
            return redirect()->back()
            ->withErrors(__('locale.name_exits'))
            ->withInput();
            // return redirect()->back()->with('error',__('locale.name_exits'))->withInput();
        }
        $insert_data=[];
        
        $insert_data['company_id'] = $request['company_id'];
        $insert_data['category_name'] = $request['category_name'];
        $create = ProductCategoryModel::create($insert_data);

        return redirect()->route($listUrl)->with('success',__('locale.product_category_success'));  
    }

    public function show($id)
    {
        //
    }

    public function edit($id=0)
    {
        
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => route("superadmin.product-category.index"), 'name' => __('locale.product category')], ['name' => "Add"],
        ];

        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'superadmin.product-category.update';
        
        if($userType!=config('custom.superadminrole')){
            $formUrl = 'product-category.update';
        }
        $company = Company::get();
        //Pageheader set true for breadcrumbs

        $productCategoryResult = ProductCategoryModel::findOrFail($id);

        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.product category');
        return view('pages.product-category.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'company'=>$company,'result'=>$productCategoryResult,'formUrl'=>$formUrl,'userType'=>$userType]);        
        
    }

    public function update(Request $request, $id=0)
    {
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.product-category.index';
        
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'product-category.index';
        }

        $result = ProductCategoryModel::findOrFail($id);

        $result->category_name = $request->input('category_name');
        $result->company_id = $request->input('company_id');

        $result->save();
        
        return redirect()->route($listUrl)->with('success','Data Updated Successfully');
        
    }
    
    public function destroy($id)
    {
        ProductCategoryModel::find($id)->delete();
        return redirect()->back()->with('success','Data Deleted Successfully');
    } 
    
    public function productcategoryimport(Request $request){
        
        try{
            $import = new ProductCategoryImport;
            Excel::import($import, request()->file('importfile'));
            $import->getRowCount();
            
            if($import->getRowCount()==0){
                
                return redirect()->back()->with('error',implode('<br>',$import->getErrorMessage()));
            }
            return redirect()->back()->with('success', __('locale.import_message'));
        }catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            $listUrl = 'superadmin.product-category.index';
        
            if($userType!=config('custom.superadminrole')){
                $listUrl = 'product-category.index';
            }
            return redirect()->route($returnUrl)->with('error', __('locale.try_again'));
        }
    }
    public function productcategoryexport($type=''){
        
        if($type=='superadmin'){
            $companyUser = new AdminProductCategoryExport;   
        }else{
            $companyUser = new ProductCategoryExport;
        }
        return Excel::download($companyUser, 'product-category-'.$type.time().'.xlsx');
    }
}