@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@include('panels.page-title')

{{-- vendor style --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/flag-icon/css/flag-icon.min.css')}}">
@endsection

{{-- page content --}}
@section('content')
<div class="section">
  <div class="card">
    
  </div>

  <!-- HTML VALIDATION  -->


   <div class="row">
     <div class="col s12">
        @include('panels.flashMessages')

       <div id="validations" class="card card-tabs">
          <div class="card-content">
              <div class="card-title">
                <div class="row">
                  <div class="col s12 m6 l10">
                    
                  </div>
                </div>
              </div>
             <div id="view-validations">
                  <form class="formValidate" method="post" action="{{ isset($result) ? route($formUrl, $result['id']) : route($formUrl) }}">
              
                  @csrf

                    @if(isset($result))
                        @method('PUT') <!-- Use PUT for updating -->
                    @endif
                  

                    <div class="row">

                      @if(isset($userType) && $userType==config('custom.superadminrole'))
                          <div class="input-field col s12 m6">
                            <select name="company_id" id="company" required>
                              <option value="" disabled selected>Select Company</option>
                              @if(isset($company) && !empty($company))
                              @foreach($company as $company_val)
                              {{$company_val->id}}
                              <option value="{{ $company_val->id }}">{{ $company_val->company_name }}</option>
                                @endforeach
                              @endif
                            </select>
                            @error('company_id')
                            <div style="color:red">{{$message}}</div>
                            @enderror
                          </div> 
                        @else
                        <input type="hidden" name="company_id" value="{{Helper::loginUserCompanyId()}}"/>
                        @endif 
                     
                      <div class="input-field col m6 s12">
                        <label for="name">Name</label>
                        <input id="name" class="validate" name="name" type="text" data-error=".errorTxt1" value="{{ (isset($result['name']) && $result['name'] !='' ) ? $result['name'] :  old('name') }}">
                        @error('name')
                            <div style="color:red">{{ $message }}</div>
                        @enderror
                        <small class="errorTxt1"></small>
                      </div>
                    
                      <div class="input-field col s12">
                        <button class="btn waves-effect waves-light right submit" type="submit" name="action">Submit
                          <i class="material-icons right">send</i>
                        </button>
                      </div>
                    </div>
                  </form>
                </div>  
             </div>
          </div>
       </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
window.onload=function(){
    var company_value = "{{(isset($result->company_id) && $result->company_id!='NULL') ? $result->company_id : old('company_id')}}";
    console.log('company_value',company_value);
    $('#company').val(company_value);
    $('#company').formSelect();
  }
  </script>
@endsection

