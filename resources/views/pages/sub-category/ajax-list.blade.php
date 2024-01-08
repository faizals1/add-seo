<table class="table">
   <thead>
     <tr>
        <th data-field="s.no">{{__('locale.S.no')}}</th>
        <th data-field="category_name">{{__('locale.sub_category_name')}}</th>
        <th data-field="category_name">{{__('locale.category_name')}}</th>
        <th data-field="company_name">{{__('locale.company_name')}}</th>
        <th data-field="action">{{__('locale.action')}}</th>
     </tr>
   </thead>
   <tbody>
    @if(isset($sub_category_list) && !empty($sub_category_list))
        @foreach($sub_category_list as $key => $sub_category_data)

        <tr>
            <td>{{$key+1}}</td>
            <td>{{$sub_category_data->subcat_name}}</td> <!-- Handle empty data -->
            <td>{{$sub_category_data->categoryname->category_name}}</td>
            <td>{{$sub_category_data->categoryname->companyname->company_name}}</td>

            <td>
                <a href="{{route($editUrl,$sub_category_data->id)}}"><i class="material-icons">edit</i></a>
                <a href="{{route($deleteUrl,$sub_category_data->id)}}" onclick="return confirm('Are you sure?')"><i class="material-icons">delete</i></a>
            </td>    
        </tr>
        @endforeach
    @else
    <tr>
        <td colspan="10"><p class="center">{{__('locale.no_record_found')}}</p></td>
    </tr>
    @endif
   </tbody>
</table>

@if(isset($sub_category_list) && !empty($sub_category_list))
{!! $sub_category_list->links('panels.paginationCustom') !!}
@endif

