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
         
          <form class="formValidate" method="post" action="{{ isset($mappingResult[0]) ? route($formUrl, $mappingResult[0]->company_id) : route($formUrl) }}">

            @csrf

              @if(isset($mappingResult[0]))
                  @method('PUT') <!-- Use PUT for updating -->
              @endif

            

              <div class="input-field col s12">

                 <select name="company_id" id="company" required>
                  <option value="Select" disabled selected>{{__('locale.Select Company')}} *</option>
                  @if(isset($companyResult) && !empty($companyResult))
                  @foreach($companyResult as $company_val)
                  {{$company_val->id}}
                  <option value="{{ $company_val->id }}">{{ $company_val->company_name }}</option>
                    @endforeach
                  @endif
                </select>
                @error('company_id')
                <div style="color:red">{{$message}}</div>
                @enderror
             </div>             
              <?php //echo '<pre>';print_r($productIds); exit(); ?>
           <div class="row">
                <div class="row">
                  
                  <div class="input-field col m6 s12">
                    <p> <label>{{__('locale.Items')}}</label></p>
                    @if(isset($productResult) && !empty($productResult) && count($productResult)>0)
                    @foreach($productResult as $productValue)
                    <p><label>
                        <input type="checkbox" name="product_ids[]" value="{{ $productValue->id }}" {{ (isset($productIds) && !empty($productIds) && in_array($productValue->id,$productIds)) ? 'checked="checked"' : '' }}>
                        <span>{{ $productValue->product_name }}</span>
                      </label>
                    </p>
                      @endforeach
                    @endif
                    
                  </div>
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
    var company_value = "{{(isset($mappingResult[0]->company_id) && $mappingResult[0]->company_id!='NULL') ? $mappingResult[0]->company_id : old('company_id')}}";
    console.log('company_value',company_value);
    $('#company').val(company_value);
    $('#company').formSelect();
  }
  </script>
@endsection

