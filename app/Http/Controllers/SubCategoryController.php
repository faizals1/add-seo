<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\{Products,ProductsVariations,ProductImagesModel,ProductsVariationsOptions};
use App\Models\{ProductCategoryModel,ProductSubCategory};
use App\Models\{Cartlist,Orderlist};
use App\Exports\ProductsExport;
use App\Exports\UserProductsExport;
use App\Exports\SubCategoryExport;
use App\Exports\AdminSubCategoryExport;
use App\Imports\SubCategoryImport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Helper;
use File;
use Image;

class SubCategoryController extends Controller
{
 
    public function index(Request $request,$id='')
    {

        // Urls
        $perpage = config('app.perpage');
        $userType = auth()->user()->role()->first()->name;
        $editUrl = 'superadmin.product-subcategory.edit';
        $deleteUrl = 'superadmin.product-subcategory.delete';
        $paginationUrl = 'superadmin.product-subcategory.index';
        $importUrl = 'superadmin.sub-category-import';
        $exportUrl = 'sub-category-export';
        $breadcrumbs = [
            ['link' => "/superadmin", 'name' => "Home"], ['link' => "superadmin/sub category", 'name' => 'Sub Category'], ['name' => "List"],
        ];
        
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.sub category list');
            
        $subCategory_List = ProductSubCategory::with('categoryname')->orderBy('id','DESC');
        $categoryResult = ProductCategoryModel::get();

        
        if($userType!=config('custom.superadminrole')){
            $editUrl = 'product-subcategory.edit';
            $deleteUrl = 'product-subcategory.delete';
            $paginationUrl = 'product-subcategory.index';
            $importUrl = 'sub-category-import';
            $exportUrl = 'sub-category-export';

            $company_id = Helper::loginUserCompanyId();

             $subCategoryList = $subCategory_List->whereHas('categoryname', function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });

            $categoryResult = ProductCategoryModel::whereHas('companyname', function ($query) use ($company_id) {

                $query->where('company_id', $company_id);

            })->get();

        }

        
        if($request->ajax()){
            $sub_category_list = $subCategory_List->when($request->seach_term, function($q)use($request){
                $q->where('procat_id',$request->seach_term);
            })->paginate($perpage);
            if($sub_category_list->count()>0){
                $sub_category_list = $sub_category_list;
            }else{
                $sub_category_list = [];
            }
           
            return view('pages.sub-category.ajax-list', compact('sub_category_list','editUrl','deleteUrl'))->render();
        }

        
        $sub_Category_List = $subCategory_List->paginate($perpage);

        $categoryResult = ProductCategoryModel::get();


        //   $company_id = auth()->user()->company->id;
                 
        // $user = Auth::user(); 
        // $company_id =Company::select('id')->$user->id; 
        // $company_id = auth()->user()->company()->first()->id;

        // dd($company_id); 
        
        // $company_id = Helper::loginUserCompanyId();
        // $categoryResult = ProductCategoryModel::whereHas('companyname', function ($query) use ($company_id) {
        //     $query->where('company_id', $company_id);
        //     })->get();
        // $categoryResult = ProductCategoryModel::whereHas('companyname', function ($query) use ($company_id) {
        //     $query->where('company_id', $company_id);
        // })->select('id', 'category_name')->get();
        
  
        return view('pages.sub-category.list',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'sub_category_list'=>$sub_Category_List, 'category_list'=>$categoryResult,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'userType'=>$userType,'exportUrl'=>$exportUrl,'importUrl'=>$importUrl]);
    }
 
    public function create()
    {
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.product-subcategory.index';
        $formUrl = 'superadmin.product-subcategory.store';
        if($userType!=config('custom.superadminrole')){
            $formUrl = 'product-subcategory.store';
            $company_id = Helper::loginUserCompanyId();
            $category = ProductCategoryModel::where('company_id',$company_id)->get();
            $listUrl = 'product-subcategory.index';
        }
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => route($listUrl), 'name' => __('locale.Sub Category')], ['name' => "Add"],
        ];

        $category = ProductCategoryModel::get();
        //Pageheader set true for breadcrumbs
        

        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Product category Add');
        return view('pages.sub-category.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'category'=>$category,'formUrl'=>$formUrl,'userType'=>$userType]);
    }

    public function store(Request $request)
    {
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.product-subcategory.index';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'product-subcategory.index';
        }

        $validator = Validator::make($request->all(), [
            'subcat_name' => 'required',
          
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $checkCatgoryName = ProductSubCategory::where('procat_id',$request->category_id)->where('subcat_name','like',$request->subcat_name);
        if($checkCatgoryName->count()>0){
            return redirect()->back()
            ->withErrors(__('locale.name_exits'))
            ->withInput();
            // return redirect()->back()->with('error',__('locale.name_exits'))->withInput();
        }
        
        $insert_data=[];
        
        $insert_data['procat_id'] = $request['category_id'];
        $insert_data['subcat_name'] = $request['subcat_name'];
        $create = ProductSubCategory::create($insert_data);
        
        // echo '<pre>';print_r($request->all());  exit();
        return redirect()->route($listUrl)->with('success',__('locale.sub_category_create_success'));  
       
    }

    public function show($id)
    {
        exit('show');
    }


    public function edit($id=0)
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => route("superadmin.product-subcategory.index"), 'name' => __('locale.Sub Category')], ['name' => "Add"],
        ];

        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'superadmin.product-subcategory.update';
        
        if($userType!=config('custom.superadminrole')){
            $formUrl = 'product-subcategory.update';
        }

        $category = ProductCategoryModel::get();

        // $company_id = Helper::loginUserCompanyId();
        // $category = ProductCategoryModel::whereHas('companyname', function ($query) use ($company_id) {
        //     $query->where('company_id', $company_id);
        //     })->get();
        // $category = ProductCategoryModel::whereHas('companyname', function ($query) use ($company_id) {
        //     $query->where('company_id', $company_id);
        // })->select('id', 'category_name')->get();
        
        $SubCategoryResult = ProductSubCategory::findOrFail($id);

        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Sub Category');
        return view('pages.sub-category.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'category'=>$category,'result'=>$SubCategoryResult,'formUrl'=>$formUrl,'userType'=>$userType]);
        
    }

    public function update(Request $request, $id=0)
    {

        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.product-subcategory.index';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'product-subcategory.index';
        }
        
        $result = ProductSubCategory::findOrFail($id);

        $result->subcat_name = $request->input('subcat_name');
        $result->procat_id = $request->input('category_id');

        $result->save();
        
        return redirect()->route($listUrl)->with('success',__('locale.sub_category_update_success'));  
        
    }

    public function destroy($id)
    {

        ProductSubCategory::find($id)->delete();
        return redirect()->back()->with('success',__('locale.sub category delete successmessage'));

    }
    public function subcategoryimport(Request $request){
        
        try{
            $import = new SubCategoryImport;
            Excel::import($import, request()->file('importfile'));
            
            return redirect()->back()->with('success', __('locale.import_message'));
        }
        catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            $listUrl = 'superadmin.product-subcategory.index';
        
            if($userType!=config('custom.superadminrole')){
                $listUrl = 'product-subcategory.index';
            }
            return redirect()->route($returnUrl)->with('error', __('locale.try_again'));
        }
    }
    public function subCategoryexport($type=''){
        
        if($type=='superadmin'){
            $categoryAdmin = new AdminSubCategoryExport;   
        }else{
            $categoryAdmin = new SubCategoryExport;
        }
        return Excel::download($categoryAdmin, 'product-subcategory-'.$type.time().'.xlsx');
    }

    // public function subCategoryexportFile($type=''){
        
    //     $categoryUser = new SubCategoryExport;
        
    //     // if($type=='companyadmin'){
    //     //     $categoryUser = new AdminSubCategoryExport;   
    //     // }else{
    //     //     $categoryUser = new SubCategoryExport;
    //     // }
    //     return Excel::download($categoryUser, 'sub-category-'.$type.time().'.xlsx');
    // }

  
}