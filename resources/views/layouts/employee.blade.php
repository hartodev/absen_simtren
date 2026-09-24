<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - {{ Auth::user()->company->name ?? 'Absensi' }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        :root {
            --c-primary: #2563eb;
            --c-bg: #f3f7fc;
            --c-border: #e6ebf3;
            --c-muted: #8a94a6;
            --c-text: #17386f;
            --sidebar-w: 236px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--c-bg);
            color: var(--c-text);
            font-family: 'DM Sans', sans-serif;
        }

        a { text-decoration: none; color: inherit; }

        /* ---------- shell: sidebar + main ---------- */
        .app-shell { display: flex; min-height: 100vh; }

        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: #102b69;
            color: #fff;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }

        .sidebar-brand .logo {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
        }

        .sidebar-brand .name {
            font-weight: 700;
            font-size: 13px;
            line-height: 1.2;
        }

        .sidebar-brand .role {
            font-size: 11px;
            color: #9db2df;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 12px 10px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #c6d3ee;
            margin-bottom: 2px;
        }

        .sidebar-link:hover { background: rgba(255, 255, 255, .06); color: #fff; }

        .sidebar-link.is-active { background: #2563eb; color: #fff; }

        .sidebar-link.is-disabled {
            opacity: .45;
            cursor: default;
            pointer-events: none;
        }

        .sidebar-foot { padding: 14px 10px; border-top: 1px solid rgba(255, 255, 255, .1); }

        .sidebar-foot button {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, .18);
            background: transparent;
            color: #e5ecfa;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
        }

        .sidebar-foot button:hover { background: rgba(255, 255, 255, .08); }

        /* ---------- main column ---------- */
        .main-col { flex: 1; min-width: 0; }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 24px;
            background: #fff;
            border-bottom: 1px solid var(--c-border);
        }

        .breadcrumb { font-size: 13px; font-weight: 600; color: var(--c-muted); display: flex; gap: 6px; align-items: center; }
        .breadcrumb a:hover { color: var(--c-primary); }
        .breadcrumb .current { color: var(--c-text); }
        .breadcrumb span.sep { color: #c5cede; }

        .topbar-user { display: flex; align-items: center; gap: 10px; font-size: 13px; }
        .topbar-user .avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: var(--c-primary); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px;
        }

        .content-wrap { padding: 22px 24px 60px; max-width: 960px; }

        /* ---------- shared content classes used across employee pages ---------- */
        .page-title { font-size: 20px; font-weight: 800; }
        .page-sub { font-size: 13px; color: var(--c-muted); margin-bottom: 18px; }

        .card { background: #fff; border: 1px solid var(--c-border); border-radius: 14px; overflow: hidden; }
        .card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 16px; border-bottom: 1px solid var(--c-border);
        }
        .card-title { font-size: 14px; font-weight: 700; }
        .card-body { padding: 16px; }

        .metrics { display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 14px; }
        .metric-label { font-size: 12px; color: var(--c-muted); margin-bottom: 4px; }
        .metric-val { font-size: 17px; font-weight: 800; }
        .metric-val.warning { color: #b7791f; }
        .metric-val.info { color: var(--c-primary); }

        .badge { display: inline-block; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .badge-success { background: #e0f4ea; color: #198754; }
        .badge-warning { background: #fff4dd; color: #b7791f; }
        .badge-info { background: #dceaff; color: #2456b5; }
        .badge-gray { background: #eef1f6; color: var(--c-muted); }

        .alert { padding: 10px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; }
        .alert-success { background: #e0f4ea; color: #198754; }
        .alert-danger { background: #fff1f1; color: #b34040; }

        .btn {
            display: inline-block; padding: 10px 16px; border-radius: 10px;
            font-size: 13px; font-weight: 700; border: none; cursor: pointer; font-family: inherit;
        }
        .btn-outline { background: #fff; border: 1px solid var(--c-border); color: var(--c-text); }
        .btn-outline:hover { border-color: var(--c-primary); color: var(--c-primary); }
        .btn-primary { background: var(--c-primary); color: #fff; }

        .menu-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 16px; border-bottom: 1px solid var(--c-border);
            font-size: 14px; font-weight: 600;
        }
        .menu-row:last-child { border-bottom: none; }
        .menu-row:hover { background: #f8fafc; }
        .menu-row-disabled { color: var(--c-muted); font-weight: 500; }

        @media (max-width: 860px) {
            .sidebar { position: fixed; left: -100%; transition: left .2s ease; z-index: 50; }
            .sidebar.is-open { left: 0; }
            .content-wrap { padding: 18px 16px 48px; }
        }
    </style>
    @stack('styles')
</head>

<body>
    @php
        $__navRoutes = [
            'company.member.dashboard' => ['label' => 'Dashboard', 'icon' => '🏠'],
            'company.member.attendance.index' => ['label' => 'Absensi', 'icon' => '🕒'],
            'company.member.permissions.index' => ['label' => 'Izin', 'icon' => '📝'],
            'company.member.leaves.index' => ['label' => 'Cuti', 'icon' => '🌴'],
            'company.member.overtimes.index' => ['label' => 'Lembur', 'icon' => '⏱'],
            'company.member.shifts.schedule' => ['label' => 'Jadwal Shift', 'icon' => '📅'],
            'company.member.payrolls.index' => ['label' => 'Slip Gaji', 'icon' => '💰'],
            'company.member.performance-scores.index' => ['label' => 'Performa', 'icon' => '📈'],
            'company.member.daily-reports.index' => ['label' => 'Laporan Harian', 'icon' => '📄'],
            'company.member.monthly-reports.index' => ['label' => 'Laporan Bulanan', 'icon' => '📊'],
            'company.member.notes.index' => ['label' => 'Catatan dari HR', 'icon' => '💬'],
            'company.member.holidays.index' => ['label' => 'Hari Libur', 'icon' => '🏖'],
        ];
    @endphp

    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <span class="logo">{{ strtoupper(substr(Auth::user()->company->name ?? 'A', 0, 1)) }}</span>
                <div>
                    <div class="name">{{ Auth::user()->company->name ?? 'Perusahaan' }}</div>
                    <div class="role">Portal Karyawan</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                @foreach($__navRoutes as $routeName => $item)
                @if(\Illuminate\Support\Facades\Route::has($routeName))
                <a href="{{ route($routeName) }}" class="sidebar-link {{ request()->routeIs($routeName) ? 'is-active' : '' }}">
                    <span>{{ $item['icon'] }}</span> <span>{{ $item['label'] }}</span>
                </a>
                @endif
                @endforeach
                <span class="sidebar-link is-disabled">
                    <span>🏦</span> <span>Pinjaman <small>(segera)</small></span>
                </span>
            </nav>

            <div class="sidebar-foot">
                <form method="POST" action="{{ route('tenant.logout') }}">
                    @csrf
                    <button type="submit">⏻ Keluar</button>
                </form>
            </div>
        </aside>

        <div class="main-col">
            <header class="topbar">
                <div class="breadcrumb">
                    @hasSection('breadcrumb')
                        @yield('breadcrumb')
                    @else
                        <span class="current">@yield('title', 'Dashboard')</span>
                    @endif
                </div>
                <div class="topbar-user">
                    <span class="avatar">{{ strtoupper(substr(Auth::user()->name ?? '-', 0, 1)) }}</span>
                    <span>{{ Auth::user()->name ?? '-' }}</span>
                </div>
            </header>

            <main class="content-wrap">
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>
