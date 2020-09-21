<!DOCTYPE html>
<html>
<head>
	<title>Test tool</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="bootstrap.css">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script type="text/javascript" src="jquery.js"></script>
</head>
<body>

	<div class="alert2"></div>

	<div class="container">
		<div class="row mt-4">
			<div class="col-12">
				<label><strong>URL</strong></label>
				<div class="row">
					<div class="col-12">
						<input type="text" id="url" placeholder="http://example.com/api" class="form-control" <?php echo (isset($_GET['url'])) ? 'value="http://' . $_GET['url'] . '/api"' : '' ?>>
					</div>
				</div>
			</div>
			<div class="col-6 mt-4">
				<label><strong>API key</strong></label>
				<div class="row">
					<div class="col-12">
						<input type="text" id="key" placeholder="API key" class="form-control">
					</div>
				</div>
			</div>
			<div class="col-6 mt-4">
				<label><strong>Sensor ID</strong></label>
				<div class="row">
					<div class="col-12">
						<input type="number" id="id" placeholder="Sensor ID" class="form-control">
					</div>
				</div>
			</div>
			<div class="col-12 mt-4">
				<label><strong>Response</strong></label>
				<div class="card">
					<pre id="response"></pre>
				</div>
			</div>
			
			<div class="col-12 mt-5">
				<label><strong>Data</strong></label>
				<div class="input-group">
					<input type="number" id="data" class="form-control" placeholder="Data" value="0">
					<div class="input-group-append">
						<button class="btn btn-success" id="reset">Reset</button> 
					</div>
				</div>
			</div>
			<div class="col-12 mt-4">
				<div class="row">
					<div class="col-6">
						<button id="just-post" class="btn btn-block btn-primary">Just post</button>
					</div>
					<div class="col-6">
						<button id="post-increment" class="btn btn-block btn-primary">Post then increment</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		function showAlert($msg) {
			$('.alert2').html($msg);
			$( ".alert2" ).animate({
				top: "0",
			}, 300, function() {
				setTimeout(hideAlert, 2000);
			});
		}

		function hideAlert() {
			$( ".alert2" ).animate({
				top: "-50",
			}, 300);
		}

		function error(ctx) {
			ctx.css('border-color', 'red');
			setTimeout(function() {
				backToNormal(ctx);
			}, 2300);
		}

		function backToNormal(ctx) {
			ctx.css('border-color', '#ced4da');
		}

		function sendData(incrementBy) {
			var url = $('#url').val();
			var key = $('#key').val();
			var id = $('#id').val();
			var data = $('#data').val();

			if(url == "") {
				showAlert('URL cannot be empty');
				error($('#url'));
				return false;
			}

			if(key == "") {
				showAlert('API key cannot be empty');
				error($('#key'));
				return false;
			}

			if(id == "") {
				showAlert('Sensor ID cannot be empty');
				error($('#id'));
				return false;
			}

			if(data == "") {
				showAlert('Data cannot be empty');
				error($('#data'));
				return false;
			}

			if(isNaN(data)) {
				showAlert('Data is not a number');
				error($('#data'));
				return false;
			}

			$('#response').html('Sending data...');
			$.ajax({
				type: 'GET',
				url: url + '/write?key=' + key + '&' + id + '=' + data,
				success: function(result) {
					if(result == "Invalid key") {
						$('#response').html("Invalid key");
					}else{
						$('#response').html(JSON.stringify(result[0], null, 2));
						$('#data').val(parseInt(data) + parseInt(incrementBy));
					}
				},
				error: function (request, status, error) {
					$('#response').html(request.responseText);
				}
			});

			return true;
		}

		<?php
		if(isset($_GET['source']) && $_GET['source'] == 'webview') {
			echo 'alert("miku is kawaii")';
		}
		?>

		$('#reset').on('click', function() {
			$('#data').val(0);
		})

		$('#just-post').on('click', function() {
			sendData(0);
		})

		$('#post-increment').on('click', function() {
			sendData(1);
		})
	</script>

</body>
</html>
