@extends('layouts.app')

@section('content')
<div class="container pb-5 fade-in">
  <h2 class="mb-4">Астрономические события</h2>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="card-title m-0">Поиск событий (AstronomyAPI)</h5>
        <form id="astroForm" class="row g-2 align-items-center">
          <div class="col-auto">
            <input type="number" step="0.0001" class="form-control form-control-sm" name="lat" value="55.7558" placeholder="lat">
          </div>
          <div class="col-auto">
            <input type="number" step="0.0001" class="form-control form-control-sm" name="lon" value="37.6176" placeholder="lon">
          </div>
          <div class="col-auto">
            <input type="number" min="1" max="30" class="form-control form-control-sm" name="days" value="7" style="width:90px" title="дней">
          </div>
          <div class="col-auto">
            <button class="btn btn-sm btn-primary" type="submit">Показать</button>
          </div>
        </form>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle" id="astroTable">
          <thead class="table-light">
            <tr>
              <th style="cursor:pointer" onclick="sortTable(0)"># ↕</th>
              <th style="cursor:pointer" onclick="sortTable(1)">Тело ↕</th>
              <th style="cursor:pointer" onclick="sortTable(2)">Событие ↕</th>
              <th style="cursor:pointer" onclick="sortTable(3)">Когда (UTC) ↕</th>
              <th style="cursor:pointer" onclick="sortTable(4)">Дополнительно ↕</th>
            </tr>
          </thead>
          <tbody id="astroBody">
            <tr><td colspan="5" class="text-muted">нет данных</td></tr>
          </tbody>
        </table>
      </div>

      <details class="mt-3">
        <summary class="text-muted small">Полный JSON ответ</summary>
        <pre id="astroRaw" class="bg-light rounded p-2 small m-0 mt-2" style="white-space:pre-wrap; max-height: 300px; overflow: auto;"></pre>
      </details>
    </div>
  </div>
</div>

<script>
  // Функция сортировки таблицы
  function sortTable(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("astroTable");
    switching = true;
    dir = "asc";
    while (switching) {
      switching = false;
      rows = table.rows;
      for (i = 1; i < (rows.length - 1); i++) {
        shouldSwitch = false;
        x = rows[i].getElementsByTagName("TD")[n];
        y = rows[i + 1].getElementsByTagName("TD")[n];
        if (dir == "asc") {
          if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
            shouldSwitch = true;
            break;
          }
        } else if (dir == "desc") {
          if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
            shouldSwitch = true;
            break;
          }
        }
      }
      if (shouldSwitch) {
        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
        switching = true;
        switchcount ++;
      } else {
        if (switchcount == 0 && dir == "asc") {
          dir = "desc";
          switching = true;
        }
      }
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('astroForm');
    const body = document.getElementById('astroBody');
    const raw  = document.getElementById('astroRaw');

    function normalize(node){
      const name = node.name || node.body || node.object || node.target || '';
      const type = node.type || node.event_type || node.category || node.kind || '';
      const when = node.time || node.date || node.occursAt || node.peak || node.instant || '';
      const extra = node.magnitude || node.mag || node.altitude || node.note || '';
      return {name, type, when, extra};
    }

    function collect(root){
      const rows = [];
      (function dfs(x){
        if (!x || typeof x !== 'object') return;
        if (Array.isArray(x)) { x.forEach(dfs); return; }
        if ((x.type || x.event_type || x.category) && (x.name || x.body || x.object || x.target)) {
          rows.push(normalize(x));
        }
        Object.values(x).forEach(dfs);
      })(root);
      return rows;
    }

    async function load(q){
      body.innerHTML = '<tr><td colspan="5" class="text-muted">Загрузка…</td></tr>';
      const url = '/api/astro/events?' + new URLSearchParams(q).toString();
      try{
        const r  = await fetch(url);
        const js = await r.json();
        raw.textContent = JSON.stringify(js, null, 2);

        const rows = collect(js);
        if (!rows.length) {
          body.innerHTML = '<tr><td colspan="5" class="text-muted">события не найдены</td></tr>';
          return;
        }
        body.innerHTML = rows.slice(0,200).map((r,i)=>`
          <tr>
            <td>${i+1}</td>
            <td class="fw-semibold">${r.name || '—'}</td>
            <td>${r.type || '—'}</td>
            <td><code>${r.when || '—'}</code></td>
            <td>${r.extra || ''}</td>
          </tr>
        `).join('');
      }catch(e){
        body.innerHTML = '<tr><td colspan="5" class="text-danger">ошибка загрузки</td></tr>';
      }
    }

    form.addEventListener('submit', ev=>{
      ev.preventDefault();
      const q = Object.fromEntries(new FormData(form).entries());
      load(q);
    });

    load({lat: form.lat.value, lon: form.lon.value, days: form.days.value});
  });
</script>
@endsection
