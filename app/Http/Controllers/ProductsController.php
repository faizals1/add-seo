<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use App\Models\{Products,ProductsVariations,ProductImagesModel,ProductsVariationsOptions};
use App\Models\{ProductCategoryModel,ProductSubCategory};
use App\Models\{Cartlist,Orderlist};
use App\Exports\ProductsExport;
use App\Exports\UserProductsExport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProductVariationType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Helper;
use File;
use Image;

class ProductsController extends Controller
{
    
    function __construct()
    {
        
        //  $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
        //  $this->middleware('permission:product-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userType = auth()->user()->role()->first()->name;
        $perpage = config('app.perpage');
        $productResultResponse = [];
        
        // Breadcrumbs
        $breadcrumbs = [
            ['link' => "/superadmin", 'name' => "Home"], ['link' => "superadmin/product", 'name' => __('locale.Items')], ['name' => "List"]
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Items List');
        $editUrl = 'superadmin.product.edit';
        $deleteUrl = 'superadmin.product.delete';
        $sampleFileName = 'product-import.csv';
        $productResult = Products::with('product_variation')->with(['category','subcategory','company'])->select(['id','product_code','product_name','product_slug','product_catid','product_subcatid','food_type','blocked'])->orderBy('id','DESC');

        // dd($productResult[0]->product_variation[0]->productvariationName); exit();

        // dd($productResult); exit();

        if($userType!=config('custom.superadminrole')){
            $company_id = Helper::loginUserCompanyId();
            $productResult = $productResult->whereHas('company',function($query) use ($company_id) {
                $query->where('company_id',$company_id);
            });
            $editUrl = 'product.edit';
            $deleteUrl = 'product.delete';
        }
        // dd($productResult->get()); exit();
        if($request->ajax()){
            // exit('abc');
            $productResult = $productResult->when($request->seach_term, function($q)use($request){
                            $q->where('product_name', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('product_code', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('food_type', 'like', '%'.$request->seach_term.'%');

                        })
                        ->when($request->status, function($q)use($request){
                            $q->where('blocked',$request->status);
                        })
                        ->paginate($perpage);
            return view('pages.products.ajax-list', compact('productResult','editUrl','deleteUrl'))->render();
        }
        $productResult = $productResult->paginate($perpage);

        if($productResult->count()>0){
            $productResultResponse = $productResult;
        }
        return view('pages.products.list',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'productResult'=>$productResultResponse,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl,'sampleFileName'=>$sampleFileName]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id='')
    {
        
        $userType = auth()->user()->role()->first()->name;
        $product_result=$states=$productSubCategoryResult=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "product", 'name' => __('locale.Items')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $formUrl = 'superadmin.product.store';
        $companies = Company::get(["company_name", "id","company_code"]);
        $productCategoryResult = ProductCategoryModel::get(["category_name", "id"]);
        $deleteImageUrl = 'superadmin/product/imagedelete';

        $product_Variation_type =  ProductVariationType::get();

        if($userType!=config('custom.superadminrole')){
            $formUrl = 'product.store';
            $deleteImageUrl = 'product/imagedelete';
            $company_id = Helper::loginUserCompanyId();
            $productCategoryResult = ProductCategoryModel::where('company_id',$company_id)->get(["category_name", "id"]);

            $product_Variation_type =  ProductVariationType::where('company_id',$company_id)->get();

        }
        $productsVariationsOptions = ProductsVariationsOptions::select('id','name')->get();


            //echo '<pre>'; print_r($productsVariationsOptions); exit();

        $foodTypeResult = ['veg','non-veg'];
        $pageTitle = __('locale.Items'); 
        $productCode = Helper::setNumber();
        
        if($id!=''){
            $product_result = Products::with('category')->find($id);
            $productSubCategoryResult = ProductCategoryModel::get(["category_name", "id"])->where('procat_id',$product_result->product_catid);
            $productCode = $product_result->product_code;
            $formUrl = 'superadmin.product.update';
            if($userType!=config('custom.superadminrole')){
                $formUrl = 'product.update';
            }
        }
        
        return view('pages.products.create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'pageTitle'=>$pageTitle,'companies'=>$companies,'product_result'=>$product_result,'userType'=>$userType,'productCategoryResult'=>$productCategoryResult,'productSubCategoryResult'=>$productSubCategoryResult,'foodTypeResult'=>$foodTypeResult,'productCode'=>$productCode,'formUrl'=>$formUrl,'productsVariationsOptions'=>$productsVariationsOptions,'deleteImageUrl'=>$deleteImageUrl,'product_variation_type'=>$product_Variation_type]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

       //echo '<pre>'; print_r($request->all()); die;

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|max:250',
            'product_code' => 'required|unique:products',
            'company' => 'required',
        ]);
        
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        $userType = auth()->user()->role()->first()->name;
        $product_info = [
            'product_name'=>$request->product_name,
            'product_code'=>$request->product_code,
            'product_catid'=>($request->product_catid!='') ? $request->product_catid : 0,
            'product_subcatid'=>($request->product_subcatid!='') ? $request->product_subcatid : 0,
            'food_type'=>($request->food_type!='') ? $request->food_type : 'veg',
            'blocked'=>$request->blocked,
            'description'=>$request->description,
            'product_slug'=>str_replace(' ','-',$request->product_name),
        ];
        $redirectUrl = 'superadmin.product.index';
        if(isset($userType) && $userType!=config('custom.superadminrole')){
            $redirectUrl = 'product.index';
        }
        $product = Products::create($product_info);
        $product->company()->attach($request->company);
        
        
        if($request->has('product_image')) {
            $allowedfileExtension=['pdf','jpg','png'];
            $folder = storage_path('/product/images/');
            
            if (!File::exists($folder)) {
            File::makeDirectory($folder, $mode = 0777, true, true);
            }
            
            foreach ($request->file('product_image') as $key => $value) {

                $file= $value;
                $extension =  $file->getClientOriginalExtension();

                $check=in_array($extension, $allowedfileExtension);
                
                if($check) {
                    $filename = time(). $file->getClientOriginalName();
                    $location = storage_path('/product/images/'.$filename);
                    Image::make($file)->resize(800,400,function ($constraint) {
                            $constraint->aspectRatio();                 
                        })->save($location);
                    ProductImagesModel::create(['product_id'=>$product->id,'image'=>$filename,'image_order'=>1]);
                } 
            }
            
        }
        
        
        if($request->has('variation')){
            $product_variation = [];
            $pro_new1 ='pro_new';
            foreach($request->variation as $key => $variation_val){
                
                
                for($i=0; $i<count($request->variation[$key]); $i++){
                    $product_variation[$i]['product_id'] = $product->id;
                    $product_variation[$i][$key] = $request->variation[$key][$i];
                    $product_variation[$i]['variation_type'] = isset($request->variation['variation_type'][$i]) ? $request->variation['variation_type'][$i] : '';

                    $product_variation[$i]['main_price'] = 0;
                    $product_variation[$i]['offer_price'] = 0; 

                }
                if(count($request->variation[$key])<count($request->variation['name'])){
                    $index = count($request->variation['name'])-count($request->variation[$key]);
                    $product_variation[$index][$key] = 0;
                    $product_variation[$i]['main_price'] = 0;
                    $product_variation[$i]['offer_price'] = 0; 

                }
                

            }
            
        }
        $product_variation_result = ProductsVariations::insert($product_variation);
        
        // if ($request->has('variation')) {
        //     $product_variation = [];
        
        //     foreach ($request->variation as $key => $variation_val) {
        //         //echo '<pre>'; print_r($key); die;
        //         for ($i = 0; $i < count($request->variation[$key]); $i++) {
        //             // $product_variation[$i]['product_id'] = $product->id;
        //             $product_variation[$i]['name'] = $request->variation[$key];

        //             $product_variation[$i]['main_price'] = 0; // Set a default value for main_price
        //             $product_variation[$i]['offer_price'] = 0; 
        //         }
        
        //         if (count($request->variation[$key]) < count($request->variation['name'])) {
        //             $index = count($request->variation['name']) - count($request->variation[$key]);
        //             $product_variation[$index][$key] = 0;
        //             $product_variation[$index]['main_price'] = 0; // Set a default value for 
        //             $product_variation[$index]['offer_price'] = 0;
        //         }
        //     }
        // }
        
        // $product_variation_result = ProductsVariations::insert($product_variation);
        
        return redirect()->route($redirectUrl)->with('success',__('locale.success common add'));
        
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
        $userType = auth()->user()->role()->first()->name;
        $product_result=$states=$productSubCategoryResult=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "product", 'name' => __('locale.Items')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $listUrl = 'superadmin.product.index';
        $deleteImageUrl = 'superadmin/product/imagedelete';
        $companies = Company::get(["company_name", "id","company_code"]);
        $productCategoryResult = ProductCategoryModel::get(["category_name", "id"]);

        $product_variation_type =  ProductVariationType::get();
        
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'product.index';
            $deleteImageUrl = 'product/imagedelete';
            $company_id = Helper::loginUserCompanyId();
            $productCategoryResult = ProductCategoryModel::where('company_id',$company_id)->get(["category_name", "id"]);

            $product_variation_type =  ProductVariationType::where('company_id',$company_id)->get();
            
        }
        
        $productsVariationsOptions = ProductsVariationsOptions::get(['id','name']);
        // echo"<pre>"; print_r($productsVariationsOptions); die;
        $foodTypeResult = ['veg','non-veg'];
        $pageTitle = __('locale.Company Admin'); 
        $productCode = Helper::setNumber();
        $formUrl = 'superadmin.product.update';
        if($userType!=config('custom.superadminrole')){
        $formUrl = 'product.update';
        }
        if($id!=''){
            $product_result = Products::with('product_variation')->with('product_images')->find($id);
            if(!$product_result){
                return redirect()->route($listUrl)->with('error',__('locale.product edit error'));
            } 
            $productSubCategoryResult =  ProductSubCategory::where("procat_id",$product_result->product_catid)->get(["subcat_name", "id","procat_id"]);
            //ProductCategoryModel::get(["category_name", "id"])->where('procat_id',$product_result->product_catid);
            $productCode = $product_result->product_code;
            
        }
        
        // echo '<pre>'; print_r($product_variation_type); die; 

        return view('pages.products.create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'pageTitle'=>$pageTitle,'companies'=>$companies,'product_result'=>$product_result,'userType'=>$userType,'productCategoryResult'=>$productCategoryResult,'productSubCategoryResult'=>$productSubCategoryResult,'foodTypeResult'=>$foodTypeResult,'productCode'=>$productCode,'formUrl'=>$formUrl,'productsVariationsOptions'=>$productsVariationsOptions,'deleteImageUrl'=>$deleteImageUrl,'product_variation_type'=>$product_variation_type]);
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
        $userType = auth()->user()->role()->first()->name;
        if(Products::where('id',$id)->count()==0){
            return redirect()->route('product.index')->with('error',__('locale.product edit error'));
        }
        $product_info = [
            'product_name'=>$request->product_name,
            'product_code'=>$request->product_code,
            'product_catid'=>($request->product_catid!='') ? $request->product_catid : 0,
            'product_subcatid'=>($request->product_subcatid!='') ? $request->product_subcatid : 0,
            'food_type'=>($request->food_type!='') ? $request->food_type : 'veg',
            'blocked'=>$request->blocked,
            'description'=>$request->description,
        ];

        Products::where('id',$id)->update($product_info);

        $redirectUrl = 'superadmin.product.index';
        if($userType!=config('custom.superadminrole')){
            $redirectUrl = 'product.index';
        }

        if($request->has('product_image')) {
            $allowedfileExtension=['pdf','jpg','png'];
            $folder = storage_path('/product/images/');
            
            if (!File::exists($folder)) {
                File::makeDirectory($folder, $mode = 0777, true, true);
            }
            
            foreach ($request->file('product_image') as $key => $value) {

                $file= $value;
                $extension =  $file->getClientOriginalExtension();

                $check=in_array($extension, $allowedfileExtension);
                
                if($check) {
                    $filename = time(). $file->getClientOriginalName();
                    $location = storage_path('/product/images/'.$filename);
                    Image::make($file)->resize(800,400,function ($constraint) {
                            $constraint->aspectRatio();                 
                        })->save($location);
                    ProductImagesModel::create(['product_id'=>$id,'image'=>$filename,'image_order'=>1]);
                } 
            }
            
        }
        
        
        if($request->has('variation')){
            $product_variation = [];
            $pro_new1 ='pro_new';
            ProductsVariations::where('product_id',$id)->delete();
            foreach($request->variation as $key => $variation_val){
                
                
                for($i=0; $i<count($request->variation[$key]); $i++){
                    $product_variation[$i]['product_id'] = $id;
                    $product_variation[$i][$key] = $request->variation[$key][$i];
                    $product_variation[$i]['main_price'] = 0;
                    $product_variation[$i]['offer_price'] = 0; 


                }
                if(count($request->variation[$key])<count($request->variation['name'])){
                    $index = count($request->variation['name'])-count($request->variation[$key]);
                    $product_variation[$index][$key] = 0;
                    $product_variation[$i]['main_price'] = 0;
                    $product_variation[$i]['offer_price'] = 0; 

                }
                

            }
            $product_variation_result = ProductsVariations::insert($product_variation);
            
        }
        
        return redirect()->route($redirectUrl)->with('success',__('locale.success common update'));
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $cartList = Cartlist::whereRaw('FIND_IN_SET("1", product_ids)')->count();
        $orderList = Orderlist::whereRaw('FIND_IN_SET("1", product_ids)')->count();
        if($cartList==0 && $orderList==0){
            Products::where('id',$id)->delete();
            ProductsVariations::where('product_id',$id)->delete();
            ProductImagesModel::where('product_id',$id)->delete();
            return redirect()->back()->with('success',__('locale.product delete successmessage'));
        }else{
            return redirect()->back()->with('error',__('locale.product delete errormessage'));
        }

    }

    public function imagedelete($id){
        
        $productImage = ProductImagesModel::findOrFail($id);
        $productImage->delete();
        return response()->json(['status'=>true,'message'=>'deleted'],200);
    }

    public function productImport(){
        try{
            
            $import = new ProductsImport;
            Excel::import($import, request()->file('importfile'));
            // print_r($import); exit();
            return redirect()->back()->with('success', __('locale.import_message'));
        }catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            
            return redirect()->route('product')->with('error', __('locale.try_again'));
        }
    }
    public function productExport($type=''){
        if($type==config('custom.superadminrole')){
            $companyUser = new ProductsExport;
        }else{
            $companyUser = new UserProductsExport;
        }
        return Excel::download($companyUser, 'products-'.$type.time().'.xlsx');
    }
}
