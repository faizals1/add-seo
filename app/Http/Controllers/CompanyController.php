<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Company,User,companyUserMapping,ProductCompanyMapping};
use Illuminate\Support\Facades\Validator;
use App\Imports\CompanyImport;
use App\Exports\CompanyExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\{Country, State, City};

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perpage = config('app.perpage');
        $companyResultResponse = [];
        // Breadcrumbs
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "company", 'name' => "Company"], ['name' => "List"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Company List');
        $companyResult = Company::with(['countryname','statename', 'cityname'])->select(['id','company_name','company_code','address1','country','state','city','contact_person','contact_mobile','licence_valid_till','blocked','phone_no'])->orderBy('id','DESC');

        if($request->ajax()){
            $companyResult = $companyResult->when($request->seach_term, function($q)use($request){
                            $q->where('id', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('company_name', 'like', '%'.$request->seach_term.'%')
                            ->orWhere('company_code', 'like', '%'.$request->seach_term.'%');
                        })
                        ->when($request->status, function($q)use($request){
                            $q->where('status',$request->status);
                        })
                        ->paginate($perpage);
            return view('pages.company.company-table-list', compact('companyResult'))->render();
        }
        if($companyResult->count()>0){
            $companyResultResponse = $companyResult->paginate($perpage);;
        }
        // dd($companyResultResponse);
        // echo '<pre>';print_r($companyResultResponse[0]->cityname);exit();
        return view('pages.company.list',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'companyResult'=>$companyResultResponse]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "company", 'name' => "Company"], ['name' => "Add"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Company Add');
        $countries = Country::get(["name", "id"]);
        return view('pages.company.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'countries'=>$countries]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|max:250',
            'company_code' => 'required|max:10',
            'address1' => 'required|max:250',
            'address2' => 'max:250',
            'pincode' => 'required',
            'contact_person' => 'required|max:250',
            'contact_mobile' => 'required|max:20',
            'licence_valid_till' => 'required|date'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        // echo '<pre>';print_r($request->all());  exit();
        $company = Company::create($request->all());
        if($company){
            return redirect()->route('company.index')->with('success',__('locale.company_create_success'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        exit('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Breadcrumbs
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"], ['link' => "buyer", 'name' => "Buyer"], ['name' => "Add"],
        ];
        //Pageheader set true for breadcrumbs
        $pageConfigs = ['pageHeader' => true];
        $pageTitle = __('locale.Company Add');
        $company_result = Company::find($id);
        $countries = Country::get(["name", "id"]);
        $states = State::where('country_id',$company_result->country)->get(["name", "id"]);
        $cities = City::where('state_id',$company_result->state)->get(["name", "id"]);
        if(!$company_result){
            return redirect('/company')->with('error','Company id not match');
        }
        return view('pages.company.create',['breadcrumbs' => $breadcrumbs], ['pageConfigs' => $pageConfigs,'pageTitle'=>$pageTitle,'company_result'=>$company_result,'countries'=>$countries,'states'=>$states,'cities'=>$cities]);
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
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|max:250',
            'company_code' => 'required|max:10',
            'address1' => 'required|max:250',
            'address2' => 'max:250',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pincode' => 'required',
            'contact_person' => 'required|max:250',
            'contact_mobile' => 'required|max:20',
            'licence_valid_till' => 'required|date'
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
        $company = Company::where('id',$id)->update($request->input());
        if($company){
            return redirect()->route('company.index')->with('success',__('locale.company_update_success'));
        }else{
            return redirect()->back()->with('error',__('locale.try_again'));
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(companyUserMapping::where('company_id',$id)->count()==0 && ProductCompanyMapping::where('company_id',$id)->count()==0){
            if(Company::where('id',$id)->delete()){
                return redirect()->back()->with('success',__('locale.delete_message'));
            }else{
                return redirect()->back()->with('error',__('locale.try_again'));
            }
        }else{
            return redirect()->back()->with('error',__('locale.company_delete_error_msg'));
        }
    }

    public function companyImport(Request $request){
        try{
            $import = new CompanyImport;
            Excel::import($import, request()->file('importcompany'));
            return redirect()->route('company.index')->with('success', __('locale.import_message'));
        }catch(\Maatwebsite\Excel\Validators\ValidationException $e){
            
            return redirect()->route('company.index')->with('error', __('locale.try_again'));
        }
            
    }

    public function companyExport() 
    {
        return Excel::download(new CompanyExport, 'company-'.time().'.xlsx');
    }

    public function fetchState(Request $request)
    {
        $data['states'] = State::where("country_id",$request->country_id)->get(["name", "id"]);
        return response()->json($data);
    }
    public function fetchCity(Request $request)
    {
        $data['cities'] = City::where("state_id",$request->state_id)->get(["name", "id"]);
        return response()->json($data);
    }
}

