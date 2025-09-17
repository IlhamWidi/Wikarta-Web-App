@if(Session::has('message_error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ Session::get('message_error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(Session::has('message_success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ Session::get('message_success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@elseif(Session::has('message_array_error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    @foreach(Session::get('message_array_error') as $key => $value)
    {{ sprintf("%s %s", $key, $value) }} &hellip;
    @endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@elseif($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif