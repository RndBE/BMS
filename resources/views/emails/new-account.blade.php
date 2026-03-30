<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Akun BMS Baru</title>
<style>
  body{font-family:'Segoe UI',Arial,sans-serif;background:#f4f4f4;margin:0;padding:32px 0;}
  .wrap{max-width:480px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.10);}
  .hdr{background:#B40404;padding:26px 32px;text-align:center;}
  .hdr h1{color:#fff;margin:0;font-size:20px;font-weight:700;}
  .body{padding:32px;color:#374151;}
  .body p{margin:0 0 14px;font-size:14.5px;line-height:1.6;}
  .credential-box{background:#fff5f5;border:1px solid #fca5a5;border-radius:10px;padding:20px 24px;margin:24px 0;}
  .credential-row{display:flex;align-items:center;margin-bottom:12px;}
  .credential-row:last-child{margin-bottom:0;}
  .cred-label{font-size:12px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;width:130px;flex-shrink:0;}
  .cred-value{font-size:15px;font-weight:700;color:#B40404;word-break:break-all;}
  .note{font-size:12.5px;color:#9ca3af;margin-top:-10px;margin-bottom:18px;line-height:1.6;}
  .warning-box{background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:12px 16px;margin-bottom:18px;}
  .warning-box p{font-size:13px;color:#92400e;margin:0;}
  .footer{border-top:1px solid #f0f0f0;padding:18px 32px;text-align:center;font-size:12px;color:#9ca3af;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr"><h1>BMS – Akun Baru Dibuat</h1></div>
  <div class="body">
    <p>Halo, <strong>{{ $user->name }}</strong>!</p>
    <p>Selamat datang! Akun Anda untuk mengakses sistem <strong>Building Management System (BMS)</strong> telah berhasil dibuat oleh Administrator.</p>

    <p style="margin-bottom:8px;">Gunakan detail berikut untuk masuk ke sistem:</p>
    <div class="credential-box">
      <div class="credential-row">
        <span class="cred-label">Nama Pengguna</span>
        <span class="cred-value">{{ $user->name }}</span>
      </div>
      <div class="credential-row">
        <span class="cred-label">Password</span>
        <span class="cred-value">{{ $plainPassword }}</span>
      </div>
    </div>

    <div class="warning-box">
      <p>⚠️ <strong>Penting:</strong> Demi keamanan akun Anda, segera ganti kata sandi setelah pertama kali berhasil masuk ke sistem.</p>
    </div>

    <p>Jika Anda mengalami kesulitan saat masuk, silakan hubungi Administrator sistem.</p>
    <p style="margin-bottom:0">Salam,<br><strong>Tim BMS – Beacon Engineering</strong></p>
  </div>
  <div class="footer">© {{ date('Y') }} BMS. Jangan balas email ini.</div>
</div>
</body>
</html>
