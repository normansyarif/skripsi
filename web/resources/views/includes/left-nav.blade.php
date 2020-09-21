<ul>
  <li class="nav-item">
    <span class="username">{{ Auth::user()->name }}</span>
  </li>
  <li class="nav-item">
    <a href="javascript:void(0)" onclick="markNotif('{{ count(Auth::user()->unreadNotifications) }}')" class=" icon-cogs-menu" id="dropdownNotificationButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="fa fa-bell"></i> 
      @if(count(Auth::user()->unreadNotifications) > 0)
      <span id="notif-badge" class="badge badge-secondary">{{ count(Auth::user()->unreadNotifications) }}</span>
      @endif
    </a>
    <ul class="dropdown-menu dropdown-notif" aria-labelledby="dropdownNotificationButton">
      <li class="dropdown-item notif-label notif-label-top">
        <p style="margin:0"><span>Notifikasi</span></p>
      </li>
      
      <div class="notif-wrapper"></div>

      <li class="dropdown-item notif-label notif-label-bottom">
        <p><a href="{{ route('notif.index') }}" style="color: #1dc3b6; font-weight: normal; text-decoration: underline; line-height: 0">Lihat semua</a></p>
      </li>

    </ul>
  </li>
  <li class="nav-item">
    <a href="#" class=" icon-cogs-menu dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
      <li class="dropdown-item">
        <a href="{{ route('profile.edit') }}"><i class="fa fa-cogs" aria-hidden="true"></i> Pengaturan</a>
      </li>
      <li class="dropdown-item">
        <a href="javascript:void(0)"
        onclick="event.preventDefault();
        document.getElementById('logout-form').submit();
        "><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          {{ csrf_field() }}
        </form>
      </li>
    </ul>
  </li>
</ul>

