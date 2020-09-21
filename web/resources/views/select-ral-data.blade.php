@extends('layouts.dashboard')

@section('title', 'Event')

@section('content')
<!--HERE CONTENT -->
<div class="container-fluid">
	<div class="col-6 offset-3" id="content">
		<div class="row">

			<div class="col-md-12">
				<p>Data yang akan dihitung</p>

				<table class="table table-sm">
					<thead>
						<tr>
							<th>Nama Sensor (Node)</th>
							@foreach($sensor->annotations as $ann)
							<th scope="col">{{ $ann->value }}</th>
							@endforeach
						</tr>
					</thead>
					<tbody id="hitung-dataset">
						<tr>
							<td>{{ $sensor->name }} ({{ $sensor->node->name }})</td>
							
							@php
							$baseArr = [];
							@endphp

							@foreach($sensor->annotations as $ann)
							<td>{{ $ann->cur_sensor_val }}</td>
							
							@php
							array_push($baseArr, $ann->cur_sensor_val);
							@endphp

							@endforeach
						</tr>
					</tbody>
				</table>

				<form action="{{ route('ral.result') }}" method="get">
					@csrf
					<input class="form-control" style="width: 50%; margin-bottom: 10px" type="text" name="var-bebas" placeholder="Nama variabel bebas" required>
					<input id="input-data" type="hidden" name="data" value="" required>
					<input type="hidden" name="var-terikat" value="{{ $sensor->name }}" required>
					<button type="submit" class="btn btn-primary">Hitung</button>
				</form>


				<p class="mt-5">Pilih pengulangan - berikut dataset yang memiliki perlakuan yang sama dengan {{ $sensor->name }} ({{ $sensor->node->name }})</p>

				<table class="table table-sm">
					<thead>
						<tr>
							<th>Nama Sensor (Node)</th>
							@foreach($sensor->annotations as $ann)
							<th scope="col">{{ $ann->value }}</th>
							@endforeach
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach($data as $item)
						<tr>
							<td>{{ $item['name'] }} ({{ $item['node'] }})</td>
							@foreach($item['sensor_vals'] as $val)
							<td class="get-this" data={{ $val }}>{{ $val }}</td>
							@endforeach
							<td><button sensor-name="{{ $item['name'] }} ({{ $item['node'] }})" class="btn btn-sm btn-secondary btn-select">Pilih</button></td>
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

@section('scripts')

<script type="text/javascript">
	let arr = []
	let firstData ={!! json_encode($baseArr) !!};
	arr.push(firstData)

	$('#input-data').val(JSON.stringify(arr));

	$('.btn-select').click(function() {
		let data = $(this).parent().parent().find('.get-this');
		let dataArr = [];
		let htmlArr = "";
		for(let i = 0; i < data.length; i++) {
			dataArr.push(Number(data[i].innerHTML));
			htmlArr += '<td>' + Number(data[i].innerHTML) + '</td>'
		}
		arr.push(dataArr);

		$('#hitung-dataset').append('<tr><td>' + $(this).attr('sensor-name') +  '</td>' + htmlArr + '</tr>');

		console.log(arr);

		$('#input-data').val(JSON.stringify(arr));
	})
</script>

@endsection
