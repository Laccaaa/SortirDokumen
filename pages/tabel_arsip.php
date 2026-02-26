<?php
require_once __DIR__ . "/../actions/proses_tabel.php";
require_once __DIR__ . "/../auth/auth_check.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Daftar Arsip</title>

<style>
:root{
  --bg-start: #f3f5f9;
  --bg-end: #e2e7f1;

  --text:#1f2a44;
  --muted:#667085;
  --line:#e6e8ef;
}

*{ box-sizing:border-box; }

html, body{
  width:100%;
  height:100%;
  margin:0;
}

/* ✅ body NO scroll */
body{
  height:100vh;
  overflow:hidden;
  background: linear-gradient(135deg, var(--bg-start), var(--bg-end));
  position:relative;
  font-family: Inter, Arial, sans-serif;

  display:flex;
  justify-content:center;
  align-items:stretch;
  padding:14px;
}

/* background bersih tanpa layer tambahan */

.wrap{
  width: 100%;
  max-width: none;
  height: 100%;
  position:relative;
  z-index:2;
  display:flex;
}

/* layout */
.layout{
  width:100%;
  height:100%;
  display:flex;
  gap:14px;
}

/* sidebar */
.sidebar{
  width: 340px;
  height: 100%;
  background: #1f2430;
  border: 1px solid #2b3242;
  border-radius: 20px;
  box-shadow: 0 16px 40px rgba(0,0,0,.18);
  padding: 14px;
  display:flex;
  flex-direction:column;
  gap:12px;
  overflow:auto;
}

.side-title{
  font-weight: 900;
  font-size: 14px;
  letter-spacing:.3px;
  color:#e2e8f0;
  text-transform: uppercase;
}

.side-list{
  display:flex;
  flex-direction:column;
  gap:10px;
  margin:0;
  padding:0;
  list-style:none;
}

.side-link{
  display:flex;
  gap:10px;
  align-items:center;
  padding:10px 12px;
  border-radius: 14px;
  text-decoration:none;
  color:#e2e8f0;
  background: #232a38;
  border:1px solid #2f3747;
  transition: all .15s ease;
}
.side-link:hover{
  transform: translateY(-1px);
  border-color:#5a63ff;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.2);
}
.side-link.active{
  background:#2a3350;
  border-color:#5a63ff;
  box-shadow: inset 0 0 0 1px rgba(90, 99, 255, .25);
}
.side-accordion{ display:flex; flex-direction:column; gap:8px; }
.side-accordion summary{ list-style:none; }
.side-accordion summary::-webkit-details-marker{ display:none; }
.side-sub{ display:flex; flex-direction:column; gap:8px; padding-left:12px; }
.side-sublink{
  display:flex;
  align-items:center;
  gap:8px;
  padding:8px 10px;
  border-radius: 12px;
  text-decoration:none;
  color:#e2e8f0;
  background: #232a38;
  border:1px solid #2f3747;
  font-size: 12px;
  transition: all .15s ease;
}
.side-sublink:hover{
  transform: translateY(-1px);
  border-color:#5a63ff;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.2);
}
.side-sublink.active{
  background:#2a3350;
  border-color:#5a63ff;
  box-shadow: inset 0 0 0 1px rgba(90, 99, 255, .25);
}
.side-icon{
  width:36px;
  height:36px;
  border-radius: 12px;
  display:grid;
  place-items:center;
  background: rgba(90, 99, 255, .18);
  border:1px solid rgba(90, 99, 255, .25);
  font-size: 18px;
  flex: 0 0 auto;
}
.side-text{
  display:flex;
  flex-direction:column;
  gap:2px;
  min-width:0;
}
.side-text strong{
  font-size: 14px;
  line-height:1.2;
}
.side-text span{
  font-size: 12px;
  color: #94a3b8;
  line-height:1.25;
}

/* ✅ Shell utama */
.card{
  width:100%;
  height:100%;
  max-width:none;
  flex: 1 1 auto;
  margin-left: 0;
  background:#fff;
  border-radius:24px;
  box-shadow:
    0 10px 30px rgba(15,23,42,.14),
    0 30px 60px rgba(15,23,42,.12);

  overflow:hidden;
  display:flex;
  flex-direction:column;
}

/* ✅ HEAD (fixed) */
.shell-head{
  padding:12px 12px 10px;
  border-bottom:1px solid #eef2f7;
  flex:0 0 auto;
  background:#fff;
}

/* ✅ TOOLS (fixed) */
.shell-tools{
  padding:10px 12px 10px;
  border-bottom:1px solid #eef2f7;
  flex:0 0 auto;
  background:#fff;
}

/* ✅ BODY (tidak scroll) */
.shell-body{
  padding:10px 12px 12px;
  flex:1 1 auto;
  min-height:0;
  overflow:hidden;
}

/* header */
.header{
  display:flex;
  justify-content:space-between;
  gap:12px;
  flex-wrap:wrap;
  align-items:flex-start;
}

.title h1{
  margin:0 0 6px;
  font-size:20px;
}
.sub{
  font-size:12px;
  color:var(--muted);
}

/* tombol */
.btns{
  display:flex;
  gap:10px;
  align-items:center;
}

a.btn{
  padding:10px 14px;
  border-radius:12px;
  text-decoration:none;
  font-weight:800;
  font-size:13px;
  background:#1f2a44;
  color:#fff;
  display:inline-flex;
  gap:8px;
  align-items:center;
  white-space:nowrap;
}
a.btn.primary{
  background:#334155;
  color:#ffffff;
  border:1px solid #334155;
  padding:10px 14px;
  border-radius:12px;
  min-height:36px;
}
a.btn.primary:hover{
  background:#475569;
  color:#ffffff;
}
a.btn.secondary{
  background:#eef2ff;
  color:#1f2a44;
  border:1px solid #d7ddff;
}

/* alerts */
.alert-err{
  margin-bottom:12px;
  padding:10px 12px;
  border-radius:12px;
  background:#fff5f5;
  border:1px solid #ffd0d0;
  color:#7a1f1f;
  font-size:13px;
  font-weight:700;
}
.alert-ok{
  margin-bottom:12px;
  padding:10px 12px;
  border-radius:12px;
  background:#f0fdf4;
  border:1px solid #bbf7d0;
  color:#166534;
  font-size:13px;
  font-weight:800;
  display:flex;
  align-items:center;
  gap:8px;
}
.alert-ok .icon-check{
  width:20px;
  height:20px;
  display:inline-block;
}

/* search */
.search-row{
  display:flex;
  justify-content:space-between;
  gap:12px;
  flex-wrap:wrap;
}
.search{
  flex:1;
  min-width: 280px;
  display:flex;
  gap:10px;
  align-items:center;
}
.search input{
  width:100%;
  padding:11px 14px;
  border-radius:12px;
  border:1px solid #e5e7eb;
  outline:none;
  font-size:14px;
  background:#fff;
}
.search input:focus{
  border-color:#b7c3ff;
  box-shadow:0 0 0 4px rgba(99,102,241,.12);
}
.search button{
  padding:11px 14px;
  border-radius:12px;
  border:1px solid #d7ddff;
  background:#eef2ff;
  color:#1f2a44;
  font-weight:900;
  cursor:pointer;
}
a.clear{
  font-size:13px;
  color:#475569;
  text-decoration:none;
  padding:10px 12px;
  border-radius:12px;
  border:1px solid #e5e7eb;
  background:#fff;
}

/* ✅ table-wrap yang scroll */
.table-wrap{
  height: 100%;
  border-radius:16px;
  overflow:auto;                 /* ✅ scroll vertikal di sini */
  overflow-x:auto;               /* ✅ allow scroll kanan-kiri */
  -webkit-overflow-scrolling: touch;
  border:1px solid #eef2f7;
  box-shadow:0 8px 24px rgba(15,23,42,.06);
}

/* table */
table{
  width:max-content;
  min-width: 1800px;
  border-collapse:collapse;
  font-size:11px;
  table-layout: auto;
}

thead th{
  background:#f8fafc;
  padding:10px 8px;
  border-bottom:1px solid #e5e7eb;
  font-size:10px;
  font-weight:900;
  color:#475569;
  white-space:nowrap;

  position: sticky;
  top: 0;
  z-index: 5;
}

thead th:last-child{
  position: sticky;
  right: 0;
  z-index: 7;
  box-shadow: none;
  background: transparent;
  border-bottom-color: transparent;
  width: 170px;
  min-width: 170px;
  max-width: 170px;
  padding: 0;
  overflow: visible;
}

tbody td{
  padding:10px 8px;
  border-bottom:1px solid #eef2f7;
  color:#1f2937;
  vertical-align:top;
  background:#fff;
  word-break: break-word;
}

tbody td:last-child{
  position: sticky;
  right: 0;
  z-index: 6;
  min-width: 170px;
  width: 170px;
  max-width: 170px;
  box-shadow: none;
  border-bottom-color: transparent;
  background: transparent !important;
  padding: 0;
  text-align: right;
  vertical-align: middle;
  overflow: visible;
}

tbody tr:nth-child(even) td{ background:#fafbff; }
tbody tr:hover td{ background:#f1f5ff; }
tbody tr:nth-child(even) td:last-child{ background:transparent !important; }
tbody tr:hover td:last-child{ background:transparent !important; }

td.muted{
  text-align:center;
  color:var(--muted);
  background:#fff;
}

/* aksi */
.actions{
  display:flex;
  gap:6px;
  flex-direction:row;
  align-items:center;
  justify-content:flex-end;
  flex-wrap:nowrap;
  width: max-content;
  margin-left: auto;
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
}

a.btn-edit{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:5px 10px;
  border-radius:999px;
  text-decoration:none;
  font-weight:900;
  font-size:10px;
  line-height:1;
  background:#6366F1;
  color:#ffffff;
  border:1px solid #4F46E5;
  width: auto;
  min-width: 48px;
  white-space: nowrap;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(79,70,229,.25);
}
a.btn-edit:hover{
  background:#4F46E5;
}

.btn-del{
  border:1px solid #DC2626;
  background:#EF4444;
  color:#ffffff;
  font-weight:900;
  font-size:10px;
  line-height:1;
  padding:5px 10px;
  border-radius:999px;
  cursor:pointer;
  width: auto;
  min-width: 52px;
  white-space: nowrap;
  display:inline-flex;
  align-items:center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(220,38,38,.22);
}
.btn-del:hover{
  background:#DC2626;
}
.btn-del:active{ transform: translateY(1px); }

.modal{
  position:fixed;
  inset:0;
  background: rgba(15, 23, 42, .45);
  display:none;
  align-items:center;
  justify-content:center;
  z-index: 999;
  padding: 20px;
}
.modal.show{ display:flex; }
.confirm-card{
  width: min(520px, 92vw);
  background:#fff;
  border-radius: 18px;
  box-shadow: 0 24px 80px rgba(0,0,0,.25);
  padding: 26px 24px 22px;
  text-align:center;
}
.confirm-icon{
  width:64px;
  height:64px;
  margin: 0 auto 12px;
  border-radius: 16px;
  display:grid;
  place-items:center;
  background: #fff7ed;
  color:#ea580c;
  font-size: 28px;
}
.confirm-title{
  font-size: 18px;
  font-weight: 800;
  color:#0f172a;
  margin-bottom: 6px;
}
.confirm-text{
  font-size: 13px;
  color:#64748b;
  margin-bottom: 18px;
}
.confirm-actions{
  display:flex;
  gap:10px;
  justify-content:center;
}
.btn-confirm{
  border:none;
  border-radius: 12px;
  padding: 10px 16px;
  font-weight: 800;
  cursor:pointer;
}
.btn-confirm.cancel{
  background:#eef2ff;
  color:#1f2a44;
  border:1px solid #d7ddff;
}
.btn-confirm.ok{
  background:#0f172a;
  color:#fff;
}

/* ✅ Mobile */
@media (max-width:768px){
  body{ padding:10px; }
  .layout{ flex-direction:column; }
  .sidebar{
    width:100%;
    height:auto;
  }
  .card{ border-radius:18px; max-width:100%; }
  .shell-head{ padding:14px 14px 10px; }
  .shell-tools{ padding:12px 14px 12px; }
  .shell-body{ padding:12px 14px 14px; }

  .header{
    flex-direction:column;
    align-items:flex-start;
    gap:10px;
  }

  .btns{
    width:100%;
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:10px;
  }
  a.btn{ width:100%; justify-content:center; border-radius:14px; }

  .search-row{ width:100%; flex-direction:column; gap:10px; }
  .search{
    width:100%;
    min-width:unset;
    display:grid;
    grid-template-columns: 1fr auto;
    gap:10px;
  }
  a.clear{ width:100%; text-align:center; border-radius:14px; }

  table{ min-width: 0; width:100%; }
  thead{ display:none; }
  table, tbody, tr, td{ display:block; width:100%; }

  tr{
    border-bottom:1px solid #eef2f7;
    padding:12px 14px;
    background:#fff;
    position: relative;
  }
  tbody tr:nth-child(even){ background:#fbfcff; }

  td{
    border:none;
    padding:10px 0;
    overflow-wrap:anywhere;
    word-break:break-word;
    background: transparent !important;
    position: static !important;
    right: auto !important;
    box-shadow: none !important;
  }

  td::before{
    content: attr(data-label);
    display:block;
    font-size:12px;
    color:var(--muted);
    font-weight:900;
    margin-bottom:4px;
  }

  .actions{
    width:100%;
    position: static;
    transform: none;
    flex-direction:column;
    align-items:stretch;
  }
  a.btn-edit, .btn-del{
    width:100%;
    justify-content:center;
    padding:12px 14px;
    border-radius:14px;
  }
}
</style>
</head>

<body>
  <div class="wrap">
    <div class="layout">
      <aside class="sidebar">
      <div class="side-title">Menu Utama</div>
      <ul class="side-list">
        <li>
          <a class="side-link" href="/SortirDokumen/pages/homepage.php">
            <div class="side-icon">🏠</div>
            <div class="side-text">
              <strong>Homepage</strong>
              <span>Kembali ke beranda.</span>
            </div>
          </a>
        </li>
        <li>
          <a class="side-link" href="/SortirDokumen/pages/form.php">
            <div class="side-icon">🗂️</div>
              <div class="side-text">
                <strong>Sortir Dokumen</strong>
                <span>Kelola kategori dokumen.</span>
              </div>
            </a>
          </li>
          <li>
            <a class="side-link" href="/SortirDokumen/pages/arsip.php">
              <div class="side-icon">🗄️</div>
              <div class="side-text">
                <strong>Rekapitulasi Arsip</strong>
                <span>Ringkasan seluruh arsip.</span>
              </div>
            </a>
          </li>
          <li>
            <a class="side-link" href="/SortirDokumen/pages/input_arsip.php">
              <div class="side-icon">🧾</div>
              <div class="side-text">
                <strong>Pemusnahan Dokumen</strong>
                <span>Input arsip dimusnahkan.</span>
              </div>
            </a>
          </li>
          <li>
            <a class="side-link active" href="/SortirDokumen/pages/tabel_arsip.php">
              <div class="side-icon">📊</div>
              <div class="side-text">
                <strong>Tabel Pemusnahan</strong>
                <span>Riwayat penghapusan.</span>
              </div>
            </a>
          </li>
        <li>
          <details class="side-accordion">
            <summary class="side-link">
              <div class="side-icon">📤</div>
              <div class="side-text">
                <strong>Ekspor</strong>
                <span>Unduh data arsip.</span>
              </div>
            </summary>
            <div class="side-sub">
              <a class="side-sublink" href="/SortirDokumen/pages/export_menu.php">Export Sortir Dokumen</a>
              <a class="side-sublink" href="/SortirDokumen/pages/export_pemusnahan.php">Export Dokumen Musnah</a>
            </div>
          </details>
        </li>
      </ul>
    </aside>

      <div class="card">

      <!-- ✅ HEAD fixed -->
      <div class="shell-head">
        <div class="header">
          <div class="title">
            <h1>DAFTAR ARSIP YANG DIMUSNAHKAN</h1>
            <div class="sub">Stasiun Meteorologi Kelas I Juanda – Sidoarjo</div>
          </div>

          <div class="btns">
            <!-- karena file ini ada di /pages, link cukup relatif -->
            <a class="btn secondary" href="input_arsip.php">➕ Form Input</a>
            <a class="btn primary" href="/SortirDokumen/pages/homepage.php">
              <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">
                <path d="M15 6L9 12l6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Kembali
            </a>
          </div>
        </div>
      </div>

      <!-- ✅ TOOLS fixed (alert + search gak ikut scroll) -->
      <div class="shell-tools">
        <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
          <div class="alert-ok">
            <span class="icon-check" aria-hidden="true">
              <svg viewBox="0 0 64 64" role="img" aria-label="Success">
                <rect x="4" y="4" width="56" height="56" rx="14" fill="#22C55E"/>
                <path d="M20 33.5L28.5 42L46 22.5" fill="none" stroke="#FFFFFF" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            <span>Data berhasil dihapus.</span>
          </div>
        <?php elseif (!empty($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
          <div class="alert-ok">
            <span class="icon-check" aria-hidden="true">
              <svg viewBox="0 0 64 64" role="img" aria-label="Success">
                <rect x="4" y="4" width="56" height="56" rx="14" fill="#22C55E"/>
                <path d="M20 33.5L28.5 42L46 22.5" fill="none" stroke="#FFFFFF" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            <span>Data berhasil diupdate.</span>
          </div>
        <?php elseif (!empty($_GET['msg']) && $_GET['msg'] === 'created'): ?>
          <div class="alert-ok">
            <span class="icon-check" aria-hidden="true">
              <svg viewBox="0 0 64 64" role="img" aria-label="Success">
                <rect x="4" y="4" width="56" height="56" rx="14" fill="#22C55E"/>
                <path d="M20 33.5L28.5 42L46 22.5" fill="none" stroke="#FFFFFF" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            <span>Data berhasil ditambahkan.</span>
          </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
          <div class="alert-err"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="search-row">
          <form class="search" method="GET" action="">
            <input
              type="text"
              name="q"
              placeholder="Cari: kode, nama berkas, pencipta, uraian, tanggal, dll..."
              value="<?= htmlspecialchars($q ?? '') ?>"
            />
            <button type="submit">🔎 Cari</button>
          </form>

          <?php if (!empty($q)): ?>
            <a class="clear" href="tabel_arsip.php">✖ Reset</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- ✅ BODY (scroll cuma tabel) -->
      <div class="shell-body">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>No</th>
                <th>Nomor Berkas</th>
                <th>Kode Klasifikasi</th>
                <th>Nama Berkas</th>
                <th>No. Isi</th>
                <th>Pencipta</th>
                <th>Tujuan Surat</th>
                <th>No. Surat</th>
                <th>Uraian Informasi 1</th>
                <th>Uraian Informasi 2</th>
                <th>Tanggal Surat</th>
                <th>Kurun Waktu</th>
                <th>Jumlah</th>
                <th>SKKAD</th>
                <th>Tingkat Perkembangan</th>
                <th>Boks</th>
                <th>Keterangan</th>
                <th aria-label="Aksi"></th>
              </tr>
            </thead>

            <tbody>
            <?php if (empty($rows) || count($rows) === 0): ?>
              <tr>
                <td class="muted" colspan="18">
                  <?= !empty($q) ? "Data tidak ditemukan untuk pencarian: " . htmlspecialchars($q) : "Belum ada data" ?>
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; foreach ($rows as $r): ?>
              <?php $rowId = $r["id"] ?? null; ?>
              <?php
                $uraian1 = $r["uraian_informasi_1"] ?? ($r["uraian"] ?? "");
                $uraian2 = $r["uraian_informasi_2"] ?? "";
                $tanggalSurat = $r["tanggal_surat"] ?? "";
                $kurunWaktu = $r["kurun_waktu"] ?? "";
                $tanggalLegacy = (string)($r["tanggal"] ?? "");
                if ($tanggalSurat === "" && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLegacy)) {
                  $tanggalSurat = $tanggalLegacy;
                }
                if ($kurunWaktu === "" && $tanggalLegacy !== "" && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLegacy)) {
                  $kurunWaktu = $tanggalLegacy;
                }
              ?>
              <tr>
                <td data-label="No"><?= $no++ ?></td>
                <td data-label="Nomor Berkas"><?= htmlspecialchars($r["nomor_berkas"] ?? "") ?></td>
                <td data-label="Kode Klasifikasi"><?= htmlspecialchars($r["kode_klasifikasi"] ?? "") ?></td>
                <td data-label="Nama Berkas"><?= htmlspecialchars($r["nama_berkas"] ?? "") ?></td>
                <td data-label="No. Isi"><?= htmlspecialchars($r["no_isi"] ?? "") ?></td>
                <td data-label="Pencipta"><?= htmlspecialchars($r["pencipta"] ?? "") ?></td>
                <td data-label="Tujuan Surat"><?= htmlspecialchars($r["tujuan_surat"] ?? "") ?></td>
                <td data-label="No. Surat"><?= htmlspecialchars($r["no_surat"] ?? "") ?></td>
                <td data-label="Uraian Informasi 1"><?= htmlspecialchars($uraian1) ?></td>
                <td data-label="Uraian Informasi 2"><?= htmlspecialchars($uraian2) ?></td>
                <td data-label="Tanggal Surat"><?= htmlspecialchars($tanggalSurat) ?></td>
                <td data-label="Kurun Waktu"><?= htmlspecialchars($kurunWaktu) ?></td>
                <td data-label="Jumlah"><?= htmlspecialchars($r["jumlah"] ?? "") ?></td>
                <td data-label="SKKAD"><?= htmlspecialchars($r["skkad"] ?? "") ?></td>
                <td data-label="Tingkat Perkembangan"><?= htmlspecialchars($r["tingkat"] ?? "") ?></td>
                <td data-label="Boks"><?= htmlspecialchars($r["lokasi"] ?? "") ?></td>
                <td data-label="Keterangan"><?= htmlspecialchars($r["keterangan"] ?? "") ?></td>
                
                <td data-label="Aksi">
                  <div class="actions">
                    <?php if ($rowId !== null): ?>
                      <a class="btn-edit" href="input_arsip.php?edit=<?= urlencode((string)$rowId) ?>">Edit</a>

                      <!-- ✅ hapus diarahkan ke actions/proses_tabel.php -->
                      <form method="POST" action="../actions/proses_tabel.php" class="js-delete-form">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$rowId) ?>">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                        <button class="btn-del" type="submit">Hapus</button>
                      </form>
                    <?php else: ?>
                      <span class="muted">-</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      </div>
    </div>
  </div>

  <div id="confirmDeleteModal" class="modal" aria-hidden="true">
    <div class="confirm-card" role="dialog" aria-modal="true">
      <div class="confirm-icon">⚠️</div>
      <div class="confirm-title">Konfirmasi</div>
      <div class="confirm-text">Yakin mau hapus data ini?</div>
      <div class="confirm-actions">
        <button type="button" class="btn-confirm cancel" id="cancelDelete">Batal</button>
        <button type="button" class="btn-confirm ok" id="okDelete">OK</button>
      </div>
    </div>
  </div>

  <script>
    const confirmDeleteModal = document.getElementById("confirmDeleteModal");
    const cancelDelete = document.getElementById("cancelDelete");
    const okDelete = document.getElementById("okDelete");
    let pendingDeleteForm = null;

    function openDeleteModal(form) {
      pendingDeleteForm = form;
      confirmDeleteModal.classList.add("show");
      confirmDeleteModal.setAttribute("aria-hidden", "false");

      okDelete?.focus();
    }

    document.addEventListener("click", (e) => {
      const form = e.target.closest(".js-delete-form");
      if (!form) return;

      e.preventDefault();
      openDeleteModal(form);
    });

    function closeDeleteModal() {
      confirmDeleteModal.classList.remove("show");
      confirmDeleteModal.setAttribute("aria-hidden", "true");
      pendingDeleteForm = null;
    }

    cancelDelete?.addEventListener("click", closeDeleteModal);
    confirmDeleteModal?.addEventListener("click", (e) => {
      if (e.target === confirmDeleteModal) closeDeleteModal();
    });
    okDelete?.addEventListener("click", () => {
      if (pendingDeleteForm) pendingDeleteForm.submit();
    });
    document.addEventListener("keydown", (e) => {
      const modalOpen = confirmDeleteModal.classList.contains("show");
      if (!modalOpen) return;

      if (e.key === "Escape") {
        e.preventDefault();
        closeDeleteModal();
        return;
      }

      if (e.key === "Enter") {
        e.preventDefault();
        if (pendingDeleteForm) pendingDeleteForm.submit();
      }
    });

  </script>
</body>
</html>
