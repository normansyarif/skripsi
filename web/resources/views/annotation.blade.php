@extends('layouts.dashboard')

@section('title', 'Event')

@section('content')
<!--HERE CONTENT -->
<div class="container-fluid">
	<div class="col-6 offset-3" id="content">
		<div class="row">

			<div class="col-md-12">
				<p>Daftar Event {{ $sensor->name }}</p>

				<table class="table table-sm">
					<thead>
						<tr>
							<th scope="col">Waktu</th>
							<th scope="col">Label</th>
							<th scope="col">Nilai Sensor</th>
							<th scope="col">Handle</th>
						</tr>
					</thead>
					<tbody>
						@foreach($sensor->annotations as $ann)
						<tr>
							<td>{{ $ann->label }}</td>
							<td>{{ $ann->value }}</td>
							<td>{{ $ann->cur_sensor_val }}</td>
							<td>
								<form action="{{ route('annotation.delete', $ann->id) }}" method="post">
									@csrf
									<button type="submit" class="btn-sm btn btn-danger">Hapus</button>
								</form>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>

				<a href="{{ route('node.view', $sensor->node->id) }}" class="btn btn-info">Kembali</a>

			</div>
		</div>
	</div>
</div>

@endsection

