@extends('layouts.dashboard')

@section('title', 'Monitor')

@section('content')
<!--HERE CONTENT -->
<div class="container-fluid">
	<div class="col-10 offset-1" id="content">
		<div class="row">
			<div class="col-md-10 node-info">
				<p data-toggle="tooltip" data-placement="right" title="{{ $thisNode->description }}">{{ $thisNode->name }} </p>
				
				@if(count($thisNode->sensors) > 0)
				<p>Sensor: {{ implode(', ', array_column(json_decode($thisNode->sensors), 'name')) }}</p>
				@else
				<p>Sensor: -</p>
				@endif

				<p>Dibuat pada: {{ date("d M Y", strtotime($thisNode->created_at)) }}</p>

				<div style="display: inline-flex;">
					<p style="margin-right: 10px"><a class="btn btn-sm btn-success" href="{{ route('node.monitor', $thisNode->id) }}">Chart View Mode</a></p>
					<p style="font-size: .8em"><a class="btn btn-sm btn-info" href="{{ route('node.db-mode', $thisNode->id) }}">Dashboard View Mode</a></p>
				</div>
				@include('includes.message')
			</div>

			<div class="col-md-12 text-center tabs">

				<div class="tab-content text-left" id="nav-tabContent">

					<div class="tab-pane fade show active" id="pop1" role="tabpanel" aria-labelledby="pop1-tab">
						<div class="row">

							<div class="mb-4 mt-2 filter">
								<form method="get" class="row">
									<span class="text-filter">Tampilkan data dari</span>
									<input value="{{ $_GET['start'] }}" type="datetime-local" name="start" class="form-control form-filter" required>
									<span class="text-filter">hingga</span>
									<input id="end-time" value="{{ $_GET['end'] }}" type="datetime-local" name="end" class="form-control form-filter" required>
									<button type="submit" class="btn btn-primary btn-sm btn-filter">OK</button>
									@if(isset($_GET['start']) && isset($_GET['end']))
									<a href="{{ route('node.monitor', $thisNode->id) }}" class="btn btn-success btn-sm btn-filter ml-3" style="color: #fff">Live monitoring</a>
									@endif
								</form>
								@if(!isset($_GET['start']) || !isset($_GET['end']))
								<div class="row show-per-wrapper">
									<span class="text-filter">Tampilkan</span>
									<select id="show" class="form-control show-per">
										<option {{ !isset($_GET['limit']) ? 'selected' : '' }} value="0">Semua data</option>
										<option {{ (isset($_GET['limit']) && $_GET['limit'] == 10) ? 'selected' : '' }} value="10">10 data</option>
										<option {{ (isset($_GET['limit']) && $_GET['limit'] == 20) ? 'selected' : '' }} value="20">20 data</option>
										<option {{ (isset($_GET['limit']) && $_GET['limit'] == 30) ? 'selected' : '' }} value="30">30 data</option>
										<option {{ (isset($_GET['limit']) && $_GET['limit'] == 40) ? 'selected' : '' }} value="40">40 data</option>
										<option {{ (isset($_GET['limit']) && $_GET['limit'] == 50) ? 'selected' : '' }} value="50">50 data</option>
										<option {{ (isset($_GET['limit']) && $_GET['limit'] == 70) ? 'selected' : '' }} value="70">70 data</option>
										<option {{ (isset($_GET['limit']) && $_GET['limit'] == 100) ? 'selected' : '' }} value="100">100 data</option>
									</select>
								</div>
								@endif
							</div>

							@if(count($thisNode->sensors) > 0)
							@foreach($thisNode->sensors as $sensor)
							<div class="col-md-6 mt-3 chart-wrapper">
								<p class="float-left view-kode">Kode sensor: <span class="monitor-sensor-code">{{ $sensor->id }}</span></p>

								@if($sensor->status == 1)
								<p recording="true" status-for-sensor="{{ $sensor->id }}" class="status-info float-right view-kode text-success">Status: Recording</p>
								@else
								<p recording="false" status-for-sensor="{{ $sensor->id }}" class="status-info float-right view-kode text-danger">Status: Paused</p>
								@endif

								<div class="canvas">
									<canvas id="line-chart-{{ $sensor->id }}"></canvas>
								</div>
								<div class="clear"></div>

								@if($sensor->is_notif == 1 && count($sensor->values) > 0)

								@php
								// Determine the current status based on $sensor->values collection
								$lastValue = $sensor->values[count($sensor->values)-1]->value;
								$limit = json_decode($sensor->limit_values);
								$status = $color = "";
								$one = $two = $three = $four = false;

								if($lastValue < $limit[0]->limit) {
									$status = $limit[0]->name;
									$color = $limit[0]->color;
								}else{
									if($lastValue >= $limit[1]->limit) {
										$status = $limit[1]->name;
										$color = $limit[1]->color;
										if($limit[1]->limit == $limit[2]->limit) $one = true;
									}

									if($lastValue >= $limit[2]->limit) {
										$status = $limit[2]->name;
										$color = $limit[2]->color;
										if($limit[2]->limit == $limit[3]->limit) $two = true;
									}

									if($lastValue >= $limit[3]->limit) {
										$status = $limit[3]->name;
										$color = $limit[3]->color;
										if($limit[3]->limit == $limit[4]->limit) $three = true;
									}

									if($lastValue >= $limit[4]->limit){
										$status = $limit[4]->name;
										$color = $limit[4]->color;
										$four = true;
									}	
								}

								if($one) {
									$status = $limit[1]->name;
									$color = $limit[1]->color;
								}else if($two) {
									$status = $limit[2]->name;
									$color = $limit[2]->color;
								}else if($three) {
									$status = $limit[3]->name;
									$color = $limit[3]->color;
								}else if($four) {
									$status = $limit[4]->name;
									$color = $limit[4]->color;
								}

								@endphp

								<div class="sensor-info">
									<p><span id="status-dot-{{ $sensor->id }}" class="dot" style="background-color: {{ $color }}"></span> <span id="status-name-{{ $sensor->id }}" style="color: {{ $color }}">{{ $status }}</span></p>
								</div>
								@endif

								<div class="sensor-sum">
									<div class="row">
										<div class="col-md-6">
											<p>Terakhir: <span class="happy-hardcore" id="happy-hardcore-{{ $sensor->id }}"></span> (<span id="last-{{ $sensor->id }}"></span>)</p>
											<p>Nilai rata-rata: <span id="avg-{{ $sensor->id }}"></span> <span class="unit-{{ $sensor->id }}"></span></p>
										</div>
										<div class="col-md-6 sensor-sum-1">
											<p>Tertinggi: <span id="max-{{ $sensor->id }}"></span> <span class="unit-{{ $sensor->id }}"></span> (<span id="max-{{ $sensor->id }}-date"></span>)</p>
											<p>Terendah: <span id="min-{{ $sensor->id }}"></span> <span class="unit-{{ $sensor->id }}"></span> (<span id="min-{{ $sensor->id }}-date"></span>)</p>
										</div>
										<div>
											<input type="hidden" id="count-{{ $sensor->id }}">
											<input class="last-num" type="hidden" id="last-num-{{ $sensor->id }}">
											<input type="hidden" id="unit-{{ $sensor->id }}" value="{{ $sensor->unit }}">
										</div>
									</div>
								</div>
							</div>
							@endforeach
							@else
							<div class="col-md-12 chart-wrapper">
								<div class=" no-data">
									<p >Tidak ada data</p>
								</div>
							</div>
							@endif

						</div>
					</div>

					<div class="tab-pane fade" id="pop2" role="tabpanel" aria-labelledby="pop2-tab">
						<div class="col-md-12">
							<form method="post" action="{{ route('node.update', $thisNode->id) }}">
								@csrf

								<div class="setting-item">
									<label>Nama node</label>
									<input type="text" class="form-control" name="name" value="{{ $thisNode->name }}" required>
								</div>

								<div class="setting-item">
									<label>Deskripsi</label>
									<input type="text" class="form-control" name="description" value="{{ $thisNode->description }}" required>
								</div>

								<div class="setting-item">
									<label>Akses</label>
									<div class="form-check">
										<label class="form-check-label" for="radio1">
											<input type="radio" class="form-check-input" id="radio1" name="access_type" value="1" {{ ($thisNode->access_type == 1) ? 'checked' : '' }}>
											<i class="fa fa-users" aria-hidden="true"></i> Publik
										</label>
									</div>
									<div class="form-check">
										<label class="form-check-label" for="radio2">
											<input type="radio" class="form-check-input" id="radio2" name="access_type" value="2" {{ ($thisNode->access_type == 2) ? 'checked' : '' }}><i class="fa fa-lock" aria-hidden="true"></i> Private
										</label>
									</div>
								</div>

								<button type="submit" class="btn btn-primary">Simpan</button>

							</form>

							<div class="api-div">
								<div class="card card-danger">

									<div class="danger-item mb-3">
										<div class="danger-info float-left">
											<p>Bersihkan data</p>
											<p>Semua data yang telah masuk akan dihapus. Data yang dihapus tidak dapat dikembalikan.</p>
										</div>
										<button class="btn btn-danger float-right"
										onclick="event.preventDefault();
										if(confirm('Anda yakin ingin menghapus semua data sensor di node?')) {
											document.getElementById('clear-form').submit();
										}
										">Bersihkan</button>
										<form id="clear-form" action="{{ route('node.clear', $thisNode->id) }}" method="POST" style="display: none;">
											@csrf
										</form>
									</div>

									<div class="danger-item">
										<div class="danger-info float-left">
											<p>Hapus node</p>
											<p>Node "{{ $thisNode->name }}" akan dihapus. Node yang dihapus tidak dapat dikembalikan.</p>
										</div>
										<button class="btn btn-danger float-right"
										onclick="event.preventDefault();
										if(confirm('Anda yakin ingin menghapus node?')) {
											document.getElementById('delete-form').submit();
										}
										">Hapus</button>
										<form id="delete-form" action="{{ route('node.delete', $thisNode->id) }}" method="POST" style="display: none;">
											@csrf
										</form>
									</div>

								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade" id="pop3" role="tabpanel" aria-labelledby="pop3-tab">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-12">

									<button data-toggle="modal" data-target="#new-sensor-modal" class="btn btn-info {{ (count($thisNode->sensors) > 0 ? 'float-right' : '') }}"><span class="fa fa-plus"></span> Tambah sensor</button>

									<div class="clear"></div>

									@if(count($thisNode->sensors) > 0)
									<div class="row">
										@foreach($thisNode->sensors as $sensor)
										<form class="col-md-6 mt-3 mb-3" method="post" action="{{ route('sensor.update', $sensor->id) }}">
											@csrf

											<div>
												<div class="sensor-field-label float-right">
													<label>Kode sensor: {{ $sensor->id }}</label>
												</div>
												<div class="sensor-field-left">
													<div>
														<label class="form-label">Nama sensor</label>
														<input type="text" name="name" class="form-control" placeholder="Nama sensor" value="{{ $sensor->name }}" required>
													</div>

													<div>
														<label class="form-label">Satuan</label>
														<input type="text" name="unit" class="form-control" placeholder="Satuan nilai" value="{{ $sensor->unit }}" required>
													</div>

													<div>
														<label class="form-label">Notifikasi <i class="info-btn fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Jika fitur ini diaktifkan, Anda akan mendapatkan notifikasi via email saat nilai sensor melebihi batas yang ditentukan."></i></i></label>
														<select name="is-notif" class="form-control is-notif" required>
															<option value="0" {{ $sensor->is_notif == 0 ? 'selected' : '' }}>Nonaktif</option>
															<option value="1" {{ $sensor->is_notif == 1 ? 'selected' : '' }}>Aktif</option>
														</select>
													</div>

													<div class="will-be-hidden {{ $sensor->is_notif == 0 ? 'hide' : '' }}">

														@php
														$limit_values = json_decode($sensor->limit_values);
														@endphp

														<div class="row">
															<div class="col-12">
																<label class="form-label">Atur status sensor jika nilai <strong>kurang</strong> dari:</label>
															</div>
															<div class="col-12">
																<div class="row">
																	<div class="col-5">
																		<input type="text" name="status-1" class="form-control" placeholder="Status" value="{{ isset($limit_values[0]->name) ? $limit_values[0]->name : '' }}">
																	</div>
																	<div class="col-5">
																		<input type="number" name="limit-1"  id="limit-1-{{ $sensor->id }}" class="form-control" placeholder="Limit" value="{{ isset($limit_values[0]->limit) ? $limit_values[0]->limit : '0' }}">
																	</div>
																	<div class="col-2">
																		<input type="color" name="color-1" value="{{ isset($limit_values[0]->color) ? $limit_values[0]->color : '#000000' }}">
																	</div>
																</div>
															</div>
														</div>

														<div class="row">
															<div class="col-12">
																<label class="form-label">Atur status sensor jika nilai <strong>lebih</strong> dari:</label>
															</div>
															<div class="col-12">
																<div class="row">
																	<div class="col-5">
																		<input type="text" name="status-2"  class="form-control" placeholder="Status" value="{{ isset($limit_values[1]->name) ? $limit_values[1]->name : '' }}">
																	</div>
																	<div class="col-5">
																		<input type="number" name="limit-2" id="limit-2-{{ $sensor->id }}" class="form-control" placeholder="Limit" value="{{ isset($limit_values[1]->limit) ? $limit_values[1]->limit : '0' }}">
																	</div>
																	<div class="col-2">
																		<input type="color" name="color-2" value="{{ isset($limit_values[1]->color) ? $limit_values[1]->color : '#000000' }}">
																	</div>
																</div>
															</div>
														</div>

														<div class="row">
															<div class="col-12">
																<label class="form-label">Atur status sensor jika nilai <strong>lebih</strong> dari:</label>
															</div>
															<div class="col-12">
																<div class="row">
																	<div class="col-5">
																		<input type="text" name="status-3" class="form-control" placeholder="Status" value="{{ isset($limit_values[2]->name) ? $limit_values[2]->name : '' }}">
																	</div>
																	<div class="col-5">
																		<input type="number" name="limit-3" id="limit-3-{{ $sensor->id }}" class="form-control" placeholder="Limit" value="{{ isset($limit_values[2]->limit) ? $limit_values[2]->limit : '0' }}">
																	</div>
																	<div class="col-2">
																		<input type="color" name="color-3" value="{{ isset($limit_values[2]->color) ? $limit_values[2]->color : '#000000' }}">
																	</div>
																</div>
															</div>
														</div>

														<div class="row">
															<div class="col-12">
																<label class="form-label">Atur status sensor jika nilai <strong>lebih</strong> dari:</label>
															</div>
															<div class="col-12">
																<div class="row">
																	<div class="col-5">
																		<input type="text" name="status-4" class="form-control" placeholder="Status" value="{{ isset($limit_values[3]->name) ? $limit_values[3]->name : '' }}">
																	</div>
																	<div class="col-5">
																		<input type="number" name="limit-4" id="limit-4-{{ $sensor->id }}" class="form-control" placeholder="Limit" value="{{ isset($limit_values[3]->limit) ? $limit_values[3]->limit : '0' }}">
																	</div>
																	<div class="col-2">
																		<input type="color" name="color-4" value="{{ isset($limit_values[3]->color) ? $limit_values[3]->color : '#000000' }}">
																	</div>
																</div>
															</div>
														</div>

														<div class="row">
															<div class="col-12">
																<label class="form-label">Atur status sensor jika nilai <strong>lebih</strong> dari:</label>
															</div>
															<div class="col-12">
																<div class="row">
																	<div class="col-5">
																		<input type="text" name="status-5" class="form-control" placeholder="Status" value="{{ isset($limit_values[4]->name) ? $limit_values[4]->name : '' }}">
																	</div>
																	<div class="col-5">
																		<input type="number" name="limit-5" id="limit-5-{{ $sensor->id }}" class="form-control" placeholder="Limit" value="{{ isset($limit_values[4]->limit) ? $limit_values[4]->limit : '0' }}">
																	</div>
																	<div class="col-2">
																		<input type="color" name="color-5" value="{{ isset($limit_values[4]->color) ? $limit_values[4]->color : '#000000' }}">
																	</div>
																</div>
															</div>
														</div>

													</div>

												</div>
												<div class="sensor-field-right">
													<button type="submit" class="btn btn-info btn-sm btn-sensor"><i class="fa fa-edit"></i> Update setting</button>
													<button onclick="
													event.preventDefault();
													if(confirm('Anda yakin ingin menghapus data sensor?')) {
														document.getElementById('sensor-id-clear').value = '{{ $sensor->id }}';
														document.getElementById('clear-sensor').submit();
													}
													" class="btn btn-success btn-sm btn-sensor"><i class="fa fa-trash"></i> Clear data</button>
													<button onclick="
													event.preventDefault();
													if(confirm('Anda yakin ingin menghapus sensor?')) {
														document.getElementById('sensor-id').value = '{{ $sensor->id }}';
														document.getElementById('delete-sensor').submit();
													}
													" class="btn btn-danger btn-sm btn-sensor"><i class="fa fa-trash"></i> Hapus sensor</button>
												</div>
											</div>
										</form>
										@endforeach
									</div>
									
									<form id="delete-sensor" method="post" action="{{ route('sensor.delete') }}">
										<input id="node-id" type="hidden" name="node-id" value="{{ $thisNode->id }}">
										<input id="sensor-id" type="hidden" name="sensor-id">
										@csrf
									</form>

									<form id="clear-sensor" method="post" action="{{ route('sensor.clear') }}">
										<input id="node-id-clear" type="hidden" name="node-id-clear" value="{{ $thisNode->id }}">
										<input id="sensor-id-clear" type="hidden" name="sensor-id-clear">
										@csrf
									</form>

									@endif

								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade" id="pop4" role="tabpanel" aria-labelledby="pop4-tab">
						<div class="col-md-12">
							<div>
								<div class="card">
									<h6 class="mb-3">API Key</h6>

									<div class="btn-group mb-3">
										<input disabled type="text" class="form-control wdth3 input-copy" name="" value="{{ $thisNode->api_key }}">
										<button type="button" class="btn btn-default btn-copy js-tooltip js-copy" data-toggle="tooltip" data-placement="bottom" data-copy="{{ $thisNode->api_key }}" title="Salin key">
											<!-- icon from google's material design library -->
											<svg class="icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="24" height="24" viewBox="0 0 24 24"><path d="M17,9H7V7H17M17,13H7V11H17M14,17H7V15H14M12,3A1,1 0 0,1 13,4A1,1 0 0,1 12,5A1,1 0 0,1 11,4A1,1 0 0,1 12,3M19,3H14.82C14.4,1.84 13.3,1 12,1C10.7,1 9.6,1.84 9.18,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3Z" /></svg>
										</button>
									</div>
								</div>

								<div class="card">
									<h6 class="mb-3">API Calls</h6>

									<div class="smaller-line-height mt-2">
										<p style="color: #008c9d">Menambah data</p>
										<div class="ml-3">
											<p class="small-text bold">Deskripsi:</p>
											<p class="small-text">Anda dapat menambahkan data baru kedalam node.</p>
											<p class="small-text bold">Parameter:</p>
											<ol class="small-text normal-line-height">
												<li><strong>api_key</strong> merupakan 'kunci' untuk mengubah node yang bersangkutan. API Key dapat dilihat pada tab <strong>API</strong>.</li>
												<li><strong>kode_sensor</strong> merupakan identitas sensor yang akan ditambah datanya. Kode sensor dapat dilihat pada tab <strong>Sensor</strong>.</li>
												<li><strong>data_sensor</strong> merupakan data baru yang akan ditambah pada sensor yang bersangkutan.</li>
											</ol>
											<div class="api-url">
												GET <span class="str">{{ Request::getHttpHost() }}/api/write?key=<span class="customcode">{api_key}</span>&<span class="customcode">{kode_sensor}</span>=<span class="customcode">{data_sensor}</span></span>
											</div>
											<p class="small-text bold mt-2">Contoh API call:</p>
											<p class="small-text">{{ Request::getHttpHost() }}/api/write?key=123456789&10=50</p>
										</div>

									</div>

									<div class="smaller-line-height mt-3">
										<p style="color: #008c9d">Melihat data</p>
										<div class="ml-3">
											<p class="small-text bold">Deskripsi:</p>
											<p class="small-text">Anda dapat menampilkan data sensor pada node tertentu.</p>
											<p class="small-text bold">Parameter:</p>
											<ol class="small-text normal-line-height">
												<li><strong>api_key</strong> merupakan 'kunci' untuk mengubah node yang bersangkutan. API Key dapat dilihat pada tab <strong>API</strong>.</li>
												<li><strong>kode_sensor (opsional)</strong> merupakan kode identifikasi sensor pada node. Anda dapat menampilkan semua data sensor pada node, atau salah satu sensor yang ada di node.</li>
												<li><strong>limit_value (opsional)</strong> merupakan jumlah maksimal data yang ditampilkan.</li>
											</ol>
											<div class="api-url">
												GET <span class="str">{{ Request::getHttpHost() }}/api/read?key=<span class="customcode">{api_key}</span>&sensor=<span class="customcode">{kode_sensor}</span>&limit=<span class="customcode">{limit_value}</span></span>
											</div>
											<p class="small-text bold mt-2">Contoh API call:</p>
											<p class="small-text" style="line-height: 5px">{{ Request::getHttpHost() }}/api/read?key=123456789</p>
											<p class="small-text" style="line-height: 5px">{{ Request::getHttpHost() }}/api/read?key=123456789&sensor=100</p>
											<p class="small-text" style="line-height: 5px">{{ Request::getHttpHost() }}/api/read?key=123456789&sensor=100&limit=50</p>

										</div>
									</div>

									<div class="smaller-line-height mt-3">
										<p style="color: #008c9d">Melihat data berdasarkan rentang waktu</p>
										<div class="ml-3">
											<p class="small-text bold">Deskripsi:</p>
											<p class="small-text">Anda dapat menampilkan data berdasarkan rentang waktu. Respon API berupa data sensor yang masuk antara dua waktu yang telah ditentukan sebelumnya.</p>
											<p class="small-text bold">Parameter:</p>
											<ol class="small-text normal-line-height">
												<li><strong>api_key</strong> merupakan 'kunci' untuk mengubah node yang bersangkutan. API Key dapat dilihat pada tab <strong>API</strong>.</li>
												<li><strong>start_time</strong> merupakan waktu awal.</li>
												<li><strong>end_time</strong> merupakan waktu akhir.</li>
											</ol>
											<div class="api-url">
												GET <span class="str">{{ Request::getHttpHost() }}/api/read?key=<span class="customcode">{api_key}</span>&start=<span class="customcode">{start_time}</span>&end=<span class="customcode">{end_time}</span></span>
											</div>
											<p class="small-text bold mt-2">Contoh API call:</p>
											<p class="small-text" style="line-height: 5px">{{ Request::getHttpHost() }}/api/read?key=123456789&start=1546322400&end=1577905200 <span style="float: right">(Unix timestamp)</span></p>
											<p class="small-text" style="line-height: 5px">{{ Request::getHttpHost() }}/api/read?key=123456789&start=2019-01-01T01%3A00&end=2020-01-01T14%3A00 <span style="float: right">(Datetime)</span></p>

										</div>
									</div>
									
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<!-- The Modal -->
<div class="modal fade" id="new-sensor-modal">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<h4 class="modal-title">Tambah sensor</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<form method="post" action="{{ route('sensor.post') }}">
				@csrf
				
				<div class="modal-body">
					<div class="setting-item">
						<label>Nama sensor</label>
						<input type="text" class="form-control" name="name" required>
					</div>

					<div class="setting-item">
						<label>Deskripsi</label>
						<input type="text" class="form-control" name="description">
					</div>

					<div class="setting-item">
						<label>Unit</label>
						<input type="text" class="form-control" name="unit" required>
					</div>
					<input type="hidden" name="node_id" value="{{ $thisNode->id }}" required>
				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Simpan</button>
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

    /**
 * index.js
 * - All our useful JS goes here, awesome!
 */
 (function ($) { 

 	$('#show').on('change', function() {
 		var val = $(this).val();
 		if(val == "0") {
 			window.location = "/monitor/{{ $thisNode->id }}";
 		}else{
 			window.location = "/monitor/{{ $thisNode->id }}?limit=" + val;
 		}
 		
 	})

 	var a = 0;
 	$('#btn-sidebars').on('click', function (e) {
 		e.preventDefault();
 		if (a == 0){
 			a = 1;
 			$('#content').removeClass('content')
 			$('.main-sidebar').removeClass('show-sidebar').addClass('hide-sidebar');
 		}else if (a  == 1) {
 			a = 0;
 			$('#content').addClass('content')
 			$('.main-sidebar').removeClass('hide-sidebar').addClass('show-sidebar');
 		}
 	});

 	$('.is-notif').on('change', function() {
 		if($(this).val() == "0") {
 			$(this).parent().parent().find('.will-be-hidden').hide();
 			$(this).parent().parent().find('.will-be-required').removeAttr('required');
 		}else{
 			$(this).parent().parent().find('.will-be-hidden').show();
 			$(this).parent().parent().find('.will-be-required').attr('required', 'required');
 		}
 	})

 	@if(count($thisNode->sensors) > 0)
	@foreach($thisNode->sensors as $sensor)
 		// Change status 1
		$('#limit-1-{{ $sensor->id }}').change(function () {
    		let val = $(this).val();
    		if(val == "") {
    			$(this).val(0);
    		}else{
    			$(this).val(parseFloat(val));
    		}

    		let lim2 = $('#limit-2-{{ $sensor->id }}');
    		lim2.val(parseFloat($(this).val()));

    		let lim3 = $('#limit-3-{{ $sensor->id }}');
    		if(parseFloat(lim3.val()) < parseFloat($(lim2).val())) {
    			lim3.val(parseFloat($(lim2).val()));
    		}

    		let lim4 = $('#limit-4-{{ $sensor->id }}');
    		if(parseFloat(lim4.val()) < parseFloat($(lim3).val())) {
    			lim4.val(parseFloat($(lim3).val()));
    		}

    		let lim5 = $('#limit-5-{{ $sensor->id }}');
    		if(parseFloat(lim5.val()) < parseFloat($(lim4).val())) {
    			lim5.val(parseFloat($(lim4).val()));
    		}
		});

		// Change status 2
		$('#limit-2-{{ $sensor->id }}').change(function () {
    		let val = $(this).val();
    		if(val == "") {
    			$(this).val(0);
    		}else{
    			$(this).val(parseFloat(val));
    		}

    		let lim1 = $('#limit-1-{{ $sensor->id }}');
    		lim1.val(parseFloat($(this).val()));

    		let lim3 = $('#limit-3-{{ $sensor->id }}');
    		if(parseFloat(lim3.val()) < parseFloat($(this).val())) {
    			lim3.val(parseFloat($(this).val()));
    		}

    		let lim4 = $('#limit-4-{{ $sensor->id }}');
    		if(parseFloat(lim4.val()) < parseFloat($(lim3).val())) {
    			lim4.val(parseFloat($(lim3).val()));
    		}

    		let lim5 = $('#limit-5-{{ $sensor->id }}');
    		if(parseFloat(lim5.val()) < parseFloat($(lim4).val())) {
    			lim5.val(parseFloat($(lim4).val()));
    		}
		});

		// Change status 3
		$('#limit-3-{{ $sensor->id }}').change(function () {
    		let val = $(this).val();
    		if(val == "") {
    			$(this).val(0);
    		}else{
    			$(this).val(parseFloat(val));
    		}

    		let lim2 = $('#limit-2-{{ $sensor->id }}');
    		if(parseFloat(lim2.val()) > parseFloat($(this).val())) {
    			lim2.val(parseFloat($(this).val()));
    		}

    		let lim1 = $('#limit-1-{{ $sensor->id }}');
    		if(parseFloat(lim1.val()) > parseFloat($(lim2).val())) {
    			lim1.val(parseFloat($(lim2).val()));
    		}

    		let lim4 = $('#limit-4-{{ $sensor->id }}');
    		if(parseFloat(lim4.val()) < parseFloat($(this).val())) {
    			lim4.val(parseFloat($(this).val()));
    		}

    		let lim5 = $('#limit-5-{{ $sensor->id }}');
    		if(parseFloat(lim5.val()) < parseFloat($(lim4).val())) {
    			lim5.val(parseFloat($(lim4).val()));
    		}
		});

		// Change status 4
		$('#limit-4-{{ $sensor->id }}').change(function () {
    		let val = $(this).val();
    		if(val == "") {
    			$(this).val(0);
    		}else{
    			$(this).val(parseFloat(val));
    		}

    		let lim3 = $('#limit-3-{{ $sensor->id }}');
    		if(parseFloat(lim3.val()) > parseFloat($(this).val())) {
    			lim3.val(parseFloat($(this).val()));
    		}

    		let lim2 = $('#limit-2-{{ $sensor->id }}');
    		if(parseFloat(lim2.val()) > parseFloat($(lim3).val())) {
    			lim2.val(parseFloat($(lim3).val()));
    		}

    		let lim1 = $('#limit-1-{{ $sensor->id }}');
    		if(parseFloat(lim1.val()) > parseFloat($(lim2).val())) {
    			lim1.val(parseFloat($(lim2).val()));
    		}

    		let lim5 = $('#limit-5-{{ $sensor->id }}');
    		if(parseFloat(lim5.val()) < parseFloat($(this).val())) {
    			lim5.val(parseFloat($(this).val()));
    		}
		});

		// Change status 5
		$('#limit-5-{{ $sensor->id }}').change(function () {
    		let val = $(this).val();
    		if(val == "") {
    			$(this).val(0);
    		}else{
    			$(this).val(parseFloat(val));
    		}

    		let lim4 = $('#limit-4-{{ $sensor->id }}');
    		if(parseFloat(lim4.val()) > parseFloat($(this).val())) {
    			lim4.val(parseFloat($(this).val()));
    		}

    		let lim3 = $('#limit-3-{{ $sensor->id }}');
    		if(parseFloat(lim3.val()) > parseFloat($(lim4).val())) {
    			lim3.val(parseFloat($(lim4).val()));
    		}

    		let lim2 = $('#limit-2-{{ $sensor->id }}');
    		if(parseFloat(lim2.val()) > parseFloat($(lim3).val())) {
    			lim2.val(parseFloat($(lim3).val()));
    		}

    		let lim1 = $('#limit-1-{{ $sensor->id }}');
    		if(parseFloat(lim1.val()) > parseFloat($(lim2).val())) {
    			lim1.val(parseFloat($(lim2).val()));
    		}

		});


	@endforeach
	@endif
 	

 }(jQuery))
</script>

<script>
	@if(count($thisNode->sensors) > 0)
	@foreach($thisNode->sensors as $sensor)

	@php
	$created_at = [];
	$values = [];
	$max = 0;
	$min = 0;
	$avg = 0;
	$lastUpdate = 0;
	$maxDate = '-';
	$minDate = '-';
	$last = 0;

	if(isset($_GET['start']) && isset($_GET['end'])) {
		foreach($sensor->values as $k => $value) {
			if($value['added_at'] >= strtotime($_GET['start']) && $value['added_at'] <= strtotime($_GET['end'])) {
				$created_at[$k] = date('d-m-Y H:i', $value['added_at']);
				$values[$k] = $value['value'];

				// Set max value on load
				if(max($values) <= $value['value']) {
					$max = $value['value'];
					$maxDate = $created_at[$k];
				}

				// Set min value on load
				if(min($values) >= $value['value']) {
					$min = $value['value'];
					$minDate = $created_at[$k];
				}

				// Set last update on load
				if($value['added_at'] > $lastUpdate) {
					$lastUpdate = $value['added_at'];
				}

				$last = $value['value'];
			}
		}
	}else{
		$remaining = count($sensor->values);
		foreach($sensor->values as $k => $value) {
			if(isset($_GET['limit']) && is_numeric($_GET['limit'])) {
				if($remaining <= $_GET['limit']) {
					$created_at[$k] = date('d-m-Y H:i', $value['added_at']);
					$values[$k] = $value['value'];

					// Set max value on load
					if(max($values) <= $value['value']) {
						$max = $value['value'];
						$maxDate = $created_at[$k];
					}

					// Set min value on load
					if(min($values) >= $value['value']) {
						$min = $value['value'];
						$minDate = $created_at[$k];
					}

					// Set last update on load
					if($value['added_at'] > $lastUpdate) {
						$lastUpdate = $value['added_at'];
					}

					$last = $value['value'];
				}
			}else{
				$created_at[$k] = date('d-m-Y H:i', $value['added_at']);
				$values[$k] = $value['value'];

				// Set max value on load
				if(max($values) <= $value['value']) {
					$max = $value['value'];
					$maxDate = $created_at[$k];
				}

				// Set min value on load
				if(min($values) >= $value['value']) {
					$min = $value['value'];
					$minDate = $created_at[$k];
				}

				// Set last update on load
				if($value['added_at'] > $lastUpdate) {
					$lastUpdate = $value['added_at'];
				}

				$last = $value['value'];
			}
			$remaining--;
		}
	}


	// Set avg value on load
	$count = count($values);
	if($count > 0) {
		$avg = array_sum($values) / $count;
	}

	@endphp

	$('#max-{{ $sensor->id }}').html('{{ $max }}');
	$('#max-{{ $sensor->id }}-date').html('{{ $maxDate }}');
	$('#min-{{ $sensor->id }}').html('{{ $min }}');
	$('#min-{{ $sensor->id }}-date').html('{{ $minDate }}');
	$('.unit-{{ $sensor->id }}').html('{{ $sensor->unit }}');
	$('#avg-{{ $sensor->id }}').html({{ number_format($avg, 2, '.', ',') }}.toString());
	$('#count-{{ $sensor->id }}').val('{{ $count }}');
	$('#last-num-{{ $sensor->id }}').val('{{ $lastUpdate }}');
	$('#happy-hardcore-{{ $sensor->id }}').html('{{ $last . ' ' . $sensor->unit }}');

	@if($lastUpdate == 0)
	$('#last-{{ $sensor->id }}').html('-');
	@else
	$('#last-{{ $sensor->id }}').html('{{ date('d-m-Y H:i', $lastUpdate) }}');
	@endif

	var config_{{ $sensor->id }} = {
		type: 'line',
		data: {
			labels: {!! json_encode(array_values($created_at)) !!},
			datasets: [{ 
				data: {!! json_encode(array_values($values)) !!},
				label: "{{ $sensor->name }}",
				borderColor: "{{ randColor() }}",
				fill: false
			}
			]
		},
		options: {
			legend: {
				display: false,
			},
			title: {
				display: true,
				text: '{{ $sensor->name }} ({{ $sensor->unit }})'
			},
			responsive: true,
			maintainAspectRatio: false,
			annotation: {
				annotations: [
				@if($sensor->annotations)
				@foreach($sensor->annotations as $ann)
				{
					type: 'line',
					mode: 'vertical',
					scaleID: 'x-axis-0',
					value: "{{ $ann->label }}",
					borderWidth: 1,
					borderColor: 'black',
					borderDash: [2, 2],
					label: {
						backgroundColor: 'rgba(0,0,0,0.7)',
						fontFamily: "sans-serif",
						fontSize: 10,
						fontStyle: "bold",
						fontColor: "#fff",
						xPadding: 6,
						yPadding: 6,
						cornerRadius: 6,
						position: "center",
						xAdjust: 0,
						yAdjust: 0,
						enabled: true,
						content: "{{ $ann->value }}",
						rotation: 90
					},
				},
				@endforeach
				@endif
				],
			}
		}
	}

	var context_{{ $sensor->id }} = document.getElementById("line-chart-{{ $sensor->id }}").getContext("2d");
	var chart_{{ $sensor->id }} = new Chart(context_{{ $sensor->id }}, config_{{ $sensor->id }});

	@endforeach
	@endif
</script>

<script type="text/javascript">
	function addData(sensor_id, label, time, data, status_name, status_color) {
		window["chart_" + sensor_id].data.labels.push(label);
		window["chart_" + sensor_id].data.datasets[0].data.push(data);
		window["chart_" + sensor_id].update();
		updateView(sensor_id, data, time, label, status_name, status_color);
	}

	function updateView(sensor_id, data, time, label, status_name, status_color) {
		var context_max = $('#max-' + sensor_id);
		var context_min = $('#min-' + sensor_id);
		var context_avg = $('#avg-' + sensor_id);
		var context_count = $('#count-' + sensor_id);
		var context_last = $('#last-' + sensor_id);
		var context_last_num = $('#last-num-' + sensor_id);
		var context_current = $('#happy-hardcore-' + sensor_id);
		var context_unit = $('#unit-' + sensor_id);
		var context_status_name = $('#status-name-' + sensor_id);
		var context_status_dot = $('#status-dot-' + sensor_id);

		var current_max = parseInt(context_max.html());
		var current_min = parseInt(context_min.html());
		var current_avg = parseFloat(context_avg.html());
		var current_count = parseInt(context_count.val());
		var current_last_num = parseInt(context_last_num.val());

		context_current.html(data + ' ' + context_unit.val());
		context_status_name.html(status_name);
		context_status_name.css("color", status_color);
		context_status_dot.css("background-color", status_color);

		// Set new max with ajax
		if(data > current_max) {
			context_max.html(data);
		}

		// set new min with ajax
		if(data < current_min) {
			context_min.html(data);
		}

		// Calculate and set new average with ajax
		var new_avg = ((current_avg * current_count) + data) / (current_count + 1);
		context_avg.html(parseFloat(new_avg).toFixed(2).toString());
		context_count.val(current_count + 1);

		// set new last updated with ajax
		if(parseInt(time) > current_last_num) {
			if(parseInt(time) == 0) {
				context_last.html('-');
			}else{
				context_last.html(label);
			}
			context_last_num.val(time);
		}
	}

	function getMinLastUpdate() {
		var lastArray = [];
		$('.last-num').each(function(i, obj) {
			var value = $(this).val();
			if(value > 0) {
				lastArray.push(value);
			}
		});

		return Math.min.apply(Math, lastArray);
	}

	function popLastTick(sensor_id) {
		window["chart_" + sensor_id].data.labels.splice(0,1);
		window["chart_" + sensor_id].data.datasets[0].data.splice(0,1);
		window["chart_" + sensor_id].update();
		var count_context = $('#count-' + sensor_id);
		count_context.val(parseInt(count_context.val()) - 1);
	}

	function copyToClipboard(text, el) {
		var copyTest = document.queryCommandSupported('copy');
		var elOriginalText = el.attr('data-original-title');

		if (copyTest === true) {
			var copyTextArea = document.createElement("textarea");
			copyTextArea.value = text;
			document.body.appendChild(copyTextArea);
			copyTextArea.select();
			try {
				var successful = document.execCommand('copy');
				var msg = successful ? 'Berhasil disalin!' : 'Ups, gagal disaslin!';
				el.attr('data-original-title', msg).tooltip('show');
			} catch (err) {
				console.log('Ups, tidak bisa disalin');
			}
			document.body.removeChild(copyTextArea);
			el.attr('data-original-title', elOriginalText);
		} else {
			window.prompt("Salin key: Ctrl+C atau Command+C, Enter", text);
		}
	}

	function liveUpdate() {
		$.ajax({
			type:'GET',
			url:'/ajax/live/{{ $thisNode->id }}/' + getMinLastUpdate(),
			success:function(data){
				let oldNotifCount = 0;
				if($('#notif-badge').length) {
					oldNotifCount = parseInt($('#notif-badge').html());
				}
				
				if(data[0] == 0) {
					$('#notif-badge').remove();
				}else{
					$('#dropdownNotificationButton').html('<i class="fa fa-bell"></i> <span id="notif-badge" class="badge badge-secondary">' + data[0] + '</span>');
				}

				let newNotifCount = 0;
				if($('#notif-badge').length) {
					newNotifCount = parseInt($('#notif-badge').html());
				}

				if(oldNotifCount != newNotifCount && newNotifCount != 0) {
					console.log('show notif');
				}

				for (var i = 0; i < data[1].length; i++) {
					var sensor_id = data[1][i].sensor_id;
					var last_update = parseInt($('#last-num-' + sensor_id).val());
					var time = data[1][i].added_at;

					if(time > last_update) {
						var label = data[1][i].label;
						var sensor_value = data[1][i].data;
						var status_name = data[1][i].status_name;
						var status_color = data[1][i].status_color;
						addData(sensor_id, label, time, sensor_value, status_name, status_color);

						@if(isset($_GET['limit']) && is_numeric($_GET['limit']))
						var limit = {{ $_GET['limit'] }};
						var count = parseInt($('#count-' + sensor_id).val());

						if(count > limit) {
							popLastTick(sensor_id);
						}
						@endif
					}
				}
			}
		});
	}

	$(document).ready(function() {
		setInterval(liveUpdate,2000);

		$('.js-tooltip').tooltip();
		$('.js-copy').click(function() {
			var text = $(this).attr('data-copy');
			var el = $(this);
			copyToClipboard(text, el);
		});

		let hash = document.location.hash;
		$(hash + '-tab').trigger('click');
		console.log(hash);

		$(function() {
			$('[data-toggle="tooltip"]').tooltip()
		})

		$(".status-info").mouseover(function() {
			var isRecording = $(this).attr('recording');
			if(isRecording == 'true') {
				$(this).html('Klik untuk pause');
				$(this).removeClass('text-success');
			}else{
				$(this).html('Klik untuk record');
				$(this).removeClass('text-danger');
			}
		}).mouseout(function() {
			var isRecording = $(this).attr('recording');
			if(isRecording == "true") {
				$(this).addClass('text-success');
				$(this).html('Status: Recording');	
			}else{
				$(this).addClass('text-danger');
				$(this).html('Status: Paused');
			}
			
		});

		$('.status-info').click(function() {
			var sensorId = $(this).attr('status-for-sensor');
			var isRecording = $(this).attr('recording');
			if(isRecording == "true") {
				$(this).removeClass('text-success');
				$(this).addClass('text-danger');
				$(this).attr('recording', 'false');
				$.get('/change-status/' + sensorId + '/2');
			}else{
				$(this).addClass('text-success');
				$(this).removeClass('text-danger');
				$(this).attr('recording', 'true');
				$.get('/change-status/' + sensorId + '/1');
			}
		});
	});
</script>
@endsection
