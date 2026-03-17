<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 – Tidak Diizinkan | BMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            padding: 48px 40px;
            max-width: 420px;
            width: 100%;
            text-align: center;
        }
        .code {
            font-size: 72px;
            font-weight: 700;
            color: #b91c1c;
            line-height: 1;
            margin-bottom: 12px;
        }
        .title {
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .desc {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #b91c1c;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 22px;
            border-radius: 8px;
            text-decoration: none;
            transition: background .15s;
        }
        .btn:hover { background: #991b1b; }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">403</div>
        <div class="title">Akses Ditolak</div>
        <div class="desc">
            Kamu tidak memiliki izin untuk mengakses halaman ini.<br>
            Hubungi administrator jika kamu merasa ini keliru.
        </div>
        <a href="{{ url('/dashboard') }}" class="btn">
            ← Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
