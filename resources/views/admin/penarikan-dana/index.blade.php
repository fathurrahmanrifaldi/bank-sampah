@extends('layouts.admin')
@section('title', 'Penarikan Dana')
@section('page-title', 'Penarikan Dana')

@section('content')

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
  <div class="col-sm-4">
    <div class="stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="stat-icon" style="background:#fef9c3">
          <i class="bi bi-hourglass-split" style="color:#ca8a04"></i>
        </div>
        <div>
          <div class="stat-value">{{ $totalMenunggu }}</div>
          <div class="stat-label">Menunggu</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="stat-icon" style="background:#dcfce7">
          <i class="bi bi-check-circle" style="color:#16a34a"></i>
        </div>
        <div>
          <div class="stat-value">{{ $totalDisetujui }}</div>
          <div class="stat-label">Disetujui</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="stat-card">
      <div class="d-flex align-items-center gap-3">
        <div class="stat-icon" style="background:#fee2e2">
          <i class="bi bi-x-circle" style="color:#dc2626"></i>
        </div>
        <div>
          <div class="stat-value">{{ $totalDitolak }}</div>
          <div class="stat-label">Ditolak</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Filter --}}
<div class="table-card">
  <div class="card-header flex-wrap gap-2">
    <h6 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Daftar Pengajuan Penarikan</h6>
    <div class="d-flex gap-2 flex-wrap">
      @foreach([''=>'Semua','menunggu'=>'Menunggu','disetujui'=>'Disetujui','ditolak'=>'Ditolak'] as $val => $label)
        <a href="{{ route('admin.penarikan-dana.index', $val ? ['status'=>$val] : []) }}"
           class="btn btn-sm {{ request('status') == $val ? 'btn-dark' : 'btn-outline-secondary' }}"
           style="font-size:12px;border-radius:20px">
          {{ $label }}
        </a>
      @endforeach
    </div>
  </div>

  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Nasabah</th>
          <th>Nominal</th>
          <th>Jenis</th>
          <th>Tgl Diminta</th>
          <th>Tgl Cair</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($penarikan as $p)
        <tr>
          <td style="color:#94a3b8;font-size:12px">{{ $p->id }}</td>
          <td>
            <div style="font-size:13px;font-weight:600">{{ $p->nasabah->user->name }}</div>
            <div style="font-size:11px;color:#94a3b8">NIK: {{ $p->nasabah->nik }}</div>
          </td>
          <td style="font-weight:700;color:#16a34a;font-size:13px">
            Rp {{ number_format($p->jumlah, 0, ',', '.') }}
          </td>
          <td>
            @if($p->jenis === 'segera')
              <span class="badge" style="background:#dbeafe;color:#1d4ed8;font-size:11px">
                <i class="bi bi-lightning-charge me-1"></i>Sekarang
              </span>
            @else
              <span class="badge" style="background:#ede9fe;color:#6d28d9;font-size:11px">
                <i class="bi bi-calendar-event me-1"></i>Terjadwal
              </span>
            @endif
          </td>
          <td style="font-size:13px">{{ $p->tanggal_diminta->format('d M Y') }}</td>
          <td style="font-size:13px">
            {{ $p->tanggal_pencairan ? $p->tanggal_pencairan->format('d M Y') : '-' }}
          </td>
          <td>
            @php
              $colors = ['menunggu'=>['bg'=>'#fef9c3','text'=>'#92400e'],'disetujui'=>['bg'=>'#dcfce7','text'=>'#166534'],'ditolak'=>['bg'=>'#fee2e2','text'=>'#991b1b']];
              $c = $colors[$p->status] ?? ['bg'=>'#f1f5f9','text'=>'#374151'];
            @endphp
            <span class="badge" style="background:{{ $c['bg'] }};color:{{ $c['text'] }};font-size:11px">
              {{ $p->statusLabel() }}
            </span>
          </td>
          <td>
            @if($p->status === 'menunggu')
              {{-- Approve --}}
              <form method="POST" action="{{ route('admin.penarikan-dana.approve', $p->id) }}" class="d-inline form-approve">
                @csrf
                <button type="submit" class="btn btn-sm"
                        style="background:#dcfce7;color:#166534;border:none;font-size:11px"
                        data-nama="{{ $p->nasabah->user->name }}"
                        data-jumlah="Rp {{ number_format($p->jumlah, 0, ',', '.') }}">
                  <i class="bi bi-check-lg"></i> Setujui
                </button>
              </form>
              {{-- Tolak (modal) --}}
              <button class="btn btn-sm ms-1"
                      style="background:#fee2e2;color:#991b1b;border:none;font-size:11px"
                      data-bs-toggle="modal"
                      data-bs-target="#modalTolak{{ $p->id }}">
                <i class="bi bi-x-lg"></i> Tolak
              </button>

              {{-- Modal Tolak --}}
              <div class="modal fade" id="modalTolak{{ $p->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content" style="border-radius:16px;border:none">
                    <div class="modal-header" style="border-bottom:1px solid #f1f5f9">
                      <h6 class="modal-title"><i class="bi bi-x-circle text-danger me-2"></i>Tolak Pengajuan</h6>
                      <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.penarikan-dana.reject', $p->id) }}">
                      @csrf
                      <div class="modal-body">
                        <p style="font-size:13px;color:#475569">
                          Tolak pengajuan penarikan <strong>Rp {{ number_format($p->jumlah, 0, ',', '.') }}</strong>
                          dari <strong>{{ $p->nasabah->user->name }}</strong>?
                        </p>
                        <div class="mb-2">
                          <label class="form-label" style="font-size:12px;color:#64748b">Catatan Penolakan (opsional)</label>
                          <textarea name="catatan_admin" class="form-control" rows="3"
                                    placeholder="Alasan penolakan..." style="font-size:13px"></textarea>
                        </div>
                      </div>
                      <div class="modal-footer" style="border-top:1px solid #f1f5f9">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-danger">Tolak Pengajuan</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            @else
              <span style="font-size:11px;color:#94a3b8">
                @if($p->prosesOleh)
                  Oleh: {{ $p->prosesOleh->name }}
                @else
                  Otomatis
                @endif
              </span>
              @if($p->catatan_admin)
                <div style="font-size:11px;color:#dc2626;margin-top:2px">
                  <i class="bi bi-info-circle me-1"></i>{{ $p->catatan_admin }}
                </div>
              @endif
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center text-muted py-5">
            <i class="bi bi-inbox" style="font-size:28px;display:block;margin-bottom:8px;color:#cbd5e1"></i>
            Belum ada pengajuan penarikan dana.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($penarikan->hasPages())
    <div class="d-flex justify-content-end p-3">{{ $penarikan->links() }}</div>
  @endif
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.form-approve').forEach(function(form) {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn    = form.querySelector('button[type="submit"]');
    const nama   = btn.dataset.nama;
    const jumlah = btn.dataset.jumlah;
    if (confirm('Setujui penarikan ' + jumlah + ' dari ' + nama + '?')) {
      form.submit();
    }
  });
});
</script>
@endpush
