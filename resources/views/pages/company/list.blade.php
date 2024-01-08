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
  
  <!-- File Input -->
<div class="row">
  <div class="col s12">
    <div id="file-input" class="card card-tabs">
      @include('panels.flashMessages')
      <div class="card-content">
        <div class="card-title">
          <div class="row">
            <div class="col s12 m6 l10">
              <h4 class="card-title">Import Company</h4>
            </div>
          </div>
        </div>
        <div id="view-file-input">
          <div class="row">
            <div class="col s12">
              <form action="{{route('company-import')}}" method="post" enctype="multipart/form-data">
                @csrf()
                <div class="file-field input-field">
                  <div class="btn">
                    <span>File</span>
                    <input type="file" name="importcompany" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                  </div>
                  <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                  </div>
                </div>
                <a class="waves-effect waves-light left submit" href="{{asset('data-import-files/companies-import.csv')}}">{{__('locale.download_sample_file')}}
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
    </div>
  </div>
</div>
  <!-- Responsive Table -->
  <div class="row">
    <div class="col s12 m12 l12">
      <div id="responsive-table" class="card card card-default scrollspy">
        <div class="card-content">
            <a class="btn waves-effect waves-light right" href="{{asset('company-export')}}">{{__('locale.export_company')}}
                <i class="material-icons right"></i>
            </a>
          <div class="row">
            <div class="col s12">
            </div>
            <div class="col s12 table-result">
                <div class="input-field col m6 s12">
                  <label for="serach">{{__('locale.search')}}</label>
                  <input id="serach" type="text" name="serach" data-error=".errorTxt12">
                  <small class="errorTxt12"></small>
                </div>
              <!-- <div class="col m3">
                <div class="form-group">
                  <input type="text" name="serach" id="serach" class="form-control" />
                </div>
              </div> -->
              @include('pages.company.company-table-list')
              <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
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
<script>
  $(document).ready(function(){
    const fetch_data = (page, status, seach_term) => {
        if(status === undefined){
            status = "";
        }
        if(seach_term === undefined){
            seach_term = "";
        }
        $.ajax({ 
            url:"/company?page="+page+"&status="+status+"&seach_term="+seach_term,
            success:function(data){
              console.log(data);
                $('.table-result table').html('');
                $('.table-result table').html(data);
            }
        })
    }

    $('body').on('keyup', '#serach', function(){
        var status = $('#status').val();
        var seach_term = $('#serach').val();
        var page = $('#hidden_page').val();
        fetch_data(page, status, seach_term);
    });

    $('body').on('change', '#status', function(){
        var status = $('#status').val();
        var seach_term = $('#serach').val();
        var page = $('#hidden_page').val();
        fetch_data(page, status, seach_term);
    });

    $('body').on('click', '.pager a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        var serach = $('#serach').val();
        var seach_term = $('#status').val();
        fetch_data(page,status, seach_term);
    });
});
</script>
@endsection