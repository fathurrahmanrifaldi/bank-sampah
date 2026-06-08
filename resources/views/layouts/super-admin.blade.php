<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') – Super Admin | Bank Sampah RW 042</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
  --sidebar-w: 248px;
  --indigo:        #4f46e5;
  --indigo-light:  #e0e7ff;
  --indigo-dark:   #3730a3;
  --sidebar-bg:    #0d0f1a;
  --sidebar-hover: #1a1d2e;
  --sidebar-active:#1e2240;
}
* { font-family: 'Inter', 'Segoe UI', sans-serif; }
body { background: #f1f5f9; }

/* ── Sidebar ── */
.sidebar {
  width: var(--sidebar-w);
  height: 100vh;
  position: fixed; top: 0; left: 0;
  background: var(--sidebar-bg);
  display: flex; flex-direction: column;
  z-index: 100;
  transition: transform .3s;
  box-shadow: 2px 0 20px rgba(79,70,229,.12);
}
.sidebar-brand {
  padding: 20px 20px 16px;
  border-bottom: 1px solid #1a1d2e;
  background: linear-gradient(135deg, #1a1d2e 0%, #141628 100%);
}
.brand-icon {
  width: 42px; height: 42px; border-radius: 12px;
  background: linear-gradient(135deg, var(--indigo) 0%, #7c3aed 100%);
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; color: #fff; margin-bottom: 10px;
  box-shadow: 0 4px 12px rgba(79,70,229,.4);
}
.sidebar-brand h6 {
  color: #fff; font-size: 13px; font-weight: 700;
  margin: 0; line-height: 1.4;
}
.sidebar-brand small { color: #6b7280; font-size: 11px; }
.role-badge {
  display: inline-flex; align-items: center; gap: 4px;
  background: linear-gradient(90deg, #4f46e5, #7c3aed);
  color: #fff; font-size: 9px; font-weight: 700;
  padding: 2px 8px; border-radius: 20px; letter-spacing: .6px;
  text-transform: uppercase; margin-top: 6px;
}

.sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }
.nav-section {
  padding: 10px 18px 4px;
  font-size: 9.5px; font-weight: 700; letter-spacing: 1.5px;
  color: #4b5563; text-transform: uppercase;
}
.sidebar-nav .nav-link {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 20px;
  color: #9ca3af; font-size: 13.5px;
  transition: all .15s;
  text-decoration: none;
  border-radius: 0;
}
.sidebar-nav .nav-link:hover { color: #e0e7ff; background: var(--sidebar-hover); }
.sidebar-nav .nav-link.active {
  color: #c7d2fe;
  background: linear-gradient(90deg, rgba(79,70,229,.25), transparent);
  border-left: 3px solid var(--indigo);
  font-weight: 600;
}
.sidebar-nav .nav-link i { font-size: 16px; width: 20px; text-align: center; }
.sidebar-footer {
  padding: 12px 16px;
  border-top: 1px solid #1a1d2e;
}

/* ── Main ── */
.main-content { margin-left: var(--sidebar-w); min-height: 100vh; }
.topbar {
  background: #fff;
  border-bottom: 1px solid #e2e8f0;
  padding: 0 24px;
  height: 58px;
  display: flex; align-items: center; justify-content: space-between;
  position: sticky; top: 0; z-index: 50;
  box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.topbar-title { font-size: 15px; font-weight: 600; color: #0f172a; }
.topbar-user {
  display: flex; align-items: center; gap: 10px;
  font-size: 13px; color: #475569;
}
.avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: linear-gradient(135deg, var(--indigo) 0%, #7c3aed 100%);
  color: #fff; display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 700;
  box-shadow: 0 2px 8px rgba(79,70,229,.35);
}
.topbar-role-badge {
  background: var(--indigo-light); color: var(--indigo-dark);
  font-size: 10px; font-weight: 700; padding: 2px 8px;
  border-radius: 20px; letter-spacing: .4px;
}
.page-body { padding: 24px; }

/* ── Cards ── */
.stat-card {
  background: #fff;
  border-radius: 14px;
  padding: 20px 22px;
  border: 1px solid #e2e8f0;
  transition: box-shadow .2s, transform .2s;
}
.stat-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.08); transform: translateY(-2px); }
.stat-icon {
  width: 46px; height: 46px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
}
.stat-value { font-size: 24px; font-weight: 800; color: #0f172a; }
.stat-label { font-size: 12px; color: #64748b; margin-top: 2px; font-weight: 500; }

/* ── Table ── */
.table-card {
  background: #fff;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.table-card .card-header {
  padding: 14px 20px;
  background: #fff;
  border-bottom: 1px solid #e2e8f0;
  display: flex; align-items: center; justify-content: space-between;
}
.table-card .card-header h6 { margin: 0; font-size: 14px; font-weight: 700; color: #0f172a; }
.table > :not(caption) > * > * { padding: 11px 16px; }
.table thead th {
  background: #f8fafc;
  font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .6px;
  color: #64748b; border: none;
}
.table tbody tr { border-color: #f1f5f9; }
.table tbody tr:hover { background: #f8fafc; }

/* ── Badges ── */
.badge-aktif   { background: #dcfce7; color: #15803d; }
.badge-nonaktif { background: #fee2e2; color: #b91c1c; }

/* ── Alerts ── */
.alert { border: none; border-radius: 10px; font-size: 13.5px; }

/* ── Responsive ── */
@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); }
  .sidebar.show { transform: translateX(0); box-shadow: 4px 0 20px rgba(79,70,229,0.2); }
  .main-content { margin-left: 0; }
  .topbar { padding: 0 16px; }
  .page-body { padding: 16px; }
  .sidebar-overlay {
    display: none;
    position: fixed; inset: 0;
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
    <div class="brand-icon">
      <i class="bi bi-shield-fill-check"></i>
    </div>
    <h6>Bank Sampah<br>RW 042</h6>
    <small>Kelurahan Bahagia, Bekasi</small>
    <div><span class="role-badge"><i class="bi bi-star-fill me-1" style="font-size:8px"></i>Super Admin</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">Menu Utama</div>
    <a href="{{ route('super-admin.dashboard') }}"
       class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
      <i class="bi bi-grid-1x2"></i> Dashboard
    </a>

    <div class="nav-section">Manajemen</div>
    <a href="{{ route('super-admin.kelola-admin.index') }}"
       class="nav-link {{ request()->routeIs('super-admin.kelola-admin.*') ? 'active' : '' }}">
      <i class="bi bi-person-badge"></i> Kelola Petugas
      @php $totalAdmin = \App\Models\User::where('role','admin')->count(); @endphp
      <span class="badge ms-auto" style="background:#1e2240;color:#818cf8;font-size:10px">{{ $totalAdmin }}</span>
    </a>
    <a href="{{ route('super-admin.kategori.index') }}"
       class="nav-link {{ request()->routeIs('super-admin.kategori.*') ? 'active' : '' }}">
      <i class="bi bi-tags"></i> Kategori Sampah
    </a>

    <div class="nav-section">Pelaporan</div>
    <a href="{{ route('super-admin.laporan.index') }}"
       class="nav-link {{ request()->routeIs('super-admin.laporan.*') ? 'active' : '' }}">
      <i class="bi bi-bar-chart-line"></i> Laporan Operasional
    </a>
  </nav>
  <div class="sidebar-footer">
    <div style="font-size:11px;color:#6b7280;margin-bottom:8px;padding:0 4px">
      Login sebagai: <strong style="color:#e0e7ff">{{ auth()->user()->name }}</strong>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-sm w-100"
              style="background:#1a1d2e;color:#9ca3af;border:1px solid #2d3150;font-size:12.5px">
        <i class="bi bi-box-arrow-left me-1"></i> Logout
      </button>
    </form>
  </div>
</aside>

<!-- MAIN -->
<div class="main-content">
  <div class="topbar">
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-sm d-md-none me-2" id="sidebarToggle"
              style="background:none;border:none;font-size:20px;padding:0">
        <i class="bi bi-list"></i>
      </button>
      <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
    </div>
    <div class="topbar-user">
      <span class="topbar-role-badge"><i class="bi bi-shield-fill-check me-1"></i>Ketua RW</span>
      <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
      <div>
        <div style="font-weight:700;font-size:13px">{{ auth()->user()->name }}</div>
        <div style="font-size:11px;color:#94a3b8">Super Administrator</div>
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
    const sidebar  = document.querySelector('.sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
      });
    }
    if (overlay) {
      overlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
      });
    }
  });
</script>
@stack('scripts')
</body>
</html>
