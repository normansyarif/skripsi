<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title')</title>

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main.css') }}">

  <link rel="stylesheet" type="text/css" href="{{ asset('css/chart.css') }}">
  <script type="text/javascript" src="{{ asset('js/chart.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/chartjs-annotation.js') }}"></script>
</head>
<body>
  <nav class="navbar navbar-midnight-blue">
    <ul class="left-nav">
      <li id="btn-sidebars" class="nav-item"><span class="control-panel-title"><i class="fa fa-bars" aria-hidden="true"></i> Daftar Node</span></li>
      <li><a href="{{ route('dashboard') }}">HOME</a></li>
    </ul>
    @include('includes.left-nav')
  </nav>

  <div id="sidebar" class="main-sidebar navbar-midnight-blue show-sidebar">
    <ul class="navbar">

      @if($nodes)
      @foreach($nodes as $node)
      <li class="{{ (Request::is('view/' . $node->id)) ? 'active' : '' }}"><a {!! (Request::is('view/' . $node->id)) ? 'style="font-weight: bold"' : '' !!} href="{{ route('node.view', $node->id) }}">{{ $node->name }}</a></li>
      @endforeach
      @endif

    </ul>
  </div>

  @yield('content')

  <script src="{{ asset('js/jquery.js') }}"></script>
  <script src="{{ asset('js/popper.js') }}"></script>
  <script src="{{ asset('js/bootstrap.js') }}"></script>
  <script src="{{ asset('js/toastr.min.js') }}"></script>

  <script type="text/javascript">
    function markNotif(count) {
      var expaneded = $('#dropdownNotificationButton').attr('aria-expanded');

      if(expaneded == 'false') {
        $('.notif-wrapper').html('<p style="text-align: center; margin-top: 160px; color: #acacac">Memuat notifikasi...</p>');
        $.ajax({
          type:'GET',
          url:'/ajax/get-notif',
          success:function(data){
            $('.notif-wrapper').html(data);
            $.get('/markasread');
            if($('#notif-badge').length) {
              $('#notif-badge').remove();
            }
          },
          error: function($e) {
            $('.notif-wrapper').html('<p style="text-align: center; margin-top: 160px; color: #acacac">Gagal memuat notifikasi</p>');
          }
        });
      }
    } 
  </script>

  @yield('scripts')
</body>
</html>
