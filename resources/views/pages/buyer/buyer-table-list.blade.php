<table class="responsive-table">
<thead>
    <tr>
    <th data-field="group_code">{{__('locale.group_code')}}</th>
    <th data-field="group_name">{{__('locale.group_name')}}</th>
    <th data-field="type">{{__('locale.type')}}</th>
    <th data-field="currency_code">{{__('locale.currency_code')}}</th>
   
    </tr>
</thead>
<tbody>
    @if(isset($BuyerResult) && !empty($BuyerResult))
    @foreach($BuyerResult as $buyer_value)
    <tr>
    <td>{{$buyer_value->group_code}}</td>
    <td>{{$buyer_value->group_name}}</td>
    <td>{{$buyer_value->type}}</td>
    <td>{{$buyer_value->currency_code}}</td>
    
    <td>
    <a href="{{asset('buyer-type/'.$buyer_value->id.'/edit')}}" class="btn"><i class="material-icons">edit</i></a>
    <a href="{{route('buyer-type.destroy',$buyer_value->id)}}" class="btn" onclick="return confirm('Are you sure you want to delete this item')"><i class="material-icons">delete</i></a>
    </td>
    </td>
    </tr>
    @endforeach
    @else
    <tr>
    <td colspan="10">{{__('locale.no_record_found')}}</td>
    </tr>
    @endif
</tbody>
</table>
{!! $BuyerResult->links('panels.paginationCustom') !!}