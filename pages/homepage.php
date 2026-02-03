<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../auth/auth_check.php";

// Ambil data user dari session
$nama_user = $_SESSION['nama_lengkap'] ?? 'User';
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Homepage - Arsip Dokumen</title>
  <style>
    :root{
      /* ✅ Background sederhana dengan gradasi halus */
      --bg-start: #7384a2;   
      --bg-end: #c3ace9;     

      --card:#ffffff;
      --text:#0f172a;
      --muted:#64748b;
      --line:#e5e7eb;
      --shadow: 0 20px 50px rgba(0,0,0,.18);
      --radius: 22px;
    }

    *{ box-sizing:border-box; }

    /* ✅ NO SCROLL TOTAL */
    html, body{
      width:100%;
      height:100vh;
      overflow:hidden;
      margin:0;
    }

    body{
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Helvetica Neue", sans-serif;
      color:var(--text);

      /* ✅ gradasi simpel */
      background: linear-gradient(135deg, var(--bg-start), var(--bg-end));
      position: relative;
      overflow: hidden;

      display:flex;
      align-items:center;
      justify-content:center;
    }

    /* background bersih tanpa layer tambahan */

    /* wrapper fullscreen */
    .wrap{
      width:100%;
      height:100%;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 16px; /* aman buat mobile notch */
      position: relative;
      z-index: 2; /* ✅ di atas background */
    }

    .shell{
      width: min(1320px, 196%);
      height: min(820px, 92vh);
      background: rgba(255,255,255,.94);
      border: 1px solid rgba(255,255,255,.65);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 18px;
      display:flex;
      flex-direction:column;
      gap: 14px;
      overflow:hidden;
      backdrop-filter: blur(10px);
    }

    .header{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:12px;
    }

    .title{
      margin:0;
      font-size: 22px;
      letter-spacing: .2px;
      line-height:1.15;
    }

    .subtitle{
      margin:6px 0 0 0;
      color: var(--muted);
      line-height:1.45;
      font-size: 13px;
    }

    .header-right{
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .user-info{
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 2px;
      min-width: 140px;
    }

    .user-name{
      font-size: 14px;
      font-weight: 600;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 8px;
      white-space: nowrap;
    }

    .user-role{
      font-size: 11px;
      color: var(--muted);
      line-height: 1.1;
    }

    .role-badge{
      display: inline-flex;
      padding: 2px 8px;
      border-radius: 6px;
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .role-badge.admin{
      background: #ffeaa7;
      color: #d63031;
    }

    .role-badge.user{
      background: #dfe6e9;
      color: #2d3436;
    }

    .btn-logout{
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      border-radius: 999px;
      background: #ee5a6f;
      color: white;
      font-size: 12px;
      font-weight: 600;
      text-decoration: none;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 8px 20px rgba(238, 90, 111, 0.25);
      flex: 0 0 auto;
    }

    .btn-logout:hover{
      transform: translateY(-2px);
      box-shadow: 0 12px 28px rgba(238, 90, 111, 0.35);
    }

    /* area menu (grid) */
    .content{
      flex:1;
      display:flex;
      flex-direction:column;
      gap:12px;
      overflow:hidden;
    }

    .grid{
      flex:1;
      display:grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
      overflow:hidden;
      align-content:start;
    }

    .card{
      background: #f8fafc; /* sedikit beda dari shell */
      border: 2px solid #d6dbe3;
      border-radius: 18px;
      padding: 14px;
      transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
      text-decoration:none;
      color: inherit;
      position: relative;
      overflow:hidden;
      min-height: 112px;
      display:flex;
      flex-direction:column;
      justify-content:space-between;
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
    }

    .card:hover{
      transform: translateY(-2px);
      box-shadow: 0 16px 35px rgba(0,0,0,.12);
      border-color: rgba(91,42,134,.35);
    }

    .card::after{
      content:"";
      position:absolute;
      inset:auto -60px -60px auto;
      width:160px;
      height:160px;
      background: rgba(142,107,191,.14);
      transform: rotate(18deg);
    }

    .cardTop{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:10px;
    }

    .left{
      display:flex;
      align-items:flex-start;
      gap:10px;
      min-width: 0;
    }

    .icon{
      width:42px;
      height:42px;
      border-radius: 14px;
      display:grid;
      place-items:center;
      background: rgba(142,107,191,.14);
      border: 1px solid rgba(91,42,134,.16);
      font-size: 18px;
      flex: 0 0 auto;
    }

    .cardTitle{
      font-weight: 800;
      margin:0;
      font-size: 17px;
      letter-spacing:.2px;
      line-height:1.2;
    }

    .cardDesc{
      margin:6px 0 0 0;
      color: var(--muted);
      font-size: 14px;
      line-height: 1.4;
      max-width: 52ch;
    }

    .pill{
      font-size: 12px;
      color:#0f172a;
      background:#f1f5f9;
      border: 1px solid #e2e8f0;
      padding: 6px 10px;
      border-radius: 999px;
      white-space: nowrap;
      flex: 0 0 auto;
      align-self:flex-start;
    }

    .footer{
      border-top: 1px dashed rgba(100,116,139,.35);
      padding-top: 12px;
      color: var(--muted);
      font-size: 12px;
      display:flex;
      justify-content:space-between;
      gap:10px;
      flex-wrap:wrap;
    }

    .hint{
      display:flex;
      align-items:center;
      gap:8px;
    }

    .dot{
      width:8px;height:8px;border-radius:50%;
      background: #22c55e;
      box-shadow: 0 0 0 4px rgba(34,197,94,.12);
    }

    /* ✅ MOBILE */
    @media (max-width: 820px){
      .shell{
        height: calc(100vh - 28px);
        padding: 16px;
      }

      .header{
        flex-direction: column;
        align-items: stretch;
      }

      .header-right{
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
      }

      .user-info{
        align-items: flex-start;
      }

      .btn-logout{
        width: 100%;
        justify-content: center;
      }

      .grid{
        grid-template-columns: 1fr;
      }

      .card{
        min-height: 108px;
      }
    }

    /* ✅ SUPER SMALL MOBILE */
    @media (max-width: 380px){
      .title{ font-size: 20px; }
      .card{ padding: 12px; }
      .icon{ width:40px; height:40px; }
    }
  </style>
</head>

<body>
  <div class="wrap">
    <div class="shell">
      <div class="header">
        <div>
          <h1 class="title">Dashboard Arsip Dokumen</h1>
          <p class="subtitle">Selamat datang, <?= htmlspecialchars($nama_user) ?>!</p>
        </div>

        <div class="header-right">
          <div class="user-info">
            <div class="user-name">
              <span>👤</span>
              <span><?= htmlspecialchars($nama_user) ?></span>
              <span class="role-badge <?= htmlspecialchars($role) ?>"><?= strtoupper(htmlspecialchars($role)) ?></span>
            </div>
            <div class="user-role">@<?= htmlspecialchars($username) ?></div>
          </div>

          <a href="/SortirDokumen/auth/logout.php" class="btn-logout"> 🚪 Logout</a>
        </div>
      </div>

      <div class="content">
        <div class="grid">

          <a class="card" href="/SortirDokumen/pages/input_arsip.php" aria-label="Pemusnahan Dokumen">
            <div class="cardTop">
              <div class="left">
                <div class="icon">🧾</div>
                <div style="min-width:0;">
                  <p class="cardTitle">Pemusnahan Dokumen</p>
                  <p class="cardDesc">Hapus dokumen tidak terpakai.</p>
                </div>
              </div>
              <span class="pill">Menu</span>
            </div>
          </a>

          <a class="card" href="/SortirDokumen/pages/form.php" aria-label="Sortir Dokumen">
            <div class="cardTop">
              <div class="left">
                <div class="icon">🗂️</div>
                <div style="min-width:0;">
                  <p class="cardTitle">Sortir Dokumen</p>
                  <p class="cardDesc">Kelola kategori dokumen.</p>
                </div>
              </div>
              <span class="pill">Menu</span>
            </div>
          </a>

          <a class="card" href="tabel_arsip.php" aria-label="Tabel Pemusnahan Dokumen">
            <div class="cardTop">
              <div class="left">
                <div class="icon">📊</div>
                <div style="min-width:0;">
                  <p class="cardTitle">Tabel Pemusnahan Dokumen</p>
                  <p class="cardDesc">Lihat riwayat penghapusan.</p>
                </div>
              </div>
              <span class="pill">Tabel</span>
            </div>
          </a>

          <a class="card" href="/SortirDokumen/pages/arsip.php" aria-label="Rekapitulasi Arsip">
          
            <div class="cardTop">
              <div class="left">
                <div class="icon">🗄️</div>
                <div style="min-width:0;">
                  <p class="cardTitle">Rekapitulasi Arsip</p>
                  <p class="cardDesc">Cek ringkasan seluruh arsip.</p>
                </div>
              </div>
              <span class="pill">Rekap</span>
            </div>
          </a>

        </div>

        <a class="card" href="export_menu.php" aria-label="Export CSV">
          <div class="cardTop">
            <div class="left">
              <div class="icon">📥</div>
              <div style="min-width:0;">
                <p class="cardTitle">Export CSV</p>
                <p class="cardDesc">Unduh data ke format CSV/Excel.</p>
              </div>
            </div>
            <span class="pill">Export</span>
          </div>
        </a>

        <div class="footer">
          <div class="hint"><span class="dot"></span>Tips: Gunakan menu Sortir untuk mempercepat pencarian arsip.</div>
          <div>© <?= date('Y'); ?> · Arsip Dokumen</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
