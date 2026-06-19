@extends('layouts.app')
@section('title', 'Pilih Nama Karyawan')

@push('styles')
<style>
    .wrap { max-width: 480px; margin: 3rem auto; padding: 0 5%; }
    .wrap h1 { font-family:'Playfair Display',serif; font-size:1.5rem; color:var(--cream); text-align:center; margin-bottom:.5rem; }
    .wrap p.sub { text-align:center; color:var(--muted); font-size:.88rem; margin-bottom:2rem; }
    .search-box input {
        width:100%; padding:.85rem 1rem; margin-bottom:1.5rem;
        background:var(--card); border:1px solid rgba(200,151,58,.2);
        border-radius:8px; color:var(--text); font-size:1rem; outline:none;
    }
    .search-box input:focus { border-color:var(--gold); }
    .emp-list { display:flex; flex-direction:column; gap:.6rem; }
    .emp-item {
        background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px;
        padding:1rem 1.2rem; display:flex; justify-content:space-between; align-items:center;
        cursor:pointer; transition:.2s;
    }
    .emp-item:hover { border-color:var(--gold); background:rgba(200,151,58,.06); }
    .emp-name { font-family:'Playfair Display',serif; color:var(--cream); font-size:1rem; }
    .emp-pos { font-size:.78rem; color:var(--muted); }
    .empty { text-align:center; color:var(--muted); padding:2rem 0; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1>👋 Siapa Kamu?</h1>
    <p class="sub">Pilih namamu dari daftar untuk melanjutkan absensi</p>

    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Cari nama kamu..." onkeyup="filterEmployees()">
    </div>

    <div class="emp-list" id="empList">
        @forelse($employees as $profile)
        <a href="{{ route('attendance.face', $profile->employee_code) }}" class="emp-item" data-name="{{ strtolower($profile->user->name) }}">
            <div>
                <div class="emp-name">{{ $profile->user->name }}</div>
                <div class="emp-pos">{{ $profile->position }}</div>
            </div>
            <span style="color:var(--gold);">→</span>
        </a>
        @empty
        <div class="empty">Belum ada karyawan terverifikasi.</div>
        @endforelse
    </div>
</div>

<script>
function filterEmployees() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.emp-item').forEach(item => {
        item.style.display = item.dataset.name.includes(search) ? 'flex' : 'none';
    });
}
</script>
@endsection