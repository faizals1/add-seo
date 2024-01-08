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
            @if(isset($buyer_result))
            <form class="formValidate" action="{{route('buyer-type.update',$buyer_result->id)}}" id="formValidateCompany" method="POST">
            {!! method_field('patch') !!}
            @else
            <form class="formValidate" action="{{route('buyer-type.store')}}" id="formValidateCompany" method="POST">
            {!! method_field('post') !!}
            @endif
            @csrf()
              <div class="row">
                <div class="input-field col m6 s12">
                  <label for="group_code">{{__('locale.group_code')}}*</label>
                  <input id="group_code" class="validate" name="group_code" type="text" data-error=".errorTxt1" value="{{(isset($buyer_result->group_code)) ? $buyer_result->group_code : old('group_code')}}">
                  <small class="errorTxt1"></small>
                </div>
                <div class="input-field col m6 s12">
                  <label for="group_name">{{__('locale.group_name')}}*</label>
                  <input id="group_name" type="text" name="group_name" data-error=".errorTxt2" value="{{(isset($buyer_result->group_name)) ? $buyer_result->group_name : old('group_name')}}">
                  <small class="errorTxt2"></small>
                </div>
                
                <div class="col m6 s12">
                  <label for="type">{{__('locale.type')}} *</label>
                  <div class="input-field">
                    <select class="error" id="type" name="type" data-error=".errorTxt6" required>
                      <option value="">Choose {{__('locale.type')}}</option>
                      
                          <option value="Domestic">Domestic</option>
                          <option value="Foreign">Foreign</option>
                        
                    </select>
                    <small class="errorTxt6"></small>
                  </div>
                </div>
                <div class="col m6 s12">
                  <label for="state">{{__('locale.currency_code')}} *</label>
                  <div class="input-field">
                    <select class="error" id="currency_code" name="currency_code" data-error=".errorTxt7" required>
                      <option value="">Choose {{__('locale.currency_code')}}</option>
                      
                          <option value="INR">INR</option>
                          <option value="USD">USD</option>
                        
                    </select>
                    <small class="errorTxt7"></small>
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
    var type_value = "{{(isset($buyer_result->type) && $buyer_result->type!='NULL') ? $buyer_result->type : old('type')}}";
    var currency_value = "{{(isset($buyer_result->currency_code) && $buyer_result->currency_code!='NULL') ? $buyer_result->currency_code : old('currency_code')}}";
    // var city_value = "{{(isset($company_result->city) && $company_result->city!='NULL') ? $company_result->city : old('state')}}";
    // console.log(state_value);

    $('#type').val(type_value);
    $('#type').formSelect();

    $('#currency_code').val(currency_value);
    $('#currency_code').formSelect();

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
                url: "{{url('api/fetch-states')}}",
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
                url: "{{url('api/fetch-cities')}}",
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
                            .name + '">' + value.name + '</option>');
                    });
                    $('#city').formSelect();
                }
            });
        });
    });
</script>
@endsection

