@extends("layouts.app")

@section("content")
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <table>
                    @foreach($users as $user)
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach
                </table>

            </div>
        </div>
    </div>
@endsection