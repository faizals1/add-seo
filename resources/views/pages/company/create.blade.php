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
      <div id="validations" class="card card-tabs">
        <div class="card-content">
          <div class="card-title">
            <div class="row">
              <div class="col s12 m6 l10">
                
              </div>
            </div>
          </div>
          <div id="view-validations">
            @include('panels.flashMessages')
            @if(isset($company_result))
            <form class="formValidate" action="{{route('company.update',$company_result->id)}}" id="formValidateCompany" method="post">
            {!! method_field('patch') !!}
            @else
            <form class="formValidate" action="{{route('company.store')}}" id="formValidateCompany" method="post">
            @endif
            @csrf()
              <div class="row">
                <div class="input-field col m6 s12">
                  <label for="company_name">{{__('locale.company_name')}}*</label>
                  <input id="company_name" class="validate" name="company_name" type="text" data-error=".errorTxt1" value="{{(isset($company_result->company_name)) ? $company_result->company_name : old('company_name')}}">
                  <small class="errorTxt1"></small>
                </div>
                <div class="input-field col m6 s12">
                  <label for="company_code">{{__('locale.company_code')}}*</label>
                  <input id="company_code" type="text" name="company_code" data-error=".errorTxt2" value="{{(isset($company_result->company_code)) ? $company_result->company_code : old('company_code')}}">
                  <small class="errorTxt2"></small>
                </div>
                <div class="input-field col s12">
                  <label for="address1">{{__('locale.address1')}}*</label>
                  <input id="address1" type="text" name="address1" data-error=".errorTxt3" value="{{(isset($company_result->address1)) ? $company_result->address1 : old('address1')}}">
                  <small class="errorTxt3"></small>
                </div>
                <div class="input-field col s12">
                  <label for="address2">{{__('locale.address2')}}</label>
                  <input id="address2" type="text" name="address2" data-error=".errorTxt4" value="{{(isset($company_result->address2) && $company_result->address2!='NULL') ? $company_result->address2 : old('address2')}}">
                  <small class="errorTxt4"></small>
                </div>
                <div class="col m4 s12">
                  <label for="country">{{__('locale.country')}} *</label>
                  <div class="input-field">
                    <select class="error" id="country" name="country" data-error=".errorTxt6">
                      <option value="">Choose {{__('locale.country')}}</option>
                      @if(isset($countries) && !empty($countries))
                        @foreach ($countries as $country_value)
                          <option value="{{$country_value->id}}">{{$country_value->name}}</option>
                        @endforeach
                      @endif
                    </select>
                    <small class="errorTxt6"></small>
                  </div>
                </div>
                <div class="col m4 s12">
                  <label for="state">{{__('locale.state')}} *</label>
                  <div class="input-field">
                    <select class="error" id="state" name="state" data-error=".errorTxt7">
                      <option value="">Choose {{__('locale.state')}}</option>
                      @if(isset($company_result->state) && isset($states) && !empty($states))
                        @foreach ($states as $state_value)
                          <option value="{{$state_value->id}}">{{$state_value->name}}</option>
                        @endforeach
                      @endif
                    </select>
                    <small class="errorTxt7"></small>
                  </div>
                </div>
                <div class="col m4 s12">
                  <label for="city">{{__('locale.city')}} *</label>
                  <div class="input-field">
                    <select class="error" id="city" name="city" data-error=".errorTxt8">
                      <option value="">Choose {{__('locale.city')}}</option>
                      @if(isset($company_result->city) && isset($cities) && !empty($cities))
                        @foreach ($cities as $city_value)
                          <option value="{{$city_value->id}}">{{$city_value->name}}</option>
                        @endforeach
                      @endif
                    </select>
                    <small class="errorTxt8"></small>
                  </div>
                </div>
                <!-- <div class="input-field col m4 s12">
                  <label for="country">{{__('locale.country')}}*</label>
                  <input id="country" type="text" name="country" data-error=".errorTxt6" value="{{(isset($company_result->country) && $company_result->country!='NULL') ? $company_result->country : old('country')}}">
                  <small class="errorTxt6"></small>
                </div>
                <div class="input-field col m4 s12">
                  <label for="state">{{__('locale.state')}}*</label>
                  <input id="state" type="text" name="state" data-error=".errorTxt7" value="{{(isset($company_result->state) && $company_result->state!='NULL') ? $company_result->state : old('state')}}">
                  <small class="errorTxt7"></small>
                </div>
                <div class="input-field col m4 s12">
                  <label for="city">{{__('locale.city')}}*</label>
                  <input id="city" type="text" name="city" data-error=".errorTxt7" value="{{(isset($company_result->city) && $company_result->city!='NULL') ? $company_result->city : old('city')}}">
                  <small class="errorTxt7"></small>
                </div>
                -->
                <div class="input-field col m6 s12">
                  <label for="pincode">{{__('locale.pin')}} *</label>
                  <input id="pincode" type="text" name="pincode" data-error=".errorTxt9" oninput="this.value=this.value.replace(/[^0-9.,]/g,'');" value="{{(isset($company_result->pincode)) ? $company_result->pincode : old('pincode')}}">
                  <small class="errorTxt9"></small>
                </div>
                <div class="input-field col m6 s12">
                  <label for="dob">{{__('locale.licence_valid_till')}} *</label>
                  <input type="text" class="datepicker" id="dob" name="licence_valid_till" data-error=".errorTxt10" value="{{(isset($company_result->licence_valid_till)) ? $company_result->licence_valid_till : old('licence_valid_till')}}">
                  <small class="errorTxt10"></small>
                </div>
                <div class="input-field col m6 s12">
                  <label for="contact_person">{{__('locale.contact_person')}} *</label>
                  <input id="contact_person" type="text" name="contact_person" data-error=".errorTxt11" value="{{(isset($company_result->contact_person)) ? $company_result->contact_person : old('contact_person')}}">
                  <small class="errorTxt11"></small>
                </div>
                <div class="input-field col m6 s12">
                  <label for="contact_mobile">{{__('locale.contact_mobile')}} *</label>
                  <input id="contact_mobile" type="text" name="contact_mobile" data-error=".errorTxt12" oninput="this.value=this.value.replace(/[^0-9.,]/g,'');" value="{{(isset($company_result->contact_mobile)) ? $company_result->contact_mobile : old('contact_mobile')}}">
                  <small class="errorTxt12"></small>
                </div>
                <div class="input-field col m6 s12">
                  <label for="phone_no">{{__('locale.phone_no')}}</label>
                  <input id="phone_no" type="text" name="phone_no" data-error=".errorTxt12" value="{{(isset($company_result->phone_no)) ? $company_result->phone_no : old('phone_no')}}">
                  <small class="errorTxt12"></small>
                </div>
                <div class="input-field col m6 s12">
                    <select name="blocked">
                    <option value="1" disabled selected>{{__('locale.blocked')}}</option>
                    <option value="0">{{__('locale.unblocked')}}</option>
                    </select>
                    <label>{{__('locale.blocked')}}</label>
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

{{-- vendor script --}}
@section('vendor-script')
<script src="{{asset('vendors/jquery-validation/jquery.validate.min.js')}}"></script>
@endsection

{{-- page script --}}
@section('page-script')
<script src="{{asset('js/scripts/form-validation.js')}}"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
<script>
  window.onload=function(){
    var country_value = "{{(isset($company_result->country) && $company_result->country!='NULL') ? $company_result->country : old('country')}}";
    var state_value = "{{(isset($company_result->state) && $company_result->state!='NULL') ? $company_result->state : old('state')}}";
    var city_value = "{{(isset($company_result->city) && $company_result->city!='NULL') ? $company_result->city : old('state')}}";
    console.log(state_value);
    $('#country').val(country_value);
    $('#country').formSelect();
    $('#state').val(state_value);
    $('#state').formSelect();
    $('#city').val(city_value);
    $('#city').formSelect();
  }
    $(document).ready(function () {
      

        $('#country').on('change', function () {
            var idCountry = this.value;
            console.log(idCountry);
            $("#state").html('');
            $.ajax({
                url: "{{url('api/user-fetch-states')}}",
                type: "POST",
                data: {
                    country_id: idCountry,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#state').html('<option value="">Select State</option>');
                    $.each(result.states, function (key, value) {
                        $("#state").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                    $('#state').formSelect();
                    $('#city').html('<option value="">Select City</option>');
                }
            });
        });
        $('#state').on('change', function () {
            var idState = this.value;
            $("#city").html('');
            $.ajax({
                url: "{{url('api/user-fetch-cities')}}",
                type: "POST",
                data: {
                    state_id: idState,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (res) {
                    $('#city').html('<option value="">Select City</option>');
                    $.each(res.cities, function (key, value) {
                        $("#city").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                    $('#city').formSelect();
                }
            });
        });
    });
</script>
@endsection

