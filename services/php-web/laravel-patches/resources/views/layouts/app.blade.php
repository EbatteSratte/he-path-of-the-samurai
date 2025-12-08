<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Space Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <style>
    body {
      background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
      color: #e0e0e0;
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .navbar {
      background-color: rgba(0, 0, 0, 0.6) !important;
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .navbar-brand, .nav-link {
      color: #e0e0e0 !important;
      transition: color 0.2s;
    }
    .nav-link:hover {
      color: #fff !important;
      text-shadow: 0 0 10px rgba(255,255,255,0.5);
    }
    .card {
      background-color: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #fff;
    }
    .card-header {
      background-color: rgba(255, 255, 255, 0.05);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .text-muted {
      color: #adb5bd !important;
    }
    .text-dark {
      color: #212529 !important;
    }
    .bg-light {
      background-color: rgba(255, 255, 255, 0.1) !important;
    }
    .form-control, .form-select {
      background-color: rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #fff;
    }
    .form-control:focus, .form-select:focus {
      background-color: rgba(0, 0, 0, 0.3);
      color: #fff;
      border-color: #80bdff;
      box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    }
    .table {
      color: #212529;
    }
    .table-hover tbody tr:hover {
      color: #212529;
      background-color: rgba(255,255,255,0.9);
    }
    
    #map{height:340px}
    .fade-in { animation: fadeIn 0.8s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .card { transition: transform 0.2s, box-shadow 0.2s; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
  </style>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary mb-3">
  <div class="container">
    <a class="navbar-brand" href="/dashboard">Dashboard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="/iss">МКС</a></li>
        <li class="nav-item"><a class="nav-link" href="/jwst">JWST</a></li>
        <li class="nav-item"><a class="nav-link" href="/astro">События</a></li>
        <li class="nav-item"><a class="nav-link" href="/osdr">OSDR</a></li>
      </ul>
    </div>
  </div>
</nav>
@yield('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
