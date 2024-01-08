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
                      <div class="input-field col s12 m6">
                          <select name="category_id" id="category" required>
                            <option value="" disabled selected>Select Category</option>
                            @if(isset($category) && !empty($category))
                            @foreach($category as $categoryname)
                            {{$categoryname->id}}
                            <option value="{{ $categoryname->id }}">{{ $categoryname->category_name }}</option>
                            @endforeach
                            @endif
                          </select>
                          @error('category_id')
                          <div style="color:red">{{$message}}</div>
                          @enderror
                      </div>
                
                      <div class="input-field col m6 s12">
                        <label for="subcat_name">Sub Category Name</label>
                        <input id="subcat_name" class="validate" name="subcat_name" type="text" data-error=".errorTxt1" value="{{ (isset($result['subcat_name']) && $result['subcat_name'] !='' ) ? $result['subcat_name'] :  old('subcat_name') }}">
                        @error('subcat_name')
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
    var category_value = "{{(isset($result->procat_id) && $result->procat_id!='NULL') ? $result->procat_id : old('procat_id')}}";
    console.log('category_value',category_value);
    $('#category').val(category_value);
    $('#category').formSelect();
  }
</script>
@endsection


