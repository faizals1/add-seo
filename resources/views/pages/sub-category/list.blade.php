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
                  <a class="waves-effect waves-light left submit" target="_blank" href="{{asset('data-import-files/subcategory-importfile.csv')}}" download>{{__('locale.download_sample_file')}}
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
                    <select name="category_id" id="category_id">
                      <option value="Select" disabled selected>Select Category</option>
                      @foreach($category_list as $categoryname)
                      <option value="{{$categoryname->id}}" {{ (request()->route()->id == $categoryname->id) ? 'selected' : '' }}>{{$categoryname->category_name}}</option>
                      @endforeach
                    </select>
                  </div>
            </div>
            <a class="btn waves-effect waves-light right" href="{{route($exportUrl,[$userType])}}">{{__('locale.export')}}
                <i class="material-icons right"></i>
            </a>
            <div class="col s12 table-result">
                
              <div class="responsive-table table-result" id="sub_category_display">
                @include('pages.sub-category.ajax-list')
                
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
      console.log(seach_term);
        if(status === undefined){
            status = "";
        }
        if(seach_term === undefined || seach_term === null){
            seach_term = "";
        }
        $.ajax({ 
            url: paginationUrl+"?page="+page+"&status="+status+"&seach_term="+seach_term,
            success:function(data){
              console.log(data);
                $('#sub_category_display').html('');
                $('#sub_category_display').html(data);
            }
        })
    }

    $('body').on('keyup', '#serach', function(){
        var status = $('#status').val();
        var seach_term = $('#category_id').val();
        var page = $('#hidden_page').val();
        fetch_data(page, status, seach_term);
    });

    $('body').on('click', '.pager a', function(event){
        console.log('ssss');
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        var serach = $('#serach').val();
        var seach_term = $('#category_id').val();
        fetch_data(page,status, seach_term);
    });

    $('body').on('change', '#category_id', function() {
      
      var seach_term = $(this).val();
      var status=false;
      console.log('selectedCategoryId', seach_term);
    
      var url = $(this).data('url');
      console.log('url', url);

      var page = $('#hidden_page').val();
      fetch_data(page,status, seach_term);
    });

});
</script>




@endsection