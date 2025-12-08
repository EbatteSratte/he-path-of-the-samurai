@extends('layouts.app')

@section('content')
<div class="container pb-5 fade-in">
  <div class="text-center mb-5">
    <h1 class="display-4 fw-bold">Space Dashboard</h1>
    <p class="lead text-muted">Единый центр управления космическими данными</p>
  </div>

  <div class="row g-4">
    <div class="col-md-6 col-lg-3">
      <a href="/iss" class="text-decoration-none">
        <div class="card h-100 shadow-sm text-center p-4 hover-card border-primary border-opacity-25">
          <div class="fs-1 mb-3">🛰️</div>
          <h3 class="h5 text-light">МКС Трекер</h3>
          <p class="text-muted small">Отслеживание положения, скорости и высоты МКС в реальном времени.</p>
        </div>
      </a>
    </div>

    <div class="col-md-6 col-lg-3">
      <a href="/jwst" class="text-decoration-none">
        <div class="card h-100 shadow-sm text-center p-4 hover-card border-warning border-opacity-25">
          <div class="fs-1 mb-3">🔭</div>
          <h3 class="h5 text-light">Галерея JWST</h3>
          <p class="text-muted small">Последние снимки с телескопа Джеймс Уэбб.</p>
        </div>
      </a>
    </div>

    <div class="col-md-6 col-lg-3">
      <a href="/astro" class="text-decoration-none">
        <div class="card h-100 shadow-sm text-center p-4 hover-card border-info border-opacity-25">
          <div class="fs-1 mb-3">🌠</div>
          <h3 class="h5 text-light">Астро-события</h3>
          <p class="text-muted small">Календарь астрономических явлений для вашей локации.</p>
        </div>
      </a>
    </div>

    <div class="col-md-6 col-lg-3">
      <a href="/osdr" class="text-decoration-none">
        <div class="card h-100 shadow-sm text-center p-4 hover-card border-success border-opacity-25">
          <div class="fs-1 mb-3">🧬</div>
          <h3 class="h5 text-light">OSDR Данные</h3>
          <p class="text-muted small">Open Science Data Repository - биологические эксперименты.</p>
        </div>
      </a>
    </div>
  </div>
</div>

<style>
  .hover-card { transition: transform 0.2s, box-shadow 0.2s; }
  .hover-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>
@endsection
