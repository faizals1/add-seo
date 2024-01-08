<table class="table">
  <thead>
    <tr>
      <th></th>
      <th>{{__('locale.company_name')}}</th>
      <th>{{__('locale.Items')}}</th>
      <th>{{__('locale.action')}}</th>
    </tr>
  </thead>
  <tbody>
    @if(isset($productMappingResult) && !empty($productMappingResult))
    @foreach($productMappingResult as $product_key => $product_map_value)
    <tr>
    <td>{{$product_key+1}}</td>
    <td>{{ isset($product_map_value->company->company_name) ? $product_map_value->company->company_name : '' }}</td>
    <td>{{ isset($product_map_value->product->product_name) ? $product_map_value->product->product_name : '' }}</td>
    
    <td>
      <a href="{{route($editUrl,$product_map_value->company_id)}}"><i class="material-icons">edit</i></a>
      <a href="{{route($deleteUrl,$product_map_value->id)}}" onclick="return confirm('Are you sure?')"><i class="material-icons">delete</i></a>
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
@if(isset($productMappingResult) && !empty($productMappingResult))
{!! $productMappingResult->links('panels.paginationCustom') !!}
@endif