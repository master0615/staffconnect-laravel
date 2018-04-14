@extends('layouts.app') @section('content')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Users</div>

				<div class="panel-body">

					<table class="table table-striped">
						<thead>
							<tr>
								<th>Name</th>
								<th>Email</th>
								<th>Role</th>
								<th>Last Login</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($users as $user)
							<tr>
								<td>{{ $user->name}}</td>
								<td>{{ $user->email}}</td>
								<td>{{ $user->role }}</td>
								<td>{{ date("j/m/y g:i a",strtotime($user->last_login))}}</td>
								<td><a href="" class="btn btn-primary btn-xs">Permissions</a></td>
							</tr>
							@endforeach
						</tbody>
					</table>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
