<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #17386f; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        p.sub { color: #666; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f3f7fc; font-size: 11px; text-transform: uppercase; }
    </style>
</head>
<body>
    <h1>Data Karyawan - {{ $company->name }}</h1>
    <p class="sub">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Departemen</th>
                <th>Jabatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($karyawan as $k)
            <tr>
                <td>{{ $k->name }}</td>
                <td>{{ $k->email }}</td>
                <td>{{ $k->phone ?? '-' }}</td>
                <td>{{ $k->department ?? '-' }}</td>
                <td>{{ $k->position ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5">Belum ada karyawan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
