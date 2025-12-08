@extends('layouts.app')

@section('content')
<div class="container pb-5 fade-in">
  <h2 class="mb-4">МКС: Мониторинг</h2>

  {{-- верхние карточки --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="border rounded p-3 text-center bg-light">
      <div class="small text-muted mb-1">Скорость</div>
      <div class="fs-4 fw-bold">{{ isset(($iss['payload'] ?? [])['velocity']) ? number_format($iss['payload']['velocity'],0,'',' ') : '—' }} <small class="fs-6 fw-normal text-muted">км/ч</small></div>
    </div></div>
    <div class="col-6 col-md-3"><div class="border rounded p-3 text-center bg-light">
      <div class="small text-muted mb-1">Высота</div>
      <div class="fs-4 fw-bold">{{ isset(($iss['payload'] ?? [])['altitude']) ? number_format($iss['payload']['altitude'],0,'',' ') : '—' }} <small class="fs-6 fw-normal text-muted">км</small></div>
    </div></div>
  </div>

  <div class="row g-3">
    <div class="col-12">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title">Карта и графики</h5>
          <div id="map" class="rounded mb-3 border" style="height:400px"></div>
          <div class="row g-3">
            <div class="col-md-6"><canvas id="issSpeedChart" height="100"></canvas></div>
            <div class="col-md-6"><canvas id="issAltChart"   height="100"></canvas></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function () {
  if (typeof L !== 'undefined' && typeof Chart !== 'undefined') {
    const last = @json(($iss['payload'] ?? []));
    let lat0 = Number(last.latitude || 0), lon0 = Number(last.longitude || 0);
    const map = L.map('map', { attributionControl:false }).setView([lat0||0, lon0||0], lat0?3:2);
    L.tileLayer('https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png', { noWrap:true }).addTo(map);
    const trail  = L.polyline([], {weight:3}).addTo(map);
    const marker = L.marker([lat0||0, lon0||0]).addTo(map).bindPopup('МКС');

    const speedChart = new Chart(document.getElementById('issSpeedChart'), {
      type: 'line', data: { labels: [], datasets: [{ label: 'Скорость (км/ч)', data: [], borderColor: 'rgb(75, 192, 192)', tension: 0.1 }] },
      options: { responsive: true, scales: { x: { display: false } } }
    });
    const altChart = new Chart(document.getElementById('issAltChart'), {
      type: 'line', data: { labels: [], datasets: [{ label: 'Высота (км)', data: [], borderColor: 'rgb(255, 99, 132)', tension: 0.1 }] },
      options: { responsive: true, scales: { x: { display: false } } }
    });

    async function loadTrend() {
      try {
        const r = await fetch('/api/iss/trend?limit=240');
        const js = await r.json();
        const pts = Array.isArray(js.points) ? js.points.map(p => [p.lat, p.lon]) : [];
        if (pts.length) {
          trail.setLatLngs(pts);
          marker.setLatLng(pts[pts.length-1]);
        }
        const t = (js.points||[]).map(p => new Date(p.at).toLocaleTimeString());
        speedChart.data.labels = t;
        speedChart.data.datasets[0].data = (js.points||[]).map(p => p.velocity);
        speedChart.update();
        altChart.data.labels = t;
        altChart.data.datasets[0].data = (js.points||[]).map(p => p.altitude);
        altChart.update();
      } catch(e) {}
    }
    loadTrend();
    setInterval(loadTrend, 15000);
  }
});
</script>
@endsection
