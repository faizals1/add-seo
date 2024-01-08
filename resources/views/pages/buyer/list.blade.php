{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@include('panels.page-title')

{{-- page style --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/flag-icon/css/flag-icon.min.css')}}">
@endsection

{{-- page content --}}
@section('content')
<div class="section">
  <div class="card">
    
  </div>
  
  <div class="row">
    <div class="col s12 m12 l12">
      <div id="responsive-table" class="card card card-default scrollspy">
        <div class="card-content">
            
          <div class="row">
            <div class="col s12">
            </div>
            <div class="col s12 table-result">
                <div class="input-field col m6 s12">
                  <label for="serach">{{__('locale.Search')}}</label>
                  <input id="serach" type="text" name="serach" data-error=".errorTxt12">
                  <small class="errorTxt12"></small>
                </div>
              <!-- <div class="col m3">
                <div class="form-group">
                  <input type="text" name="serach" id="serach" class="form-control" />
                </div>
              </div> -->
              @include('pages.buyer.buyer-table-list')
              <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
            </div>
          </div>
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
@endsection