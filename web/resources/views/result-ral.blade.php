@extends('layouts.dashboard')

@section('title', 'Event')

@section('content')

<style type="text/css">
	.result-text {
		font-size: 1.5em;
	}
</style>

<!--HERE CONTENT -->
<div class="container-fluid">
	<div class="col-6 offset-3" id="content">
		<div class="row">

			<div class="col-md-12">
				<p style="font-weight: bold">Tabel ANOVA</p>

				<table class="table table-sm">
					<thead>
						<tr>
							<th></th>
							<th>DB</th>
							<th>JK</th>
							<th>KT</th>
							<th>F. Hit</th>
							<th>F. Tab 0.05</th>
							<th>F. Tab 0.01</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Perlakuan</td>
							<td>{{ round($col['db_perlakuan'], 4) }}</td>
							<td>{{ round($col['jumlah_kuadrat_perlakuan'], 4) }}</td>
							<td>{{ round($col['kuadrat_tengah_perlakuan'], 4) }}</td>
							<td>{{ round($col['f_hitung'], 4) }}</td>
							<td id="ftab5"></td>
							<td id="ftab1"></td>
						</tr>
						<tr>
							<td>Galat</td>
							<td>{{ round($col['db_galat'], 4) }}</td>
							<td>{{ round($col['jumlah_kuadrat_galat'], 4) }}</td>
							<td>{{ round($col['kuadrat_tengah_galat'], 4) }}</td>
							<td colspan="3"></td>
						</tr>
						<tr>
							<td>Total</td>
							<td>{{ round($col['db_total'], 4) }}</td>
							<td>{{ round($col['jumlah_kuadrat_total'], 4) }}</td>
							<td colspan="4"></td>
						</tr>
					</tbody>
				</table>

				<p style="font-size: .9em" class="kesimpulan"></p>

				<div class="result" style="margin-top: 50px">
					<p style="font-weight: bold">Hasil</p>
					<p class="result-text"><strong>tn</strong> Tidak Berpengaruh Nyata</p>
					<p class="result-subtext"></p>
				</div>

			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')

<script type="text/javascript" src="{{ asset('js/finv.js') }}"></script>

<script type="text/javascript">
	
	let dbp = {{ $col['db_perlakuan'] }};
	let dbg = {{ $col['db_galat'] }};
	let fhit = {{ $col['f_hitung'] }};
	let var_bebas = '{{ $col['var_bebas'] }}';
	let var_terikat = '{{ $col['var_terikat'] }}';
	let f5 = calculate_f(0.05, dbp, dbg);
	let f1 = calculate_f(0.01, dbp, dbg);
	$('#ftab5').html(f5);
	$('#ftab1').html(f1);

	if(fhit < f5) {
		$('.result-text').html('<strong>tn</strong> Tidak Berpengaruh Nyata');
		$('.result-subtext').html('Perubahan ' + var_bebas + ' tidak berpengaruh nyata terhadap perubahan ' + var_terikat);
		$('.kesimpulan').html('F Hitung (P/G) < F Tabel ( 0,05; DB Perlakuan, DB Galat))');
	}else if(fhit < f1) {
		$('.result-text').html('<strong>*</strong> Berpengaruh Nyata');
		$('.result-subtext').html('Perubahan ' + var_bebas + ' berpengaruh nyata terhadap perubahan ' + var_terikat);
		$('.kesimpulan').html('F Hitung (P/G) ≥ F Tabel ( 0,05; DB Perlakuan, DB Galat))');
	}else{
		$('.result-text').html('<strong>**</strong> Berpengaruh Sangat Nyata');
		$('.result-subtext').html('Perubahan ' + var_bebas + ' berpengaruh sangat nyata terhadap perubahan ' + var_terikat);
		$('.kesimpulan').html('F Hitung (P/G) ≥ F Tabel ( 0,01; DB Perlakuan, DB Galat))');
	}
</script>

@endsection
