<table class="table">
   <thead>
     <tr>
        <th data-field="s.no">{{__('locale.S.no')}}</th>
        <th data-field="name">{{__('locale.name')}}</th>
        <th data-field="company_name">{{__('locale.company_name')}}</th>
        <th data-field="action">{{__('locale.action')}}</th>
     </tr>
   </thead>
   <tbody>
        @if(isset($product_variation_List) && !empty($product_variation_List))
        @foreach($product_variation_List as $key => $product_variation_data)

        <tr>
            <td>{{$key+1}}</td>
            <td>{{$product_variation_data->name}}</td> <!-- Handle empty data -->
            <td>{{$product_variation_data->companyname->company_name}}</td>

            <td>
            <a href="{{route($editUrl,$product_variation_data->id)}}"><i class="material-icons">edit</i></a>
            <a href="{{route($deleteUrl,$product_variation_data->id)}}" onclick="return confirm('Are you sure?')"><i class="material-icons">delete</i></a>
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

@if(isset($product_variation_List) && !empty($product_variation_List))
{!! $product_variation_List->links('panels.paginationCustom') !!}
@endif