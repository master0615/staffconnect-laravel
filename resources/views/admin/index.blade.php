@section("content")
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <a class="btn btn-default" href="{{ route('admin.createCompany') }}">
                    Create Company
                </a>

                <a class="btn btn-default" href="{{ route('admin.currentCompany') }}">
                    Current company
                </a>
                <a class="btn btn-default" href="{{ route('admin.listCompanies') }}">
                   List companies
                </a>
            </div>
        </div>
    </div>
@endsection