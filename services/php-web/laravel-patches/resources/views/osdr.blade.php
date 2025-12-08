@extends('layouts.app')

@section('content')
<div class="container pb-5 fade-in">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="mb-1">NASA OSDR</h2>
      <div class="small text-muted">Open Science Data Repository</div>
    </div>
    <div class="text-end">
      <div class="d-flex align-items-center justify-content-end gap-2 mb-2">
        <div class="input-group input-group-sm" style="width: 250px;">
            <input type="text" id="searchInput" class="form-control bg-dark text-light border-secondary" placeholder="Поиск..." oninput="filterTable()">
            <button class="btn btn-outline-secondary" type="button" onclick="filterTable()">🔍</button>
        </div>
        <div class="btn-group btn-group-sm">
            <button onclick="setLimit(20)" class="btn btn-outline-secondary limit-btn" data-limit="20">20</button>
            <button onclick="setLimit(50)" class="btn btn-outline-secondary limit-btn active" data-limit="50">50</button>
            <button onclick="setLimit(100)" class="btn btn-outline-secondary limit-btn" data-limit="100">100</button>
            <button onclick="setLimit(1000)" class="btn btn-outline-secondary limit-btn" data-limit="1000">All</button>
        </div>
      </div>
      <div class="d-flex justify-content-end align-items-center gap-3">
          <div class="small text-muted" id="countDisplay"></div>
          <div class="small text-muted">Источник: {{ $src }}</div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0 bg-white rounded">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>
                <a href="?sort=id&dir={{ $sort==='id' && $dir==='asc' ? 'desc' : 'asc' }}" class="text-decoration-none text-dark d-block">
                  # {!! $sort==='id' ? ($dir==='asc'?'↑':'↓') : '' !!}
                </a>
              </th>
              <th>
                <a href="?sort=dataset_id&dir={{ $sort==='dataset_id' && $dir==='asc' ? 'desc' : 'asc' }}" class="text-decoration-none text-dark d-block">
                  Dataset ID {!! $sort==='dataset_id' ? ($dir==='asc'?'↑':'↓') : '' !!}
                </a>
              </th>
              <th>
                <a href="?sort=title&dir={{ $sort==='title' && $dir==='asc' ? 'desc' : 'asc' }}" class="text-decoration-none text-dark d-block">
                  Title {!! $sort==='title' ? ($dir==='asc'?'↑':'↓') : '' !!}
                </a>
              </th>
              <th class="text-dark">REST_URL</th>
              <th>
                <a href="?sort=updated_at&dir={{ $sort==='updated_at' && $dir==='asc' ? 'desc' : 'asc' }}" class="text-decoration-none text-dark d-block">
                  Updated {!! $sort==='updated_at' ? ($dir==='asc'?'↑':'↓') : '' !!}
                </a>
              </th>
              <th>
                <a href="?sort=inserted_at&dir={{ $sort==='inserted_at' && $dir==='asc' ? 'desc' : 'asc' }}" class="text-decoration-none text-dark d-block">
                  Inserted {!! $sort==='inserted_at' ? ($dir==='asc'?'↑':'↓') : '' !!}
                </a>
              </th>
              <th class="text-dark">Raw</th>
            </tr>
          </thead>
          <tbody id="osdr-table-body">
          @forelse($items as $row)
            <tr class="osdr-row">
              <td class="text-dark">{{ $row['id'] }}</td>
              <td class="fw-semibold text-dark">{{ $row['dataset_id'] ?? '—' }}</td>
              <td class="text-dark" style="max-width:350px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $row['title'] ?? '' }}">
                {{ $row['title'] ?? '—' }}
              </td>
              <td>
                @if(!empty($row['rest_url']))
                  <a href="{{ $row['rest_url'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary py-0 px-1">Link</a>
                @else <span class="text-dark">—</span> @endif
              </td>
              <td class="small text-dark">{{ $row['updated_at'] ?? '—' }}</td>
              <td class="small text-dark">{{ $row['inserted_at'] ?? '—' }}</td>
              <td>
                <button class="btn btn-light btn-sm border" type="button" data-bs-toggle="collapse" data-bs-target="#raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
                  JSON
                </button>
              </td>
            </tr>
            <tr class="osdr-detail collapse bg-light" id="raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
              <td colspan="7" class="p-3">
                <pre class="mb-0 small text-dark" style="max-height:300px;overflow:auto">{{ json_encode($row['raw'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-dark p-4">нет данных</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
  (function() {
    let currentLimit = 50;
    
    function init() {
        updateLimitButtons(currentLimit);
        filterTable();
    }

    window.setLimit = function(limit) {
        currentLimit = limit;
        updateLimitButtons(limit);
        filterTable();
    }

    window.filterTable = function() {
        const input = document.getElementById('searchInput');
        const filter = input ? input.value.toLowerCase() : '';
        const tbody = document.getElementById('osdr-table-body');
        if (!tbody) {
            console.error('Table body not found');
            return;
        }
        
        const rows = tbody.getElementsByClassName('osdr-row');
        let visibleCount = 0;
        let totalMatches = 0;

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const detailRow = row.nextElementSibling; 
            
            const mainText = row.textContent.toLowerCase();
            const detailText = detailRow ? detailRow.textContent.toLowerCase() : '';
            const matches = mainText.includes(filter) || detailText.includes(filter);
            
            if (matches) {
                totalMatches++;
                if (visibleCount < currentLimit) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            } else {
                row.style.display = 'none';
            }
            
            if (detailRow && detailRow.classList.contains('osdr-detail')) {
                if (row.style.display === 'none') {
                    detailRow.style.display = 'none';
                    detailRow.classList.remove('show');
                } else {
                    detailRow.style.display = '';
                }
            }
        }
        
        const countDisplay = document.getElementById('countDisplay');
        if (countDisplay) {
            countDisplay.textContent = `Показано ${visibleCount} из ${totalMatches} (Всего: ${rows.length})`;
        }
    }

    function updateLimitButtons(limit) {
        document.querySelectorAll('.limit-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.limit == limit) {
                btn.classList.add('active');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
  })();
  </script>
</div>
@endsection
