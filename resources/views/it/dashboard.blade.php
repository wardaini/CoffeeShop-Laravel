@extends('layouts.app')
@section('title', 'Dashboard IT')

@push('styles')
<style>
    .wrap { max-width:1000px; margin:3rem auto; padding:0 5%; }
    .menu-bar { display:flex; gap:.8rem; margin-bottom:1.5rem; flex-wrap:wrap; }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.8rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.88rem; color:var(--text); vertical-align:middle; }
    .role-badge { padding:.2rem .7rem; border-radius:20px; font-size:.75rem; font-weight:600; }
    .role-admin { background:rgba(200,151,58,.2); color:var(--gold); }
    .role-karyawan { background:rgba(52,152,219,.15); color:#74b9ff; }
    .role-bos { background:rgba(155,89,182,.15); color:#c39bd3; }
    .role-it { background:rgba(39,174,96,.15); color:#6fcf97; }
    .role-pelanggan { background:rgba(138,122,106,.15); color:var(--muted); }
    .status-active { color:#6fcf97; font-size:.8rem; }
    .status-inactive { color:#e07070; font-size:.8rem; }
    .verif-pending { color:#e07070; font-weight:600; }
    .verif-verified { color:#6fcf97; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:.3rem;">Dashboard IT</h1>
    <p style="color:var(--muted); margin-bottom:1.5rem;">Manajemen User & Verifikasi Karyawan · Total: {{ $users->total() }} user</p>

    <div class="menu-bar">
        <a href="{{ route('it.attendance.index') }}" class="btn btn-outline btn-sm">📋 Kelola Absensi</a>
    </div>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status Akun</th>
                    <th>Verifikasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td style="font-size:.82rem; color:var(--muted);">{{ $user->email }}</td>
                    <td><span class="role-badge role-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                    <td>
                        <span class="{{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $user->is_active ? '● Aktif' : '● Nonaktif' }}
                        </span>
                    </td>
                    <td style="font-size:.82rem;">
                        @if($user->employeeProfile)
                            <span class="{{ $user->employeeProfile->verification_status === 'pending' ? 'verif-pending' : 'verif-verified' }}">
                                {{ ucfirst($user->employeeProfile->verification_status) }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; gap:.4rem; flex-wrap:wrap;">
                            @if($user->employeeProfile && $user->employeeProfile->verification_status === 'pending')
                            <form method="POST" action="{{ route('it.employees.verify', $user->employeeProfile) }}">
                                @csrf
                                <button type="submit" class="btn btn-gold btn-sm">✅ Verifikasi</button>
                            </form>
                            <form method="POST" action="{{ route('it.employees.reject', $user->employeeProfile) }}">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">❌ Tolak</button>
                            </form>
                            @endif

                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('it.users.toggle', $user) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm">
                                    {{ $user->is_active ? '🚫 Nonaktif' : '✅ Aktif' }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.5rem;">{{ $users->links('pagination::simple-bootstrap-4') }}</div>
</div>
@endsection