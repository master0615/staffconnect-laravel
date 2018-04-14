@extends("layouts.app")

@section("content")
    <div class="container">
        <div class="row">
            <div class="col-md-8">

                <form method="post" action="{{ route('user.storeField') }}">
                    {{ csrf_field() }}
                    <input type="text" name="ename">
                    <select name="etype">
                        <option>short</option>
                        <option>medium</option>
                        <option>long</option>
                        <option>list</option>
                        <option>date</option>
                    </select>
                    <input type="checkbox" name="editable"  value="1">
                    <input type="checkbox" name="deletable"  value="1">
                    <select name="visibility">
                        <option>optional</option>
                        <option>compulsory</option>
                        <option>hidden</option>
                        <option>pay</option>
                    </select>
                    <input type="text" name="dorder">
                    <select name="sex">
                        <option>male</option>
                        <option>female</option>
                    </select>
                    <select name="filter">
                        <option>equals</option>
                        <option>range</option>
                    </select>


                    <input type="submit">
                </form>

            </div>
        </div>
    </div>
@endsection