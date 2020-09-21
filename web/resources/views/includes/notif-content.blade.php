<style type="text/css">
  .unread {
    background-color: #1dc3b6;
  }
  .unread:hover {
    background-color: #006a5c;
  }
  .unread th, .unread td {
    color: #fff;
  }
</style>

@forelse(Auth::user()->notifications as $notif)
<li class="dropdown-item {{ ($notif->read_at == null) ? 'unread' : '' }}">
  <a class="item-notif" href="{{ route('node.view', $notif["data"]["node_id"]) }}">
    <table style="width: 100%">
      <tr>
        <th style="width: 20%">Node</th>
        <td style="width: 30%">{{ $notif["data"]["node"] }}</td>
        <th style="width: 20%">Status</th>
        <td style="width: 30%">{{ $notif["data"]["status"] }}</td>
      </tr>
      <tr>
        <th>Sensor</th>
        <td>{{ $notif["data"]["sensor"] }}</td>
        <th>Nilai</th>
        <td>{{ $notif["data"]["value"] . ' ' . $notif["data"]["unit"] }}</td>
      </tr>
      <tr>
        <td colspan="4" class="notif-time">{{ date("j M Y, H:i", strtotime($notif["created_at"])) }}</td>
      </tr>
    </table>
  </a>
</li>
@empty
<p style="text-align: center; margin-top: 160px; color: #acacac">Tidak ada notifikasi</p>
@endforelse
