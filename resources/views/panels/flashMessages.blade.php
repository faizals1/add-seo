@if ($errors->any())
<div class="alert alert-danger" role="alert">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>

@elseif (session()->get('success'))

<div class="alert-container header-alert-msg common-success-message" id="flash-message">

    <div class="alert alert-success">
        
        <div class="alert-description" >

            @if(is_array(json_decode(session()->get('success'), true)))

                {!! implode('', session()->get('success')->all(':message<br/>')) !!}

            @else

                {!! session()->get('success') !!}

            @endif

        </div>

    </div>

</div>

@elseif (session()->get('error'))

<div class="alert-container header-alert-msg common-danger-message" id="flash-message">

    <div class="alert alert-danger">
        
        <div class="alert-description" >

            @if(is_array(json_decode(session()->get('error'), true)))

                {!! implode('', session()->get('error')->all(':message<br/>')) !!}

            @else

                {!! session()->get('error') !!}

            @endif

        </div>

    </div>

</div>
@endif