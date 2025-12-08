@extends('layouts.app')

@section('content')
<div class="container pb-5 fade-in">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="mb-1">NASA OSDR</h2>
      <div class="small text-muted">Open Science Data Repository</div>
    </div>
    <div class="text-end">
      <div class="small text-muted mb-1">Источник: {{ $src }}</div>
      <div class="btn-group btn-group-sm">
        <a href="?limit=20&sort={{$sort}}&dir={{$dir}}" class="btn btn-outline-secondary">20</a>
        <a href="?limit=50&sort={{$sort}}&dir={{$dir}}" class="btn btn-outline-secondary">50</a>
        <a href="?limit=100&sort={{$sort}}&dir={{$dir}}" class="btn btn-outline-secondary">100</a>
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
          <tbody>
          @forelse($items as $row)
            <tr>
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
            <tr class="collapse bg-light" id="raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
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
</div>
@endsection
