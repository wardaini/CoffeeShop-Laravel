@extends('layouts.app')
@section('title', 'Verifikasi Karyawan')

@push('styles')
<style>
    .wrap { max-width:1000px; margin:3rem auto; padding:0 5%; }
    .tab-nav { display:flex; gap:.5rem; margin-bottom:1.5rem; border-bottom:1px solid rgba(200,151,58,.15); padding-bottom:.5rem; }
    .tab-btn { padding:.5rem 1.2rem; border-radius:6px 6px 0 0; font-size:.85rem; cursor:pointer; border:none; background:transparent; color:var(--muted); transition:.2s; }
    .tab-btn.active { background:var(--gold); color:var(--bg); }
    .emp-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1.2rem; margin-bottom:.8rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; }
    .emp-name { font-family:'Playfair Display',serif; color:var(--cream); font-size:1.05rem; }
    .emp-info { font-size:.82rem; color:var(--muted); margin-top:.2rem; }
    .emp-actions { display:flex; gap:.5rem; flex-wrap:wrap; }
    .empty { text-align:center; padding:3rem 0; color:var(--muted); }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:1.5rem;">Verifikasi Karyawan</h1>

    <div class="tab-nav">
        <button class="tab-btn active" onclick="showTab('pending')">⏳ Pending ({{ $pending->count() }})</button>
        <button class="tab-btn" onclick="showTab('verified')">✅ Terverifikasi ({{ $verified->count() }})</button>
        <button class="tab-btn" onclick="showTab('rejected')">❌ Ditolak ({{ $rejected->count() }})</button>
    </div>

    <div id="tab-pending">
        @forelse($pending as $profile)
        <div class="emp-card">
            <div>
                <div class="emp-name">{{ $profile->user->name }}</div>
                <div class="emp-info">{{ $profile->position }} · {{ $profile->user->email }} · {{ $profile->user->phone }}</div>
                <div class="emp-info">KTP: {{ $profile->ktp_number }}</div>
            </div>
            <div class="emp-actions">
                <a href="{{ route('admin.employees.show', $profile) }}" class="btn btn-outline btn-sm">👁 Lihat</a>
                <form method="POST" action="{{ route('admin.employees.verify', $profile) }}">
                    @csrf
                    <button type="submit" class="btn btn-gold btn-sm">✅ Verifikasi</button>
                </form>
                <form method="POST" action="{{ route('admin.employees.reject', $profile) }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">❌ Tolak</button>
                </form>
            </div>
        </div>
        @empty
        <div class="empty">Tidak ada karyawan pending.</div>
        @endforelse
    </div>

    <div id="tab-verified" style="display:none;">
        @forelse($verified as $profile)
        <div class="emp-card">
            <div>
                <div class="emp-name">{{ $profile->user->name }}</div>
                <div class="emp-info">{{ $profile->position }} · {{ $profile->employee_code }}</div>
                <div class="emp-info">Gaji Pokok: Rp {{ number_format($profile->base_salary, 0, ',', '.') }}</div>
            </div>
            <div class="emp-actions">
                <form method="POST" action="{{ route('admin.employees.salary', $profile) }}" style="display:flex; gap:.5rem; align-items:center;">
                    @csrf
                    <input type="number" name="base_salary" value="{{ $profile->base_salary }}" min="0" step="50000"
                           style="width:140px; padding:.4rem .6rem; background:var(--surface); border:1px solid rgba(200,151,58,.2); border-radius:6px; color:var(--text); font-size:.85rem;">
                    <button type="submit" class="btn btn-outline btn-sm">💾 Update Gaji</button>
                </form>
            </div>
        </div>
        @empty
        <div class="empty">Belum ada karyawan terverifikasi.</div>
        @endforelse
    </div>

    <div id="tab-rejected" style="display:none;">
        @forelse($rejected as $profile)
        <div class="emp-card">
            <div>
                <div class="emp-name">{{ $profile->user->name }}</div>
                <div class="emp-info">{{ $profile->position }} · {{ $profile->user->email }}</div>
            </div>
            <div class="emp-actions">
                <form method="POST" action="{{ route('admin.employees.verify', $profile) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm">↩ Verifikasi Ulang</button>
                </form>
            </div>
        </div>
        @empty
        <div class="empty">Tidak ada karyawan ditolak.</div>
        @endforelse
    </div>
</div>

<script>
function showTab(tab) {
    ['pending','verified','rejected'].forEach(t => {
        document.getElementById('tab-' + t).style.display = t === tab ? 'block' : 'none';
    });
    document.querySelectorAll('.tab-btn').forEach((btn, i) => {
        btn.classList.toggle('active', ['pending','verified','rejected'][i] === tab);
    });
}
</script>
@endsection