<?php
$dbhandle = require __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../auth/auth_check.php";

$tahun = $_GET['tahun'] ?? '';
$bulan = $_GET['bulan'] ?? '';

function getPemusnahanFilterOptions($conn, $tahun = null) {
    $yearExpr = "CASE
        WHEN tanggal ~ '^[0-9]{4}$' THEN tanggal::int
        WHEN tanggal ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN SUBSTRING(tanggal,1,4)::int
        ELSE NULL
    END";
    $monthExpr = "CASE
        WHEN tanggal ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN SUBSTRING(tanggal,6,2)::int
        ELSE NULL
    END";

    $yearsSql = "SELECT DISTINCT $yearExpr AS tahun
        FROM arsip_dimusnahkan
        WHERE $yearExpr IS NOT NULL
        ORDER BY tahun DESC";
    $yearStmt = $conn->prepare($yearsSql);
    $yearStmt->execute();
    $years = $yearStmt->fetchAll(PDO::FETCH_COLUMN);

    $monthsSql = "SELECT DISTINCT $monthExpr AS bulan
        FROM arsip_dimusnahkan
        WHERE $monthExpr IS NOT NULL";
    $params = [];
    if ($tahun) {
        $monthsSql .= " AND $yearExpr = ?";
        $params[] = (int)$tahun;
    }
    $monthsSql .= " ORDER BY bulan";
    $monthStmt = $conn->prepare($monthsSql);
    $monthStmt->execute($params);
    $months = $monthStmt->fetchAll(PDO::FETCH_COLUMN);

    return ['years' => $years, 'months' => $months];
}

function getPemusnahanRows($conn, $tahun = null, $bulan = null) {
    $yearExpr = "CASE
        WHEN tanggal ~ '^[0-9]{4}$' THEN tanggal::int
        WHEN tanggal ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN SUBSTRING(tanggal,1,4)::int
        ELSE NULL
    END";
    $monthExpr = "CASE
        WHEN tanggal ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN SUBSTRING(tanggal,6,2)::int
        ELSE NULL
    END";

    $sql = "SELECT
        id, nomor_berkas, kode_klasifikasi, nama_berkas, no_isi, pencipta, tujuan_surat, no_surat,
        uraian, uraian_informasi_1, uraian_informasi_2,
        tanggal, tanggal_surat, kurun_waktu,
        jumlah, skkad, tingkat, lokasi, keterangan, created_at
        FROM arsip_dimusnahkan WHERE 1=1";
    $params = [];

    if ($tahun) {
        $sql .= " AND $yearExpr = ?";
        $params[] = (int)$tahun;
    }
    if ($bulan) {
        $sql .= " AND $monthExpr = ?";
        $params[] = (int)$bulan;
    }

    $sql .= " ORDER BY $yearExpr DESC NULLS LAST, $monthExpr NULLS LAST, id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$filterOptions = getPemusnahanFilterOptions($dbhandle, $tahun !== '' ? $tahun : null);
$rows = getPemusnahanRows($dbhandle, $tahun !== '' ? $tahun : null, $bulan !== '' ? $bulan : null);
$monthNames = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Export Dokumen Musnah</title>

<style>
:root{
  --bg-start: #f3f5f9;
  --bg-end: #e2e7f1;
  --card: rgba(255,255,255,.96);
  --text:#0f172a;
  --muted:#64748b;
  --line:#e5e7eb;
  --shadow: 0 22px 70px rgba(0,0,0,.18);
  --radius: 24px;
  --btn: #0f172a;
  --btn2:#eef2ff;
}

*{ box-sizing:border-box; margin:0; padding:0; }
html, body{ width:100%; height:100%; margin:0; overflow:hidden; }
body{
  height:100vh;
  background: linear-gradient(135deg, var(--bg-start), var(--bg-end));
  position:relative;
  font-family: Inter, Arial, sans-serif;
  padding: 14px;
  display:flex;
  align-items:stretch;
  justify-content:center;
}

.wrap{ width:100%; max-width:none; height:100%; position:relative; z-index:2; display:flex; }
.layout{ width:100%; height:100%; display:flex; gap:14px; }

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

.side-title{ font-weight: 900; font-size: 14px; letter-spacing:.3px; color:#e2e8f0; text-transform: uppercase; }
.side-list{ display:flex; flex-direction:column; gap:10px; margin:0; padding:0; list-style:none; }

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
.side-link:hover{ transform: translateY(-1px); border-color:#5a63ff; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.2); }
.side-link.active{ background:#2a3350; border-color:#5a63ff; box-shadow: inset 0 0 0 1px rgba(90, 99, 255, .25); }
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
.side-sublink:hover{ transform: translateY(-1px); border-color:#5a63ff; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.2); }
.side-sublink.active{ background:#2a3350; border-color:#5a63ff; box-shadow: inset 0 0 0 1px rgba(90, 99, 255, .25); }
.side-icon{ width:36px; height:36px; border-radius: 12px; display:grid; place-items:center; background: rgba(90, 99, 255, .18); border:1px solid rgba(90, 99, 255, .25); font-size: 18px; flex: 0 0 auto; }
.side-text{ display:flex; flex-direction:column; gap:2px; min-width:0; }
.side-text strong{ font-size: 14px; line-height:1.2; }
.side-text span{ font-size: 12px; color: #94a3b8; line-height:1.25; }

.shell{
  width: 100%;
  height: 100%;
  max-width: none;
  flex: 1 1 auto;
  background: var(--card);
  border: 1px solid rgba(255,255,255,.55);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 12px 12px 10px;
  overflow:auto;
  overflow-x:hidden;
  -webkit-overflow-scrolling: touch;
  backdrop-filter: blur(10px);
}

.top{ display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap; margin-bottom: 10px; }
.titles h1{ margin:0; font-size: 20px; letter-spacing:.2px; line-height: 1.15; }
.titles p{ margin:6px 0 0; color: var(--muted); font-size: 12px; }

.actionsTop{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }

.btn{ display:inline-flex; gap:8px; align-items:center; padding:10px 16px; border-radius: 14px; text-decoration:none; font-weight:900; font-size: 13px; border: 1px solid transparent; white-space:nowrap; cursor:pointer; }
.btn.light{ background: var(--btn2); color: #1f2a44; border-color: #d7ddff; }
.btn.dark{ background: var(--btn); color: #fff; }
.btn.back{
  background:#334155;
  color:#ffffff;
  border:1px solid #334155;
  padding:10px 14px;
  border-radius:12px;
  min-height:36px;
}
.btn.back:hover{
  background:#475569;
  color:#ffffff;
}
.btn.back svg{ display:block; }

.filter-row{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin: 8px 0 12px; }
.filter-select{
  background:#f8fafc; border: 1px solid #e9ecef; padding: 7px 10px; border-radius: 10px; font-size: 12px; color:#0f172a;
}
.filter-note{ font-size: 11px; color: var(--muted); }

.table-wrap{ border: 1px solid #e5e7eb; border-radius: 12px; overflow:auto; background:#fff; }
.table{ width:max-content; min-width: 2200px; border-collapse: collapse; font-size: 12px; }
.table th, .table td{ border-bottom:1px solid #eef2f6; padding: 10px 12px; text-align:left; vertical-align:top; }
.table th{ background:#e5e7eb; color:#1f2a44; position:sticky; top:0; z-index:1; }
.table th:nth-child(2), .table td:nth-child(2){ min-width: 120px; }  /* Nomor Berkas */
.table th:nth-child(3), .table td:nth-child(3){ min-width: 140px; }  /* Kode Klasifikasi */
.table th:nth-child(4), .table td:nth-child(4){ min-width: 170px; }  /* Nama Berkas */
.table th:nth-child(7), .table td:nth-child(7){ min-width: 150px; }  /* Tujuan Surat */
.table th:nth-child(9), .table td:nth-child(9),
.table th:nth-child(10), .table td:nth-child(10){ min-width: 170px; } /* Uraian */
.table th:nth-child(15), .table td:nth-child(15){ min-width: 170px; } /* Tingkat Perkembangan */
.table th:nth-child(17), .table td:nth-child(17){ min-width: 180px; } /* Keterangan */

.empty-state{ text-align:center; padding: 40px 20px; color:#94a3b8; }

@media (max-width: 980px){
  body{ padding: 10px; }
  .layout{ flex-direction:column; }
  .sidebar{ width:100%; height:auto; }
  .shell{ max-width:100%; padding:14px; border-radius: 20px; }
  .actionsTop{ width:100%; }
  .btn{ flex:1; justify-content:center; }
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
          <a class="side-link" href="/SortirDokumen/pages/tabel_arsip.php">
            <div class="side-icon">📊</div>
            <div class="side-text">
              <strong>Tabel Pemusnahan</strong>
              <span>Riwayat penghapusan.</span>
            </div>
          </a>
        </li>
        <li>
          <details class="side-accordion" open>
            <summary class="side-link">
              <div class="side-icon">📤</div>
              <div class="side-text">
                <strong>Ekspor</strong>
                <span>Unduh data arsip.</span>
              </div>
            </summary>
            <div class="side-sub">
              <a class="side-sublink" href="/SortirDokumen/pages/export_menu.php">Export Sortir Dokumen</a>
              <a class="side-sublink active" href="/SortirDokumen/pages/export_pemusnahan.php">Export Dokumen Musnah</a>
            </div>
          </details>
        </li>
      </ul>
    </aside>

    <div class="shell">
      <div class="top">
        <div class="titles">
          <h1>Export Dokumen Musnah</h1>
          <p>Sortir berdasarkan tahun dan bulan, lalu unduh CSV.</p>
        </div>
        <div class="actionsTop">
          <a class="btn back" href="homepage.php">
            <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">
              <path d="M15 6L9 12l6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Kembali
          </a>
        </div>
      </div>

      <form class="filter-row" method="get" action="export_pemusnahan.php">
        <select class="filter-select" name="tahun">
          <option value="">Semua tahun</option>
          <?php foreach ($filterOptions['years'] as $yr): ?>
            <option value="<?= htmlspecialchars($yr) ?>" <?= (string)$tahun === (string)$yr ? 'selected' : '' ?>><?= htmlspecialchars($yr) ?></option>
          <?php endforeach; ?>
        </select>
        <select class="filter-select" name="bulan">
          <option value="">Semua bulan</option>
          <?php foreach ($filterOptions['months'] as $mo): ?>
            <option value="<?= htmlspecialchars($mo) ?>" <?= (string)$bulan === (string)$mo ? 'selected' : '' ?>>
              <?= htmlspecialchars($monthNames[(int)$mo] ?? $mo) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <a class="btn light" href="export_pemusnahan.php">Reset</a>
        <button class="btn light" type="submit">Terapkan</button>
        <a class="btn dark" href="/SortirDokumen/actions/export_pemusnahan_csv.php?tahun=<?= urlencode($tahun) ?>&bulan=<?= urlencode($bulan) ?>">Unduh CSV</a>
        <span class="filter-note">Filter update otomatis mengikuti data baru.</span>
      </form>

      <?php if (empty($rows)): ?>
        <div class="empty-state">Tidak ada data untuk filter ini.</div>
      <?php else: ?>
        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nomor Berkas</th>
                <th>Kode Klasifikasi</th>
                <th>Nama Berkas</th>
                <th>No. Isi Berkas</th>
                <th>Pencipta</th>
                <th>Tujuan Surat</th>
                <th>No Surat</th>
                <th>Uraian Informasi 1</th>
                <th>Uraian Informasi 2</th>
                <th>Tanggal Surat</th>
                <th>Kurun Waktu</th>
                <th>Jumlah</th>
                <th>SKKAD</th>
                <th>Tingkat Perkembangan</th>
                <th>Boks</th>
                <th>Keterangan</th>
                <th>Created At</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $i => $row): ?>
                <?php
                  $uraian1 = $row['uraian_informasi_1'] ?? ($row['uraian'] ?? '');
                  $uraian2 = $row['uraian_informasi_2'] ?? '';
                  $tanggalSurat = $row['tanggal_surat'] ?? '';
                  $kurunWaktu = $row['kurun_waktu'] ?? '';
                  $tanggalLegacy = (string)($row['tanggal'] ?? '');
                  if ($tanggalSurat === '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLegacy)) {
                    $tanggalSurat = $tanggalLegacy;
                  }
                  if ($kurunWaktu === '' && $tanggalLegacy !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLegacy)) {
                    $kurunWaktu = $tanggalLegacy;
                  }
                ?>
                <tr>
                  <td><?= $i + 1 ?></td>
                  <td><?= htmlspecialchars($row['nomor_berkas'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['kode_klasifikasi'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['nama_berkas'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['no_isi'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['pencipta'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['tujuan_surat'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['no_surat'] ?? '') ?></td>
                  <td><?= htmlspecialchars($uraian1) ?></td>
                  <td><?= htmlspecialchars($uraian2) ?></td>
                  <td><?= htmlspecialchars($tanggalSurat) ?></td>
                  <td><?= htmlspecialchars($kurunWaktu) ?></td>
                  <td><?= htmlspecialchars($row['jumlah'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['skkad'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['tingkat'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['lokasi'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
