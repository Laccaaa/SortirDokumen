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
  overflow-x:hidden;             /* ✅ no scroll kanan-kiri */
  -webkit-overflow-scrolling: touch;
  border:1px solid #eef2f7;
  box-shadow:0 8px 24px rgba(15,23,42,.06);
}

/* table */
table{
  width:100%;
  border-collapse:collapse;
  font-size:11px;
  table-layout: fixed;
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

tbody td{
  padding:10px 8px;
  border-bottom:1px solid #eef2f7;
  color:#1f2937;
  vertical-align:top;
  background:#fff;
  word-break: break-word;
}

tbody tr:nth-child(even) td{ background:#fafbff; }
tbody tr:hover td{ background:#f1f5ff; }

td.muted{
  text-align:center;
  color:var(--muted);
  background:#fff;
}

/* aksi */
.actions{
  display:flex;
  gap:6px;
  flex-wrap:wrap;
  align-items:center;
}

a.btn-edit{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:6px 8px;
  border-radius:12px;
  text-decoration:none;
  font-weight:900;
  font-size:11px;
  background:#eef2ff;
  color:#1f2a44;
  border:1px solid #d7ddff;
}

.btn-del{
  border:1px solid #ffd0d0;
  background:#fff5f5;
  color:#7a1f1f;
  font-weight:900;
  font-size:11px;
  padding:6px 8px;
  border-radius:12px;
  cursor:pointer;
}
.btn-del:active{ transform: translateY(1px); }

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
  }
  tbody tr:nth-child(even){ background:#fbfcff; }

  td{
    border:none;
    padding:10px 0;
    overflow-wrap:anywhere;
    word-break:break-word;
    background: transparent !important;
  }

  td::before{
    content: attr(data-label);
    display:block;
    font-size:12px;
    color:var(--muted);
    font-weight:900;
    margin-bottom:4px;
  }

  .actions{ width:100%; }
  a.btn-edit, .btn-del{
    width:100%;
    justify-content:center;
    padding:10px 12px;
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
          <a class="side-link" href="/SortirDokumen/pages/export_menu.php">
            <div class="side-icon">📥</div>
            <div class="side-text">
              <strong>Export CSV</strong>
              <span>Unduh data CSV/Excel.</span>
            </div>
          </a>
        </li>
        <li>
          <a class="side-link" href="/SortirDokumen/pages/export_pemusnahan.php">
            <div class="side-icon">🗑️</div>
            <div class="side-text">
              <strong>Export Dokumen Musnah</strong>
              <span>Unduh data arsip dimusnahkan.</span>
            </div>
          </a>
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
            <a class="btn primary" href="/SortirDokumen/pages/homepage.php">⬅️ Balik</a>
          </div>
        </div>
      </div>

      <!-- ✅ TOOLS fixed (alert + search gak ikut scroll) -->
      <div class="shell-tools">
        <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
          <div class="alert-ok">✅ Data berhasil dihapus.</div>
        <?php elseif (!empty($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
          <div class="alert-ok">✅ Data berhasil diupdate.</div>
        <?php elseif (!empty($_GET['msg']) && $_GET['msg'] === 'created'): ?>
          <div class="alert-ok">✅ Data berhasil ditambahkan.</div>
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
                <th>Kode Klasifikasi</th>
                <th>Nama Berkas</th>
                <th>No. Isi</th>
                <th>Pencipta</th>
                <th>No. Surat</th>
                <th>Uraian</th>
                <th>Tanggal</th>
                <th>Jumlah</th>
                <th>Tingkat</th>
                <th>Lokasi</th>
                <th>Keterangan</th>
                <th>Aksi</th>
              </tr>
            </thead>

            <tbody>
            <?php if (empty($rows) || count($rows) === 0): ?>
              <tr>
                <td class="muted" colspan="13">
                  <?= !empty($q) ? "Data tidak ditemukan untuk pencarian: " . htmlspecialchars($q) : "Belum ada data" ?>
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; foreach ($rows as $r): ?>
              <?php $rowId = $r["id"] ?? null; ?>
              <tr>
                <td data-label="No"><?= $no++ ?></td>
                <td data-label="Kode Klasifikasi"><?= htmlspecialchars($r["kode_klasifikasi"] ?? "") ?></td>
                <td data-label="Nama Berkas"><?= htmlspecialchars($r["nama_berkas"] ?? "") ?></td>
                <td data-label="No. Isi"><?= htmlspecialchars($r["no_isi"] ?? "") ?></td>
                <td data-label="Pencipta"><?= htmlspecialchars($r["pencipta"] ?? "") ?></td>
                <td data-label="No. Surat"><?= htmlspecialchars($r["no_surat"] ?? "") ?></td>
                <td data-label="Uraian"><?= htmlspecialchars($r["uraian"] ?? "") ?></td>
                <td data-label="Tanggal"><?= htmlspecialchars($r["tanggal"] ?? "") ?></td>
                <td data-label="Jumlah"><?= htmlspecialchars($r["jumlah"] ?? "") ?></td>
                <td data-label="Tingkat"><?= htmlspecialchars($r["tingkat"] ?? "") ?></td>
                <td data-label="Lokasi"><?= htmlspecialchars($r["lokasi"] ?? "") ?></td>
                <td data-label="Keterangan"><?= htmlspecialchars($r["keterangan"] ?? "") ?></td>
                
                <td data-label="Aksi">
                  <div class="actions">
                    <?php if ($rowId !== null): ?>
                      <a class="btn-edit" href="input_arsip.php?edit=<?= urlencode((string)$rowId) ?>">✏️ Edit</a>

                      <!-- ✅ hapus diarahkan ke actions/proses_tabel.php -->
                      <form method="POST" action="../actions/proses_tabel.php" onsubmit="return confirm('Yakin mau hapus data ini? 😬');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$rowId) ?>">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                        <button class="btn-del" type="submit">🗑️ Hapus</button>
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
</body>
</html>
