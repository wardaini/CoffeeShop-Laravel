<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BrewNest Coffee')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0E0A07;
            --surface:   #1A1410;
            --card:      #231C17;
            --gold:      #C8973A;
            --gold-soft: #E8B860;
            --cream:     #F5ECD7;
            --text:      #EDE0CC;
            --muted:     #8A7A6A;
            --danger:    #C0392B;
            --success:   #27AE60;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 16px;
            line-height: 1.6;
        }

        h1, h2, h3 { font-family: 'Playfair Display', serif; }

        a { color: inherit; text-decoration: none; }

        /* ── Navbar ── */
        nav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(14,10,7,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(200,151,58,.18);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 5%;
            height: 68px;
        }

        .nav-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gold);
            letter-spacing: .03em;
        }

        .nav-links { display: flex; gap: 2rem; align-items: center; }

        .nav-links a {
            font-size: .9rem;
            font-weight: 500;
            color: var(--muted);
            letter-spacing: .06em;
            text-transform: uppercase;
            transition: color .2s;
        }

        .nav-links a:hover, .nav-links a.active { color: var(--gold); }

        .cart-badge {
            background: var(--gold);
            color: var(--bg);
            border-radius: 20px;
            padding: .25rem .75rem;
            font-size: .8rem;
            font-weight: 700;
        }

        /* ── Alerts ── */
        .alert {
            padding: .9rem 1.2rem;
            border-radius: 8px;
            margin: 1rem 5%;
            font-size: .9rem;
        }

        .alert-success { background: rgba(39,174,96,.15); border: 1px solid rgba(39,174,96,.4); color: #6fcf97; }
        .alert-error   { background: rgba(192,57,43,.15);  border: 1px solid rgba(192,57,43,.4);  color: #e07070; }

        /* ── Btn ── */
        .btn {
            display: inline-block;
            padding: .75rem 2rem;
            border-radius: 6px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: opacity .2s, transform .15s;
        }

        .btn:hover { opacity: .88; transform: translateY(-1px); }
        .btn-gold  { background: var(--gold); color: var(--bg); }
        .btn-outline { background: transparent; border: 1px solid var(--gold); color: var(--gold); }
        .btn-sm    { padding: .45rem 1.1rem; font-size: .85rem; }
        .btn-danger { background: var(--danger); color: #fff; }

        /* ── Footer ── */
        footer {
            background: var(--surface);
            border-top: 1px solid rgba(200,151,58,.12);
            text-align: center;
            padding: 2.5rem 5%;
            color: var(--muted);
            font-size: .85rem;
        }

        footer span { color: var(--gold); }

        /* ── Container ── */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 5%; }

        /* ── Hamburger ── */
        .ham { display: none; background: none; border: none; cursor: pointer; }
        .ham span { display: block; width: 24px; height: 2px; background: var(--gold); margin: 5px 0; border-radius: 2px; transition: .3s; }

        @media (max-width: 768px) {
            .ham { display: block; }
            .nav-links {
                display: none; flex-direction: column; gap: 1rem;
                position: absolute; top: 68px; left: 0; right: 0;
                background: var(--surface);
                padding: 1.5rem 5%;
                border-bottom: 1px solid rgba(200,151,58,.15);
            }
            .nav-links.open { display: flex; }
        }
    </style>
    @stack('styles')
</head>
<body>

<nav>
    <a href="{{ route('home') }}" class="nav-brand">☕ BrewNest</a>

    <button class="ham" id="ham" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>

    <div class="nav-links" id="navLinks">
        <a href="{{ route('home') }}"        class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
        <a href="{{ route('menu.index') }}"  class="{{ request()->routeIs('menu.*') ? 'active' : '' }}">Menu</a>

        @auth
            @if(auth()->user()->role === 'pelanggan')
                <a href="{{ route('cart.index') }}" class="cart-badge">🛒 {{ count(session('cart', [])) }}</a>
                <form method="POST" action="{{ route('customer.logout') }}">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:var(--muted);cursor:pointer;font-family:inherit;font-size:.9rem;text-transform:uppercase;letter-spacing:.06em;">Keluar</button>
                </form>
            @elseif(auth()->user()->role === 'karyawan')
                <a href="{{ route('employee.dashboard') }}">Dashboard</a>
                <form method="POST" action="{{ route('employee.logout') }}">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:var(--muted);cursor:pointer;font-family:inherit;font-size:.9rem;text-transform:uppercase;letter-spacing:.06em;">Keluar</button>
                </form>
            @else
                <a href="{{ route(auth()->user()->role . '.dashboard') }}">Dashboard</a>
                <form method="POST" action="{{ route('staff.logout') }}">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:var(--muted);cursor:pointer;font-family:inherit;font-size:.9rem;text-transform:uppercase;letter-spacing:.06em;">Keluar</button>
                </form>
            @endif
        @else
            <a href="{{ route('order.track') }}" class="{{ request()->routeIs('order.track') ? 'active' : '' }}">Lacak Pesanan</a>
            <a href="{{ route('customer.login') }}" class="cart-badge">Masuk</a>
        @endauth
    </div>
</nav>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

@yield('content')

<footer>
    <p>&copy; {{ date('Y') }} <span>BrewNest Coffee</span> — Lhokseumawe, Aceh</p>
</footer>

<script>
    document.getElementById('ham').addEventListener('click', () => {
        document.getElementById('navLinks').classList.toggle('open');
    });
</script>
@stack('scripts')
</body>
</html>