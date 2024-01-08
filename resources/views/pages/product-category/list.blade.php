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


  <!-- Responsive Table -->
  <div class="row">
    <div class="col s12 m12 l12">
      
      <div id="responsive-table" class="card card card-default scrollspy">
        @include('panels.flashMessages')
        <div class="card-content">
          <div class="card-title">
            <div class="row">
              <div class="col s12 m6 l10">
                <h4 class="card-title">{{__('locale.imports')}} {{__('locale.users')}}</h4>
              </div>
            </div>
          </div>
          <div id="view-file-input">
            <div class="row">
              <div class="col s12">
                <form action="{{route($importUrl)}}" method="post" enctype="multipart/form-data">
                  @csrf()
                  <div class="file-field input-field">
                    <div class="btn">
                      <span>File</span>
                      <input type="file" name="importfile" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    </div>
                    <div class="file-path-wrapper">
                      <input class="file-path validate" type="text">
                    </div>
                  </div>
                  <a class="waves-effect waves-light left submit" target="_blank" href="{{asset('data-import-files/product-category-import.csv')}}" download>{{__('locale.download_sample_file')}}
                      <i class="material-icons">download</i>
                  </a>
                  <button class="btn waves-effect waves-light right submit" type="submit" name="action">Submit
                      <i class="material-icons right">send</i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="card-content">
           
          <div class="row">
            <div class="col s12">
                  <div class="col s4">
                    @if($userType==config('custom.superadminrole'))
                    <select name="company_id" id="company_id">
                      <option value="Select" disabled selected>Select Company</option>
                      @foreach($company_list as $company)
                      <option value="{{$company->id}}" {{ (request()->route()->id == $company->id) ? 'selected' : '' }}>{{$company->company_name}}</option>
                      @endforeach
                    </select>
                    <input type="hidden" id="search" name="search"/>
                    @else
                    <label for="search">{{__('locale.search')}}</label>
                    <input id="search" type="text" name="search" data-error=".errorTxt12">
                    <small class="errorTxt12"></small>
                    @endif
                 </div>
            </div>
            <a class="btn waves-effect waves-light right" href="{{route($exportUrl,[$userType])}}">{{__('locale.export')}}
                <i class="material-icons right"></i>
            </a>
            <div class="col s12 table-result">
                
              <div class="responsive-table table-result" id="table-result">
                @include('pages.product-category.ajax-list')
                
              </div>
              <input type="hidden" name="hidden_page" id="hidden_page" value="1" />

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('page-script')
<script src="{{asset('js/scripts/page-users.js')}}"></script>
<script>
  $(document).ready(function(){
    var paginationUrl = "{{(isset($paginationUrl) && $paginationUrl!='') ? route($paginationUrl) : '' }}";
    const fetch_data = (page, status, seach_term) => {
        if(status === undefined){
            status = "";
        }
        if(seach_term === undefined){
            seach_term = "";
        }
        $.ajax({ 
            url: paginationUrl+"?page="+page+"&status="+status+"&seach_term="+seach_term,
            success:function(data){
              console.log(data);
                $('#table-result').html('');
                $('#table-result').html(data);
            }
        })
    }

    $('body').on('keyup', '#search', function(){
        var status = $('#status').val();
        var seach_term = $('#search').val();
        var page = $('#hidden_page').val();
        fetch_data(page, status, seach_term);
    });

    $('body').on('change', '#company_id', function(){
        var status = $('#users-list-status').val();
        var seach_term = $(this).val();
        console.log('seach_term',seach_term);
        $(this).parent().parent().find('#search').val(seach_term);
        var page = $('#hidden_page').val();
        fetch_data(page, status, seach_term);
    });

    $('body').on('click', '.pager a', function(event){
        console.log('ssss');
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        var search = $('#status').val();
        var seach_term = $('#company_id').parent().parent().find('#search').val();
        fetch_data(page,status, seach_term);
    });
});
</script>
@endsection