@extends('layouts.dashboard')

@section('title', 'Pengaturan')

@section('content')
<!--HERE CONTENT -->
<div class="container-fluid">

	<div class="content-db" id="content">
		<div class="row">
			<div class="col-md-10 offset-md-1 pt-3">

				@include('includes.message')

				<div class="row">
					<div class="col-lg-8 offset-lg-2 col-md-8 col-xs-12 offset-md-2">
						<form method="post" action="{{ route('profile.update') }}">
							@csrf
							<div class="form-group row">
								<label for="inputName" class="col-sm-2 col-form-label">Nama</label>
								<div class="col-sm-10">
									<div class="row">
										<div class="col-12">
											<input value="{{ Auth::user()->name }}" required type="text" class="form-control" name="inputName" id="inputName" placeholder="Masukkan nama">
										</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
								<div class="col-sm-10">
									<div class="row">
										<div class="col-9">
											<input value="{{ Auth::user()->email }}" required type="email" class="form-control" name="inputEmail" id="inputEmail" placeholder="Masukkan alamat email">		
										</div>
										<div class="col-3">
											<div class="col-3">
												@if(Auth::user()->email_verified == 0)
												<p id="emailVerificationBtn" class="text-info" style="font-size: .8em; text-decoration: underline; cursor: pointer;">Verifikasi</p>
												@else
												<p class="text-success" style="font-size: .8em">Terverfikasi</p>
												@endif
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label for="inputPhone" class="col-sm-2 col-form-label">HP</label>
								<div class="col-sm-10">
									<div class="row">
										
										@if(Auth::user()->phone != null)
										<div class="col-9">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text" id="basic-addon1">+62</span>
												</div>
												<input value="{{ Auth::user()->phone }}" type="number" class="form-control" name="inputPhone" id="inputPhone" placeholder="81234567890">
											</div>
										</div>
										<div class="col-3">
											<div class="col-3">
												@if(Auth::user()->phone_verified == 0)
												<p id="phoneVerificationBtn" class="text-info" style="font-size: .8em; text-decoration: underline; cursor: pointer;">Verifikasi</p>
												@else
												<p class="text-success" style="font-size: .8em">Terverfikasi</p>
												@endif
											</div>
										</div>

										@else
										<div class="col-12">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text" id="basic-addon1">+62</span>
												</div>
												<input value="{{ Auth::user()->phone }}" type="number" class="form-control" name="inputPhone" id="inputPhone" placeholder="81234567890">
											</div>
										</div>
										@endif

										
									</div>
									
								</div>
							</div>
							<p style="font-size: .8em; color: grey"><i class="fa fa-info-circle"></i> Notifikasi akan dikirim ke email dan kontak HP yang telah terverifikasi</p>
							<button type="submit" class="btn btn-primary">Simpan</button>
							<button data-toggle="modal" data-target="#changePasswordModal" type="button" class="btn btn-secondary">Ubah password</button>
						</form>

					</div>
				</div>

			</div>
		</div>
	</div>
</div>

<!-- The Modal -->
<div class="modal fade" id="changePasswordModal">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<h4 class="modal-title">Ubah password</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<form method="post" action="{{ route('password.update') }}">
				@csrf
				
				<div class="modal-body">
					<div class="setting-item">
						<label>Password lama</label>
						<input id="old-pass" type="password" class="form-control" name="old-pass" required>
					</div>


					<div class="setting-item">
						<label>Password baru</label>
						<input id="new-pass" type="password" class="form-control" name="new-pass" required>
						<small id="newHelp" class="form-text text-danger"></small>
					</div>

					<div class="setting-item">
						<label>Konfirmasi password</label>
						<input id="confirm-pass" type="password" class="form-control" name="confirm-pass" required>
						<small id="confirmHelp" class="form-text text-danger"></small>
					</div>

				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					<button disabled id="submit-btn" type="submit" class="btn btn-primary">Simpan</button>
				</div>

			</form>

		</div>
	</div>
</div>

<!-- Modal email -->
<div class="modal fade" id="modalEmail" tabindex="-1" role="dialog" aria-labelledby="modalEmail" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Verifikasi Email</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('check-verification') }}" method="post">
      	@csrf
	      <div class="modal-body">
	        <p>Kode verifikasi telah dikirimkan ke <strong>{{ Auth::user()->email }}</strong> dan akan berlaku hingga 24 jam</p>
	        <input type="number" name="code" required class="form-control" placeholder="Kode verifikasi">
	      </div>
	      <div class="modal-footer">
	        <button type="submit" class="btn btn-primary">OK</button>
	      </div>
  		</form>
    </div>
  </div>
</div>


<!-- Modal phone -->
<div class="modal fade" id="modalPhone" tabindex="-1" role="dialog" aria-labelledby="modalPhone" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Verifikasi Nomer HP</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('check-verification') }}" method="post">
      	@csrf
	      <div class="modal-body">
	        <p>Kode verifikasi telah dikirimkan ke <strong>+62{{ Auth::user()->phone }}</strong> dan akan berlaku hingga 24 jam</p>
	        <input type="number" name="code" required class="form-control" placeholder="Kode verifikasi">
	      </div>
	      <div class="modal-footer">
	        <button type="submit" class="btn btn-primary">OK</button>
	      </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$(function() {
		$('[data-toggle="tooltip"]').tooltip();

		toastr.options = {
		  "closeButton": true,
		  "debug": false,
		  "newestOnTop": false,
		  "progressBar": false,
		  "positionClass": "toast-top-right",
		  "preventDuplicates": false,
		  "onclick": null,
		  "showDuration": "300",
		  "hideDuration": "1000",
		  "timeOut": "5000",
		  "extendedTimeOut": "1000",
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut"
		}

		$('#emailVerificationBtn').click(function() {
			$(this).html('Loading...');
			$.ajax({
				type:'POST',
				url:'/generate-verification-mail',
				success:function(data){
					if(data == "ok") {
						let ctx = $('#emailVerificationBtn');
						ctx.html('Terkirim');
						ctx.css('text-decoration', 'none');
						ctx.off('click');
						$('#modalEmail').modal('show');
					}else{
						$('#emailVerificationBtn').html('Verifikasi');
						toastr["error"]("Terjadi kesalahan saat mengirim kode konfirmasi", "Error");
					}
				},
				error:function(e) {
					$('#emailVerificationBtn').html('Verifikasi');
					toastr["error"]("Terjadi kesalahan saat mengirim kode konfirmasi", "Error");
				}
			});
		});

		$('#phoneVerificationBtn').click(function() {
			$(this).html('Loading...');
			$.ajax({
				type:'POST',
				url:'/generate-verification-phone',
				success:function(data){
					let status = JSON.parse(data);
					status = status.messages[0].status;
					if(status == 0) {
						let ctx = $('#phoneVerificationBtn');
						ctx.html('Terkirim');
						ctx.css('text-decoration', 'none');
						ctx.off('click');
						$('#modalPhone').modal('show');
					}else{
						$('#phoneVerificationBtn').html('Verifikasi');
						toastr["error"]("Terjadi kesalahan saat mengirim kode konfirmasi", "Error");
					}
				},
				error:function(e) {
					$('#emailVerificationBtn').html('Verifikasi');
					toastr["error"]("Terjadi kesalahan saat mengirim kode konfirmasi", "Error");
				}
			});
		});
	});

	function validatePassword() {
		var oldPass = $('#old-pass').val();
		var newPass = $('#new-pass').val();
		var confirmPass = $('#confirm-pass').val();

		if(newPass.length != 0 && newPass.length < 8) {
			$('#newHelp').html("Password tidak boleh kurang dari 8 karakter.");
		}else{
			$('#newHelp').html("");
		}

		if(confirmPass.length != 0 && newPass != confirmPass) {
			$('#confirmHelp').html("Password tidak cocok.");
		}else{
			$('#confirmHelp').html("");
		}

		if(oldPass.length > 0 && newPass.length >= 8 && confirmPass == newPass) {
			return true;
		}else{
			return false;
		}
	}

	$('#old-pass').on('input', function() {
		if(validatePassword()) {
			$('#submit-btn').prop('disabled', false);
		}else{
			$('#submit-btn').prop('disabled', true);
		}
	});

	$('#new-pass').on('input', function() {
		if(validatePassword()) {
			$('#submit-btn').prop('disabled', false);
		}else{
			$('#submit-btn').prop('disabled', true);
		}
	});

	$('#confirm-pass').on('input', function() {
		if(validatePassword()) {
			$('#submit-btn').prop('disabled', false);
		}else{
			$('#submit-btn').prop('disabled', true);
		}
	});
</script>
@endsection
