{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@include('panels.page-title')

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/select2/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/select2/select2-materialize.css')}}">
@endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('css/pages/page-users.css')}}">
@endsection

{{-- page content --}}
@section('content')
<!-- users edit start -->
<div class="section users-edit">
  <div class="card">
    <div class="card-content">
      <!-- <div class="card-body"> -->
      
      <div class="row">
        <div class="col s12" id="account">
          
          <!-- users edit media object ends -->
          <!-- users edit account form start -->
          @include('panels.flashMessages')
          @if(isset($user_result->id))
          <?php //$formUrl = (isset($formUrl) && $formUrl!='') ? $formUrl : 'company-admin-update'; ?>
            <form class="formValidate" action="{{route($formUrl,$user_result->id)}}" id="formValidateCompany" method="post">
            {!! method_field('post') !!}
            @else
            <?php //$formUrl = (isset($formUrl) && $formUrl!='') ? $formUrl : 'company-admin-create'; ?>
          <form id="accountForm" action="{{route($formUrl)}}" method="post">
            @endif
            @csrf()
            <div class="row">
              <div class="col s12 m12">
                <div class="row">
                  @if(isset($userType) && $userType==config('custom.superadminrole'))
                  <div class="col s12 input-field">
                    <select class="error" id="company" name="company" data-error=".errorTxt7" required>
                      <option value="">Choose {{__('locale.Company')}}</option>
                      @if(isset($companies) && !empty($companies))
                        @foreach ($companies as $company_value)
                          <option value="{{$company_value->id}}">{{$company_value->company_name}} ({{$company_value->company_code}})</option>
                        @endforeach
                      @endif
                    </select>
                    <label for="company">{{__('locale.Companies')}}</label>
                    <small class="errorTxt7"></small>
                  </div>
                  @else
                  <input type="hidden" name="company" value="{{Helper::loginUserCompanyId()}}"/>
                  @endif
                  <div class="col s12 m6 input-field">
                    <input id="name" name="name" type="text" class="validate" value="{{(isset($user_result->name)) ? $user_result->name : old('name')}}"
                      data-error=".errorTxt2">
                    <label for="name">{{__('locale.name')}}</label>
                    <small class="errorTxt2"></small>
                  </div>
                  <div class="col s12 m6 input-field">
                    <input id="email" name="email" type="email" class="validate" {{(isset($user_result->email)) ? 'readonly' : '' }} value="{{(isset($user_result->email)) ? $user_result->email : old('email')}}"
                      data-error=".errorTxt3">
                    <label for="email">{{__('locale.email')}}</label>
                    <small class="errorTxt3"></small>
                  </div>
                  <div class="col s12 m6 input-field">
                    <input id="phonenumber" type="text" name="phone" oninput="this.value=this.value.replace(/[^0-9.,]/g,'');" class="validate" value="{{(isset($user_result->phone)) ? $user_result->phone : old('phone')}}" data-error=".errorTxt4">
                    <label for="phonenumber">{{__('locale.phone')}}</label>
                    <small class="errorTxt4"></small>
                  </div>
                  <div class="col s12 m6 input-field">
                    <input id="address" name="address" type="text" class="validate" data-error=".errorTxt5" value="{{(isset($user_result->address)) ? $user_result->address : old('address')}}">
                    <label for="address">{{__('locale.address')}}</label>
                    <small class="errorTxt5"></small>
                  </div>
                  <div class="col s12 m4 input-field">
                    <select name="country" id="country">
                      <option value="">Choose {{__('locale.country')}}</option>
                      @if(isset($countries) && !empty($countries))
                        @foreach ($countries as $country_value)
                          <option value="{{$country_value->id}}">{{$country_value->name}}</option>
                        @endforeach
                      @endif
                    </select>
                    <label for="country">{{__('locale.country')}}</label>
                  </div>
                  <div class="col s12 m4 input-field">
                    <select name="state" id="state">
                    <option value="">Choose {{__('locale.state')}}</option>
                      @if(isset($user_result->state) && isset($states) && !empty($states))
                        @foreach ($states as $state_value)
                          <option value="{{$state_value->id}}">{{$state_value->name}}</option>
                        @endforeach
                      @endif
                    </select>
                    <label for="state">{{__('locale.state')}}</label>
                  </div>
                  <div class="col s12 m4 input-field">
                    <select name="city" id="city">
                      <option value="">Choose {{__('locale.city')}}</option>
                      @if(isset($user_result->city) && isset($cities) && !empty($cities))
                        @foreach ($cities as $city_value)
                          <option value="{{$city_value->id}}">{{$city_value->name}}</option>
                        @endforeach
                      @endif
                    </select>
                    <label for="city">{{__('locale.city')}}</label>
                  </div>
                  <div class="col s12 m6 input-field">
                    <input id="website_url" name="website_url" type="text" value="{{(isset($user_result->website_url)) ? $user_result->website_url : old('website_url')}}">
                    <label for="website_url">{{__('locale.website_url')}}</label>
                  </div>
                  
                  <div class="col s12 m6 input-field">
                    <select name="blocked">
                      <option value="1">Blocked</option>
                      <option value="0">Un-Blocked</option>
                    </select>
                    <label>{{__('locale.status')}}</label>
                  </div>
                  @if(isset($user_result->name) && $user_result->name!='')
                  <div class="col s12 m6 input-field">
                    <input id="password" name="password" type="password">
                    <label for="password">{{__('locale.Password')}}</label>
                  </div>
                  @endif
                  <div class="col s12 m6 input-field">
                    <div class="file-field input-field">
                      <div class="btn">
                        <span>File</span>
                        <input type="file" name="image" accept="image/*">
                      </div>
                      <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                      </div>
                    </div>
                  </div>
                  <!-- permission start -->
                  <div class="col s12">
                    <table class="mt-1">
                      <thead>
                        <tr>
                          <th>Module Permission</th>
                          <th>Read</th>
                          <th>Create</th>
                          <th>Update</th>
                          <th>Delete</th>
                        </tr>
                      </thead>
                      <tbody>

                      <?php //echo '<pre>'; print_r($user_result); die; ?>
                        @if(config('custom.modulePermissionArray') && is_array(config('custom.modulePermissionArray')))
                        @foreach(config('custom.modulePermissionArray') as $key => $moduleValue)
                        
                        <?php //echo '<pre>'; print_r($user_result->permission); ?>
                        <tr>
                          <td>{{ucwords(str_replace('_',' ',$moduleValue))}}</td>
                          <td>
                            <label>
                            <input type="checkbox" name="permission_allow[{{ $moduleValue }}][guard_name][]" value="read" {{ (isset($user_result->permission[$moduleValue]) && !empty($user_result->permission[$moduleValue]) && in_array('read',$user_result->permission[$moduleValue])) ? 'checked="checked"' :'' }}/>
                              <span></span>
                          </label>
                          </td>
                         
                          <?php //echo '<pre>'; print_r($user_result->permission[$key]->name); ?>
                          <td>
                            <label>
                              
                              <input type="checkbox" name="permission_allow[{{ $moduleValue }}][guard_name][]" value="create" {{ (isset($user_result->permission[$moduleValue]) && !empty($user_result->permission[$moduleValue]) && in_array('create',$user_result->permission[$moduleValue])) ? 'checked="checked"' :'' }}/>
                              
                              <span></span>
                            </label>
                          </td>
                          <td>
                            <label>
                             
                              <input type="checkbox" name="permission_allow[{{ $moduleValue }}][guard_name][]" value="update" {{ (isset($user_result->permission[$moduleValue]) && !empty($user_result->permission[$moduleValue]) && in_array('update',$user_result->permission[$moduleValue])) ? 'checked="checked"' :'' }}/>
                             
                              <span></span>
                            </label>
                          </td>
                          <td>
                            <label>
                             
                              <input type="checkbox" name="permission_allow[{{ $moduleValue }}][guard_name][]" value="delete" {{ (isset($user_result->permission[$moduleValue]) && !empty($user_result->permission[$moduleValue]) && in_array('delete',$user_result->permission[$moduleValue])) ? 'checked="checked"' :'' }}/>
                              
                              <span></span>
                            </label>
                          </td>
                        </tr>
                        @endforeach
                        @endif
                      <?php /*
                      //echo '<pre>'; print_r($user_result); die; ?>
                        @if(isset($user_result->permission) && !empty($user_result->permission))

                        <?php //echo $editData = user_result) ?>
                        @foreach($user_result->permission as $moduleValue)

                        <tr>

                          <td>{{ucwords(str_replace('_',' ',$moduleValue->name))}}</td>

                          <td>
                          <label>
                              @if ($moduleValue->guard_name == 'read')
                              <input type="checkbox" name="permission_allow[{{ $moduleValue }}][guard_name][]" value="index" checked />
                              @else
                                  <input type="checkbox" name="permission_allow[{{ $moduleValue }}][guard_name][]" value="index" />
                              @endif
                              <span></span>
                          </label>
                          </td>
                          <?php //echo '<pre>'; print_r($moduleValue); die; ?>
                          <td>
                            <label>
                              
                              @if ($moduleValue->guard_name == 'create')
                              <input type="checkbox" name="permission_allow[{{ $moduleValue }}][guard_name][]" value="create" checked />
                              @else
                                  <input type="checkbox" name="permission_allow[{{ $moduleValue }}][guard_name][]" value="create" />
                              @endif
                              <span></span>
                            </label>
                          </td>
                          <td>
                            <label>
                             
                              @if ($moduleValue->guard_name == 'update')
                              <input type="checkbox" name="permission_allow[{{ $moduleValue }}][guard_name][]" value="update" checked />
                              @else
                                  <input type="checkbox" name="permission_allow[{{ $moduleValue }}][guard_name][]" value="update" />
                              @endif
                              <span></span>
                            </label>
                          </td>
                          <td>
                            <label>
                            
                              @if ($moduleValue->guard_name == 'delete')
                              <input type="checkbox" name="permission_allow[{{ $moduleValue }}][guard_name][]" value="delete" checked />
                              @else
                                  <input type="checkbox" name="permission_allow[{{ $moduleValue }}][guard_name][]" value="delete" />
                              @endif
                              <span></span>
                            </label>
                          </td>
                        </tr>
                        @endforeach
                        @endif
                        */ ?>
                      </tbody>
                    </table>
                    <!-- </div> -->
                  </div>
                  <!-- permission end -->
                </div>
              </div>
              
              <div class="col s12 display-flex justify-content-end mt-3">
                <button type="submit" class="btn indigo">
                  Save changes</button>
                <button type="reset" class="btn btn-light">Cancel</button>
              </div>
            </div>
          </form>
          <!-- users edit account form ends -->
        </div>
      </div>
      <!-- </div> -->
    </div>
  </div>
</div>
<!-- users edit ends -->
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('vendors/select2/select2.full.min.js')}}"></script>
<script src="{{asset('vendors/jquery-validation/jquery.validate.min.js')}}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
<script src="{{asset('js/scripts/page-users.js')}}"></script>
<script src="{{asset('js/scripts/form-validation.js')}}"></script>
<script>
  window.onload=function(){
    var country_value = "{{(isset($user_result->country) && $user_result->country!='NULL') ? $user_result->country : old('country')}}";
    var country_value_edit = "{{(isset($user_result->country) && $user_result->country!='NULL') ? $user_result->country : ''}}";
    var state_value = "{{(isset($user_result->state) && $user_result->state!='NULL') ? $user_result->state : old('state')}}";
    var city_value = "{{(isset($user_result->city) && $user_result->city!='NULL') ? $user_result->city : old('state')}}";
    var company_value = "{{(isset($user_result->company[0]->id) && $user_result->company[0]->id!='NULL') ? $user_result->company[0]->id : old('company')}}";
    console.log(state_value);
    $('#country').val(country_value);
    $('#country').formSelect();
    $('#state').val(state_value);
    $('#state').formSelect();
    $('#city').val(city_value);
    $('#city').formSelect();
    $('#company').val(company_value);
    if(country_value_edit && country_value_edit!=''){
      $('#company').attr('disabled',true);
    }
    $('#company').formSelect();
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
                            .id + '">' + value.name + '</option>');
                    });
                    $('#city').formSelect();
                }
            });
        });
    });
</script>
@endsection