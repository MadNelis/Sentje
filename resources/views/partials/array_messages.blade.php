<div class="container">
    @if(count($errors) > 0)
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
</div>
