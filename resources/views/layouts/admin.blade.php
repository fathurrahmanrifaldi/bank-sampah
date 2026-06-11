<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') – Bank Sampah RW 042</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link rel="icon" type="image" href="{{ asset('images/logo.png') }}">
<style>
:root {
  --sidebar-w: 240px;
  --green: #16a34a;
  --green-light: #dcfce7;
}
body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }

/* Sidebar */
.sidebar {
  width: var(--sidebar-w);
  height: 100vh;
  position: fixed; top: 0; left: 0;
  background: #0f172a;
  display: flex; flex-direction: column;
  z-index: 100;
  transition: transform .3s;
}
.sidebar-brand {
  padding: 20px 20px 16px;
  border-bottom: 1px solid #1e293b;
}
.logo {
  width: 40px; height: 40px; border-radius: 10px;
  margin-bottom: 8px;
}
.sidebar-brand h6 {
  color: #fff; font-size: 13px; font-weight: 700;
  margin: 0; line-height: 1.4;
}
.sidebar-brand small { color: #64748b; font-size: 11px; }
.sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }
.nav-section {
  padding: 8px 16px 4px;
  font-size: 10px; font-weight: 700; letter-spacing: 1.2px;
  color: #475569; text-transform: uppercase;
}
.sidebar-nav .nav-link {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 20px;
  color: #94a3b8; font-size: 13.5px;
  border-radius: 0;
  transition: all .15s;
  text-decoration: none;
}
.sidebar-nav .nav-link:hover { color: #fff; background: #1e293b; }
.sidebar-nav .nav-link.active {
  color: #fff;
  background: linear-gradient(90deg, #16a34a22, transparent);
  border-left: 3px solid var(--green);
}
.sidebar-nav .nav-link i { font-size: 16px; width: 20px; text-align: center; }
.sidebar-footer {
  padding: 12px 16px;
  border-top: 1px solid #1e293b;
}

/* Main */
.main-content {
  margin-left: var(--sidebar-w);
  min-height: 100vh;
}
.topbar {
  background: #fff;
  border-bottom: 1px solid #e2e8f0;
  padding: 0 24px;
  height: 56px;
  display: flex; align-items: center; justify-content: space-between;
  position: sticky; top: 0; z-index: 50;
}
.topbar-title { font-size: 15px; font-weight: 600; color: #0f172a; }
.topbar-user {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; color: #475569;
}
.avatar {
  width: 32px; height: 32px; border-radius: 50%;
  background: var(--green); color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700;
}
.page-body { padding: 24px; }

/* Cards */
.stat-card {
  background: #fff;
  border-radius: 12px;
  padding: 20px 22px;
  border: 1px solid #e2e8f0;
  transition: box-shadow .2s;
}
.stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.07); }
.stat-icon {
  width: 44px; height: 44px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
}
.stat-value { font-size: 24px; font-weight: 700; color: #0f172a; }
.stat-label { font-size: 12px; color: #64748b; margin-top: 2px; }
.stat-change { font-size: 11px; margin-top: 6px; }

/* Table */
.table-card {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
}
.table-card .card-header {
  padding: 14px 20px;
  background: #fff;
  border-bottom: 1px solid #e2e8f0;
  display: flex; align-items: center; justify-content: space-between;
}
.table-card .card-header h6 { margin: 0; font-size: 14px; font-weight: 600; }
.table > :not(caption) > * > * { padding: 11px 16px; }
.table thead th {
  background: #f8fafc;
  font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .6px;
  color: #64748b; border: none;
}
.table tbody tr { border-color: #f1f5f9; }
.table tbody tr:hover { background: #f8fafc; }

/* Badge */
.badge-emas    { background: #fef3c7; color: #92400e; }
.badge-perak   { background: #f1f5f9; color: #374151; }
.badge-perunggu{ background: #fed7aa; color: #92400e; }

/* Alert */
.alert { border: none; border-radius: 10px; font-size: 13.5px; }

/* Responsive */
@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); }
  .sidebar.show { transform: translateX(0); box-shadow: 4px 0 15px rgba(0,0,0,0.1); }
  .main-content { margin-left: 0; }
  .topbar { padding: 0 16px; }
  .page-body { padding: 16px; }
  .sidebar-overlay {
    display: none;
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5); z-index: 90;
  }
  .sidebar-overlay.show { display: block; }
}
.table-card { overflow-x: auto; }
</style>
@stack('head-styles')
</head>
<body>

<!-- SIDEBAR OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    <h6>Bank Sampah<br>RW 042</h6>
    <small>Kelurahan Bahagia, Bekasi</small>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">Menu Utama</div>
    <a href="{{ route('admin.dashboard') }}"
       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <i class="bi bi-grid-1x2"></i> Dashboard
    </a>

    <div class="nav-section">Data Master</div>
    <a href="{{ route('admin.nasabah.index') }}"
       class="nav-link {{ request()->routeIs('admin.nasabah.index') || request()->routeIs('admin.nasabah.edit') || request()->routeIs('admin.nasabah.create') ? 'active' : '' }}">
      <i class="bi bi-people"></i> Nasabah
    </a>
    <a href="{{ route('admin.kategori.index') }}"
       class="nav-link {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
      <i class="bi bi-tags"></i> Kategori Sampah
    </a>

    <div class="nav-section">Operasional</div>
    <a href="{{ route('admin.transaksi.index') }}"
       class="nav-link {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}">
      <i class="bi bi-arrow-left-right"></i> Transaksi Setoran
    </a>
    <a href="{{ route('admin.penarikan-dana.index') }}"
       class="nav-link {{ request()->routeIs('admin.penarikan-dana.*') ? 'active' : '' }}">
      <i class="bi bi-cash-stack"></i> Penarikan Dana
      @php $pendingPenarikan = \App\Models\PenarikanDana::where('status','menunggu')->count(); @endphp
      @if($pendingPenarikan > 0)
        <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size:10px">{{ $pendingPenarikan }}</span>
      @endif
    </a>
    <a href="{{ route('admin.penjualan-pengepul.index') }}"
       class="nav-link {{ request()->routeIs('admin.penjualan-pengepul.*') ? 'active' : '' }}">
      <i class="bi bi-cash-coin"></i> Jual ke Pengepul
    </a>

    <div class="nav-section">Evaluasi</div>
    <a href="{{ route('admin.penilaian.index') }}"
       class="nav-link {{ request()->routeIs('admin.penilaian.*') ? 'active' : '' }}">
      <i class="bi bi-trophy"></i> Nasabah Terbaik
    </a>
    <a href="{{ route('admin.laporan.index') }}"
       class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
      <i class="bi bi-file-earmark-bar-graph"></i> Laporan
    </a>
  </nav>
  <div class="sidebar-footer">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-sm w-100"
              style="background:#1e293b;color:#94a3b8;border:none;">
        <i class="bi bi-box-arrow-left me-1"></i> Logout
      </button>
    </form>
  </div>
</aside>

<!-- MAIN -->
<div class="main-content">
  <div class="topbar">
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-sm d-md-none me-2" id="sidebarToggle" style="background: none; border: none; font-size: 20px; padding: 0;">
        <i class="bi bi-list"></i>
      </button>
      <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
    </div>
    <div class="topbar-user">
      <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
      <div>
        <div style="font-weight:600;font-size:13px">{{ auth()->user()->name }}</div>
        <div style="font-size:11px;color:#94a3b8">Petugas Lapangan</div>
      </div>
    </div>
  </div>

  <div class="page-body">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @yield('content')
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    if(toggleBtn) {
      toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
      });
    }
    if(overlay) {
      overlay.addEventListener('click', function() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
      });
    }
  });
</script>
@stack('scripts')
</body>
</html>