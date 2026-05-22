<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title','Portal Nasabah') – Bank Sampah RW 042</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root { --green: #16a34a; }
body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }
.topnav {
  background: linear-gradient(135deg, #0f172a, #134e26);
  padding: 0 24px; height: 56px;
  display: flex; align-items: center; justify-content: space-between;
  position: sticky; top: 0; z-index: 50;
}
.brand { color: #fff; font-weight: 700; font-size: 15px; }
.brand small { color: #86efac; font-size: 11px; display: block; font-weight: 400; }
.nav-tabs .nav-link { font-size: 13px; color: #64748b; border: none; padding: 10px 16px; }
.nav-tabs .nav-link.active { color: var(--green); border-bottom: 2px solid var(--green); font-weight: 600; }
.page-body { padding: 24px; max-width: 860px; margin: 0 auto; }
.stat-card {
  background: #fff; border-radius: 14px; padding: 22px;
  border: 1px solid #e2e8f0;
}
.saldo-card {
  background: linear-gradient(135deg, #16a34a, #15803d);
  border-radius: 16px; padding: 28px; color: #fff;
}
.table-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
.table thead th {
  background: #f8fafc; font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .6px; color: #64748b; border: none;
}
.table > :not(caption) > * > * { padding: 11px 16px; }
.table tbody tr { border-color: #f1f5f9; }
</style>
</head>
<body>
<nav class="topnav">
  <div class="brand">
    🌿 Bank Sampah RW 042
    <small>Portal Nasabah</small>
  </div>
  <div class="d-flex align-items-center gap-3">
    <span style="color:#86efac;font-size:13px">{{ auth()->user()->name }}</span>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-sm"
              style="background:rgba(255,255,255,.15);color:#fff;border:none;font-size:12px">
        <i class="bi bi-box-arrow-left me-1"></i>Logout
      </button>
    </form>
  </div>
</nav>

<div class="page-body">
  <ul class="nav nav-tabs mb-4 border-0">
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('nasabah.dashboard') ? 'active' : '' }}" href="{{ route('nasabah.dashboard') }}">
        <i class="bi bi-grid-1x2 me-1"></i> Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('nasabah.riwayat') ? 'active' : '' }}" href="{{ route('nasabah.riwayat') }}">
        <i class="bi bi-clock-history me-1"></i> Riwayat
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('nasabah.profil.*') ? 'active' : '' }}" href="{{ route('nasabah.profil.edit') }}">
        <i class="bi bi-person-circle me-1"></i> Profil Saya
      </a>
    </li>
  </ul>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>