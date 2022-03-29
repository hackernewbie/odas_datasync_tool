@if(isset($errors))
    @if(count($errors) >0 )
        @foreach($errors->all() as $error)
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close btn-close" data-bs-dismiss="alert" aria-bs-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{$error}}
            </div>
        @endforeach
    @endif
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close btn-close" data-bs-dismiss="alert" aria-bs-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{session('success')}}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error alert-dismissable">
        <button type="button" class="close btn-close" data-bs-dismiss="alert" aria-bs-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{session('error')}}
    </div>
@endif
