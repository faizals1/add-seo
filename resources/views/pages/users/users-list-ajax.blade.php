<table class="table">
  <thead>
    <tr>
      <th></th>
      <th>{{__('locale.name')}}</th>
      <th>{{__('locale.email')}}</th>
      <th>{{__('locale.phone')}}</th>
      <th>{{__('locale.address')}}</th>
      <th>{{__('locale.company_name')}}</th>
      <th>{{__('locale.status')}}</th>
      <th>{{__('locale.action')}}</th>
    </tr>
  </thead>
  <tbody>
    @if(isset($usersResult) && !empty($usersResult->items()))
    @foreach($usersResult as $user_key => $user_value)
    <tr>
    <td>{{$user_key+1}}</td>
    <td>{{$user_value->name}}</td>
    <td>{{$user_value->email}}</td>
    <td>{{$user_value->phone}}</td>
    <td>{{$user_value->address}}</td>
    <td>
      
      {{ isset($user_value->company[0]->company_name) ? $user_value->company[0]->company_name : '' }}
      
    </td>
    <td>{{($user_value->blocked==1) ? 'Blocked' : 'Un-blocked'}}</td>
    
    <td>
      <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      <a href="{{route($deleteUrl,$user_value->id)}}" onclick="return confirm('Are you sure you want to delete this item')"><i class="material-icons">delete</i></a>
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
@if(isset($usersResult) && !empty($usersResult))
{!! $usersResult->links('panels.paginationCustom') !!}
@endif