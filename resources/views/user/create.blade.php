@extends("layouts.app")

@section("content")
    <div class="container">
        <div class="row">
            <div class="col-md-8">

                <form method="post" action="{{ route('user.store') }}">
                    {{ csrf_field() }}
                    <input type="text" name="email">
                    <input type="submit">
                </form>

            </div>
        </div>
    </div>
@endsection