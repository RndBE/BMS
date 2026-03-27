<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kode OTP – BMS</title>
<style>
  body{font-family:'Segoe UI',Arial,sans-serif;background:#f4f4f4;margin:0;padding:32px 0;}
  .wrap{max-width:480px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.10);}
  .hdr{background:#B40404;padding:26px 32px;text-align:center;}
  .hdr h1{color:#fff;margin:0;font-size:20px;font-weight:700;}
  .body{padding:32px;color:#374151;}
  .body p{margin:0 0 14px;font-size:14.5px;line-height:1.6;}
  .otp-box{text-align:center;margin:24px 0;}
  .otp-code{display:inline-block;font-size:40px;font-weight:800;letter-spacing:14px;color:#B40404;
            background:#fff5f5;border:2px dashed #B40404;border-radius:10px;padding:14px 32px;}
  .note{font-size:12.5px;color:#9ca3af;margin-top:8px;}
  .footer{border-top:1px solid #f0f0f0;padding:18px 32px;text-align:center;font-size:12px;color:#9ca3af;}
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr"><h1>BMS – Reset Kata Sandi</h1></div>
  <div class="body">
    <p>Halo{{ $userName ? ', '.$userName : '' }},</p>
    <p>Kami menerima permintaan reset kata sandi untuk akun Anda. Gunakan kode di bawah ini:</p>
    <div class="otp-box">
      <div class="otp-code">{{ $otp }}</div>
      <div class="note">Kode berlaku selama <strong>15 menit</strong></div>
    </div>
    <p>Jika Anda tidak meminta reset kata sandi, abaikan email ini.</p>
    <p style="margin-bottom:0">Salam,<br><strong>Tim BMS</strong></p>
  </div>
  <div class="footer">© {{ date('Y') }} BMS. Jangan balas email ini.</div>
</div>
</body>
</html>
