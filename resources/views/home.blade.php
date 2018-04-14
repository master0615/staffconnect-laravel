@extends('layouts.app') @section('content')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Dashboard</div>

				<div class="panel-body">
					@if (session('status'))
					<div class="alert alert-success">{{ session('status') }}</div>
					@endif

					<ul class='nav'>

						<li class="nav-item"><a class="nav-link" href="newClient">New
								Client</a></li>

						<li class="nav-item"><a class="nav-link" href="clients">Clients</a></li>

						<li class="nav-item"><a class="nav-link" href="users">Users</a></li>

						<li class="nav-item"><a href="{{ route('logout') }}"
							onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
								Logout </a></li>


					</ul>

					<form id="logout-form" action="{{ route('logout') }}" method="POST"
						style="display: none;">{{ csrf_field() }}</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
