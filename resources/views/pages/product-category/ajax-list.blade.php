<table class="table">
    <thead>
    <tr>
        <th></th>
        <th>{{__('locale.category_name')}}</th>
        <th>{{__('locale.company_name')}}</th>
        <th>{{__('locale.action')}}</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($product_category_list) && !empty($product_category_list))
        @foreach($product_category_list as $key => $product_category_data)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$product_category_data->category_name}}</td>
            <td>{{ isset($product_category_data->companyname->company_name) ? $product_category_data->companyname->company_name : '' }}</td>
            <td>
            <a href="{{route($editUrl,$product_category_data->id)}}"><i class="material-icons">edit</i></a>
            <a href="{{route($deleteUrl,$product_category_data->id)}}" onclick="return confirm('Are you sure?')"><i class="material-icons">delete</i></a>
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
@if(isset($product_category_list) && !empty($product_category_list))
{!! $product_category_list->links('panels.paginationCustom') !!}
@endif