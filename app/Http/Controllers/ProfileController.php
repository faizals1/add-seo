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
use Illuminate\Support\Facades\Hash;
use App\Models\{User,Role};



use Helper;
use File;
use Image;

class ProfileController extends Controller
{
 
    public function edit($id='')
    {
        $userType = auth()->user()->role()->first()->name;

         $login_id = auth()->user()->id;

        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "profile", 'name' => __('locale.Profile')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];

            $formUrl = 'superadmin.profile-edit';

            if($userType!=config('custom.superadminrole')){
                $formUrl = 'profile-edit';
            }

        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.profile');
        // $user_result = null;
     
        if($login_id!=''){
    
            $userType = auth()->user()->role()->first()->name;
            $formUrl = 'superadmin.profile-update';
            
            if($userType!=config('custom.superadminrole')){
                $formUrl = 'profile-update';
            }
            
            $user_result = User::find($login_id);
            // $formUrl = 'profile-update';
           
        }

        return view('pages.profile.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'userType'=>$userType,'formUrl'=>$formUrl,'user_result'=>$user_result]);
    }

    public function update(Request $request, $id){

        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'phone' => 'required|max:20',
            'address' => 'max:250',
            'password' => 'max:10',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }  
        
        unset($request['_method']);
        unset($request['_token']);
        unset($request['action']);
        
        // exit('abc');

        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.profile-edit';

        if($userType!=config('custom.superadminrole')){
            $listUrl = 'profile-edit';
        }

        if(isset($request['password']) && $request['password']!=''){
            $request['password'] = Hash::make($request['password']);
        }else{
            unset($request['password']);
        }

        // echo '<pre>'; print_r($request->all()); die;

        $login_id=auth()->user()->id;

        $user = User::where('id',$login_id)->update($request->all());

        return redirect()->route($listUrl)->with('success',__('locale.profile_success'));  
        
    }


}   