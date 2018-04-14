@extends("layouts.app")

@section("content")
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h3>Company settings</h3>

                <form>
                    <div class="form-group">
                        <label for="company-name">Company name:</label>
                        <input type="text" class="form-control" id="company-name">
                    </div>
                    <div class="form-group">
                        <label for="company-email">Company shared email:</label>
                        <input type="email" class="form-control" id="company-email">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection