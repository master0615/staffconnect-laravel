@extends('layouts.app') @section('content')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Delete Client</div>

				<div class="panel-body">
					@if (session('status'))
					<div class="alert alert-success">{{ session('status') }}</div>
					@endif
					Really delete client <b>{{ $cname }}</b>?
					<br/>
					<br/>
					<form action="/client" method="post">
						{!! csrf_field() !!}
						{{ method_field('DELETE') }}
						<input type='hidden' name='id' value="{{ $id }}" />
						<button type="submit" class="btn btn-danger">Yes, delete them!</button>
						<a href="/clients" class="btn btn-primary">No, take me back!</a>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
