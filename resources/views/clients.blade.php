@extends('layouts.app') @section('content')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Clients</div>

				<div class="panel-body">
					@if (session('status'))
					<div class="alert alert-success">{{ session('status') }}</div>
					@endif

					<table class="table table-striped">
						<thead>
							<tr>
								<th>Client</th>
								<th>URL</th>
								<th>Start Date</th>
								<th>Users</th>
								<th>Shifts</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($websites as $w)
							<tr>
								<td>{{ $w->client}}</td>
								<td>
									@foreach ($w->urls as $u)
										{{$u}}
										<br/>
									@endforeach
								</td>
								<td>{{ $w->created_at}}
								<td></td>
								<td></td>
								<td>
									<a href="deleteClient/{{ $w->id }}" class="btn btn-primary btn-xs">Delete</a>
								</td>
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

@section('pagejs')
    <script type='text/javascript'>
        $(function(){
        	
        });
    </script>
@endsection