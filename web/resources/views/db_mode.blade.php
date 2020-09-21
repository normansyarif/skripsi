<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Dashboard Mode</title>
	<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/chart.css') }}">
	<style type="text/css">
		body {
			background: #000;
			height: 100vh;
			position: relative;
			left: 0;
			top: 0;
			width: 100vw;
		}
		.container {
			position: absolute;
			left: 50%;
			top: 50%;
			transform: translate(-50%, -50%);
		}
		.item {
			display: flex;
			justify-content: center;
			align-items: center;
			margin: 0 auto;
			width: 300px;
			height: 300px;
			border-radius: 90%;
		}
		.item .content {
			flex: 0 0 120px;
			text-align: center;
			-webkit-background-clip: text;
			-webkit-filter: invert() sepia();
			font-size: 1.7em;
		}
		.info {
			color: #fff;
			text-align: center;
			margin-top: 20px;
			font-size: 1.5em;
		}
	</style>
</head>
<body>

	<div class="container">
		<div class="row">
			<div class="col-md-8 col-sm-8 col-xs-12 offset-md-2 offset-sm-2">
				<div class="row">
					@foreach($thisNode->sensors as $sensor)
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="item" id="item-{{ $sensor->id }}">
							<div  class="content">
								<span id="content-{{ $sensor->id }}"></span> {{ $sensor->unit }}
							</div>
						</div>
						<div class="info">
							<p>{{ $sensor->name }}</p>	
							<p id="status-name-{{ $sensor->id }}"></p>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>

</body>

<script src="{{ asset('js/jquery.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/chart.js') }}"></script>
<script src="{{ asset('js/bootstrap.js') }}"></script>

<script type="text/javascript">
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	function liveUpdate() {
		$.ajax({
			type:'GET',
			url:'/ajax/liveDb/{{ $thisNode->id }}',
			success:function(data){
				for(let i = 0; i < data.length; i++) {

					if(data[i].status_color != null) {
						$('#item-' + data[i].id).css('background-color', data[i].status_color);
					}else{
						$('#item-' + data[i].id).css('background-color', '#fff');
					}
					
					$('#content-' + data[i].id).html(data[i].last_value);
					$('#status-name-' + data[i].id).html(data[i].status_name);
				}
				console.log(data);
			}
		});
	}

	liveUpdate();

	setInterval(liveUpdate,2000);
</script>

</html>
