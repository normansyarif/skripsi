@extends('layouts.dashboard')

@section('title', 'Notifikasi')

@section('content')
<!--HERE CONTENT -->
<div class="container-fluid">

	<div class="content-db" id="content">
		<div class="row">
			<div class="col-md-10 offset-md-1 pt-3">

				@include('includes.message')

				<div class="row">
					<div class="col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xs-12">
						<div class="row">
							<div class="col-8">
								<p>Nilai sensor melampaui batas</p>
							</div>
							<div class="col-4" style="text-align: right">
								<p id="clear-notif"><a href="javascript:void(0)">Bersihkan</a></p>
							</div>
						</div>
					</div>
					<div class="col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xs-12">
						<table class="table table-notif">

							@if(Auth::user()->notifications->count())
							<tr>
								<th>Waktu</th>
								<th>Node</th>
								<th>Sensor</th>
								<th>Batas</th>
								<th>Nilai</th>
							</tr>

							@foreach(Auth::user()->notifications as $notif)
							<tr onclick="window.location='{{ route('node.view', $notif["data"]["node_id"]) }}'" class="{{ ($notif["read_at"] == null) ? 'diactive' : '' }} notif-item">
								<td>{{ date("d/m/Y H:i", strtotime($notif["created_at"])) }}</td>
								<td>{{ $notif["data"]["node"] }}</td>
								<td>{{ $notif["data"]["sensor"] }}</td>
								<td>{{ $notif["data"]["limit"] . ' ' . $notif["data"]["unit"] }}</td>
								<td>{{ $notif["data"]["value"] . ' ' . $notif["data"]["unit"] }}</td>
							</tr>

							@php
							Auth::user()->unreadNotifications->markAsRead();
							@endphp

							@endforeach
							
							@else
							<tr>
								<td class="ta-center" colspan="5">Tidak ada notifikasi</td>
							</tr>
							@endif

						</table>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
	
	$('#clear-notif').click(function() {
		$.get('/clearnotif');
		$('.notif-item').remove();
		$('.table-notif').html('<tr><td class="ta-center" colspan="5">Tidak ada notifikasi</td></tr>');
	});

</script>
@endsection
