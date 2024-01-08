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
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Exports\AdminExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Permission;
use Helper;
use File;
use Image;


class UserController extends Controller
{

    public function index(Request $request)
    {
        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'company-admin-list';
        $deleteUrl = 'company-admin-delete';
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.Company Admin')], ['name' => __('locale.Company Admin').__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Company Admin';
        $usersResult = User::whereHas(
            'role', function($q){
                $q->where('name', 'company-admin');
            }
        )->select(['id','name','email','phone','address','image','website_url','blocked'])->orderBy('id','DESC');
        $editUrl = 'company-admin-edit';

        if($request->ajax()){
            $usersResult = $usersResult->when($request->seach_term, function($q)use($request){
                $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('name', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('email', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('phone', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('address', 'like', '%'.$request->seach_term.'%');
                        })
                        ->when($request->status, function($q)use($request){
                            $q->where('users.blocked',$request->status);
                        })
                        ->paginate($perpage);
                        
            return view('pages.users.users-list-ajax', compact('usersResult','editUrl','deleteUrl'))->render();
        }

        $usersResult = $usersResult->paginate($perpage);
        
        return view('pages.users.users-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'usersResult'=>$usersResult,'pageTitle'=>$pageTitle,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl]);
    }


    public function create($id='')
    {
        $userType = auth()->user()->role()->first()->name;
        $formUrl = 'company-admin-create';
        $user_result=$states=$cities=false;
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.Company Admin')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $pageTitle = __('locale.Company Admin'); 
        if($id!=''){
            $permission_arr = [];
            $user_result = User::with(['company','permission'])->find($id);
            if($user_result->permission->count()>0){
                foreach($user_result->permission as $permission_val){
                    $permission_arr[$permission_val->name][] = $permission_val->guard_name;
                }
            }
            $user_result->permission = $permission_arr;
            // echo '<pre>';print_r($user_result);exit();
            if($user_result){
            $states = State::where('country_id',$user_result->country)->get(["name", "id"]);
            $cities = City::where('state_id',$user_result->state)->get(["name", "id"]);
            }
            $formUrl = 'company-admin-update';
        }
        // dd($user_result->permission);
        return view('pages.users.users-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'user_result'=>$user_result,'states'=>$states,'cities'=>$cities,'userType'=>$userType,'formUrl'=>$formUrl]);
    }
    
    
    public function store(Request $request){
        
        //echo '<pre>';print_r($request->all()); exit();

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'email' => 'required|unique:users|max:250',
            'phone' => 'required|max:20',
            'address' => 'max:250',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        if ($request->hasFile('image')) {         
           $imageType = $request->file('image');
           $name = rand(10000, 99999) . time() . '.' . $imageType->getClientOriginalExtension();
           $destinationPath = storage_path('user/images/');
           $imageType->move($destinationPath, $name);
           $request->image = $name;
        }

        $role = Role::where('name', 'company-admin')->first();
        $random_password = Str::random(6);
        $request['password'] = Hash::make($random_password);
        $user = User::create($request->all());

        $id = $user->id;

        $user->company()->attach($request->company);
        $user->role()->attach( $role->id);

        if($request->has('permission_allow')){
            $i=0;
            $permissionInsert = [];
            foreach($request->input('permission_allow') as $key => $permissionVal){
                // echo '<pre>';print_r($permissionVal['guard_name']);
                if(isset($permissionVal['guard_name'])){
                    for($g=0;$g<count($permissionVal['guard_name']);$g++){
                        $permissionInsert[$i]['user_id'] = $id;
                        $permissionInsert[$i]['name'] = $key;
                        $permissionInsert[$i]['guard_name'] = $permissionVal['guard_name'][$g];
                        $i++;
                    }
                }
            }
            if(!empty($permissionInsert)){
                Permission::where('user_id',$user->id)->delete();
                Permission::insert($permissionInsert);
            }
        }
        
        return redirect()->route('company-admin-list')->with('success',__('locale.company_admin_create_success'));
    }

    public function update(Request $request, $id){

        $userType = auth()->user()->role()->first()->name;
        $listUrl = 'superadmin.product-subcategory.index';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'product-subcategory.index';
        }
        
        // echo '<pre>'; print_r($request->all()); die;
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'email' => 'required|max:250',
            'phone' => 'required|max:20',
            'address' => 'max:250',
            'password' => 'max:10',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        if(User::where('email',$request->email)->where('id','!=',$id)->count()>0){
            return redirect()->back()
            ->with('email','The Email Has Already Been Taken.')
            ->withInput();
        }
        
        unset($request['_method']);
        unset($request['_token']);
        unset($request['action']);
        unset($request['company']);
        unset($request['importcompany']);
        
        if ($request->has('permission_allow')) {
            Permission::where('user_id',$id)->delete();
            foreach ($request->input('permission_allow') as $key => $permissionVal) {
                //echo '<pre>'; print_r($permissionVal['guard_name']); die;  

                if (isset($permissionVal['guard_name'])) {
                    $guardNames = $permissionVal['guard_name'];
        
                    foreach ($guardNames as $guardName) {
                        Permission::updateOrInsert(
                            [
                                'name' => $key,
                                'guard_name' => $guardName,
                                'user_id' => $id,
                            ]
                          
                        );
                    }
                }
            }
        }
        
        unset($request['permission_allow']);
        if(isset($request['password']) && $request['password']!=''){
            $request['password'] = Hash::make($request['password']);
        }else{
            unset($request['password']);
        }

        $user = User::where('id',$id)->update($request->all());

        return redirect()->route('company-admin-list')->with('success',__('locale.company_admin_update_success'));
    }

    
    public function companyUserImport(){
        try{
            $import = new UsersImport;
            Excel::import($import, request()->file('importcompany'));
            // print_r($import); exit();
            return redirect()->back()->with('success', __('locale.import_message'));
        }catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            $userType = auth()->user()->role()->first()->name;
            $returnUrl = 'superadmin.company-user-list';
            if($userType!=config('custom.superadminrole')){
                $returnUrl = 'company-user-list';
            }
            return redirect()->route($returnUrl)->with('error', __('locale.try_again'));
        }
            
    }

    public function companyUserExport($type='superadmin') 
    {
        if($type=='superadmin'){
            $companyUser = new AdminExport;
            
        }else{
            $type = 'user';
            $companyUser = new UsersExport;
        }
        
        return Excel::download($companyUser, 'company-'.$type.time().'.xlsx');
    }

    


    public function usersList(Request $request)
    {
        $userType = auth()->user()->role()->first()->name;
        $deleteUrl = 'superadmin.company-user-delete';
        $perpage = config('app.perpage');
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.Company User')], ['name' => __('locale.Company User').__('locale.List')]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = 'Company User';
        $paginationUrl = 'superadmin.company-user-list';
        $editUrl = 'superadmin.company-user-edit';
        
        if($userType!=config('custom.superadminrole')){
            $paginationUrl = 'superadmin.company-user-list';
            $editUrl = 'company-user-edit';
            $deleteUrl = 'company-user-delete';
        }
        
        $usersResult = User::with('company')->whereHas(
            'role', function($role_q){
                $role_q->where('name', 'company-user');
            }
        )->select(['name','email','phone','address','image','website_url','id','blocked'])->orderBy('id','DESC');
        $currentPage = 1;
        if($request->ajax()){
            
            $currentPage = $request->get('page');
            $usersResult = $usersResult->when($request->seach_term, function($q)use($request){
                $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('name', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('email', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('phone', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('address', 'like', '%'.$request->seach_term.'%');
                        })
                        ->when($request->status, function($q)use($request){
                            $q->where('users.blocked',$request->status);
                        })
                        ->paginate($perpage);
                        
            return view('pages.users.users-list-ajax', compact('usersResult','currentPage','editUrl','deleteUrl'))->render();
        }

        $usersResult = $usersResult->paginate($perpage);
        
        return view('pages.users.users-list', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'usersResult'=>$usersResult,'pageTitle'=>$pageTitle,'paginationUrl'=>$paginationUrl,'currentPage'=>$currentPage,'userType'=>$userType,'editUrl'=>$editUrl,'deleteUrl'=>$deleteUrl]);
    }

    public function usersCreate($id='')
    {
        
        $user_result=$states=$cities=false;
        $userType = auth()->user()->role()->first()->name;
        
        $breadcrumbs = [
            ['link' => "modern", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => __('locale.Company User')], ['name' => (($id!='') ? __('locale.Edit') : __('locale.Create') )]];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $formUrl = 'superadmin.company-user-create';
        if($userType!=config('custom.superadminrole')){
            $company_id = Helper::loginUserCompanyId();
            $formUrl = 'company-user-create';
        }
        $countries = Country::get(["name", "id"]);
        $companies = Company::get(["company_name", "id","company_code"]);
        $pageTitle = __('locale.Company User    '); 
        if($id!=''){

            $permission_arr = [];
            $user_result = User::with(['company','permission'])->find($id);
            if($user_result->permission->count()>0){
                foreach($user_result->permission as $permission_val){
                    $permission_arr[$permission_val->name][] = $permission_val->guard_name;
                }
            }
            $user_result->permission = $permission_arr;

            // $user_result = User::with('company')->find($id);
            if($user_result){
                $states = State::where('country_id',$user_result->country)->get(["name", "id"]);
                $cities = City::where('state_id',$user_result->state)->get(["name", "id"]);
            }

            $formUrl = 'superadmin.company-user-update';

            if($userType!=config('custom.superadminrole')){
                $company_id = Helper::loginUserCompanyId();
                $formUrl = 'company-user-update';
            }
        }
   
        return view('pages.users.users-create', ['pageConfigs' => $pageConfigs], ['breadcrumbs' => $breadcrumbs,'countries'=>$countries,'pageTitle'=>$pageTitle,'companies'=>$companies,'user_result'=>$user_result,'states'=>$states,'cities'=>$cities,'formUrl'=>$formUrl,'userType'=>$userType]);
    }

    public function usersUpdate(Request $request, $id){
        $userType = auth()->user()->role()->first()->name;
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'email' => 'required|max:250',
            'phone' => 'required|max:20',
            'address' => 'max:250',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        if(User::where('email',$request->email)->where('id','!=',$id)->count()>0){
            return redirect()->back()
            ->with('email','The Email Has Already Been Taken.')
            ->withInput();
        }
        
        unset($request['_method']);
        unset($request['_token']);
        unset($request['action']);
        unset($request['company']);
        unset($request['importcompany']);
             
        $listUrl = 'superadmin.company-user-list';
        if($userType!=config('custom.superadminrole')){
            $listUrl = 'company-user-list';
        }

        // if ($request->has('permission_allow')) {
        //     Permission::where('user_id',$id)->delete();
        //     foreach ($request->input('permission_allow') as $key => $permissionVal) {
        //         //echo '<pre>'; print_r($permissionVal); die;  

        //         if (isset($permissionVal['guard_name'])) {
        //             $guardNames = $permissionVal['guard_name'];
        
        //             foreach ($guardNames as $guardName) {
        //                 Permission::updateOrInsert(
        //                     [
        //                         'name' => $key,
        //                         'guard_name' => $guardName,
        //                         'user_id' => $id,
        //                     ]
                          
        //                 );
        //             }
        //         }
        //     }
        // }

        // exit('abc');
    
        if ($request->has('permission_allow')) {
            Permission::where('user_id',$id)->delete();
            foreach ($request->input('permission_allow') as $key => $permissionVal) {
                //echo '<pre>'; print_r($permissionVal['guard_name']); die;  

                if (isset($permissionVal['guard_name'])) {
                    $guardNames = $permissionVal['guard_name'];
        
                    foreach ($guardNames as $guardName) {
                        Permission::updateOrInsert(
                            [
                                'name' => $key,
                                'guard_name' => $guardName,
                                'user_id' => $id,
                            ]
                          
                        );
                    }
                }
            }
        }
        
        unset($request['permission_allow']);
        if(isset($request['password']) && $request['password']!=''){
            $request['password'] = Hash::make($request['password']);
        }else{
            unset($request['password']);
        }

        $user = User::where('id',$id)->update($request->all());

        return redirect()->route($listUrl)->with('success',__('locale.company_user_update_success'));
    }

    public function userStore(Request $request){
        $userType = auth()->user()->role()->first()->name;
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'email' => 'required|unique:users|max:250',
            'phone' => 'required|max:20',
            'address' => 'max:250',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        $role = Role::where('name', 'company-user')->first();
        $random_password = Str::random(6);
        $request['password'] = Hash::make($random_password);
        $user = User::create($request->all());
        $id   = $user->id;
        $user->company()->attach($request->company);
        $user->role()->attach( $role->id);
        $listUrl = 'superadmin.company-user-list';
        if($userType!=config('custom.superadminrole')){
            
            $listUrl = 'company-user-list';
        }
        
        if($request->has('permission_allow')){
            $i=0;
            $permissionInsert = [];
            foreach($request->input('permission_allow') as $key => $permissionVal){
                // echo '<pre>';print_r($permissionVal['guard_name']);
                if(isset($permissionVal['guard_name'])){
                    for($g=0;$g<count($permissionVal['guard_name']);$g++){
                        $permissionInsert[$i]['user_id'] = $id;
                        $permissionInsert[$i]['name'] = $key;
                        $permissionInsert[$i]['guard_name'] = $permissionVal['guard_name'][$g];
                        $i++;
                    }
                }
            }
            if(!empty($permissionInsert)){
                Permission::where('user_id',$user->id)->delete();
                Permission::insert($permissionInsert);
            }
        }

        return redirect()->route($listUrl)->with('success',__('locale.company_user_create_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        
        $companyId = companyUserMapping::where('user_id',$id)->first()->company_id;
        if(companyUserMapping::where('company_id',$companyId)->where('user_id','!=',$id)->count()==0){
            if(User::where('id',$id)->delete()){
                return redirect()->back()->with('success',__('locale.delete_message'));
            }else{
                return redirect()->back()->with('error',__('locale.try_again'));
            }
        }else{
            return redirect()->back()->with('error',__('locale.company_admin_delete_error_msg'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyUser($id)
    {   
        if(User::where('id',$id)->delete()){
            return redirect()->back()->with('success',__('locale.delete_message'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
    }

}
