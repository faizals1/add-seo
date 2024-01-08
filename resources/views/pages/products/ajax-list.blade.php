<table class="table">
  <thead>
    <tr>
      <th></th>
      <th>{{__('locale.name')}}</th>
      <th>{{__('locale.product code')}}</th>
      <th>{{__('locale.slug')}}</th>
      <th>{{__('locale.Variationtype')}}</th>
      @if(isset($userType) && $userType==config('custom.superadminrole'))
      <th>{{__('locale.company_name')}}</th>
      @endif
      <th>{{__('locale.category')}}</th>
      <th>{{__('locale.sub category')}}</th>
      <th>{{__('locale.food type')}}</th>
      <th>{{__('locale.status')}}</th>
      <th>{{__('locale.action')}}</th>
    </tr>
  </thead>
  <tbody>

    <?php //echo '<pre>'; print_r($product_value->product_variation[0]->productvariationName->name); ?>
    
    @if(isset($productResult) && !empty($productResult))
    
    
    @foreach($productResult as $product_key => $product_value)
    <tr>
      <td>{{$product_key+1}}</td>
      <td>{{$product_value->product_name}}</td>
      <td>{{$product_value->product_code}}</td>
      <td>{{$product_value->product_slug}}</td>
      <td>
        {{isset($product_value->product_variation[0]->productvariationName->name) ? ucwords($product_value->product_variation[0]->productvariationName->name) : ''}}
      </td>

      {{--<td>{{$product_value->product_variation[0]->productvariationName->name}}</td> --}}
    @if(isset($userType) && $userType==config('custom.superadminrole'))
    <td>{{isset($product_value->company[0]->company_name) ? $product_value->company[0]->company_name : '' }}</td>
    @endif
    <td>{{isset($product_value->category->category_name) ? $product_value->category->category_name : '' }}</td>
    <td>{{isset($product_value->subcategory->subcat_name) ? $product_value->subcategory->subcat_name : '' }}</td>
    <td>{{$product_value->food_type}}</td>
    <td>{{($product_value->blocked==1) ? 'Blocked' : 'Un-blocked'}}</td>
    <td>
    <td>
      <a href="{{route($editUrl,$product_value->id)}}"><i class="material-icons">edit</i></a>
      <a href="{{route($deleteUrl,$product_value->id)}}" onclick="return confirm('Are you sure?')"><i class="material-icons">delete</i></a>
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
@if(isset($productResult) && !empty($productResult))
{!! $productResult->links('panels.paginationCustom') !!}
@endif