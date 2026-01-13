<?php
// daftar_arsip.php (FRONTEND)
require_once "proses_tabel.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Daftar Arsip</title>

<style>
:root{
  --card:#ffffff;
  --text:#1f2a44;
  --muted:#667085;
  --line:#e6e8ef;
}

*{ box-sizing:border-box; }

body{
  margin:0;
  min-height:100vh;
  background:
    radial-gradient(
      150% 90% at 50% 120%,
      #1f8a70 0%,
      #34a37f 25%,
      #7ccfb3 45%,
      #cfeee2 60%,
      transparent 75%
    ),
    linear-gradient(180deg,#ffffff 0%,#f2fbf7 55%,#e6f7f1 100%);
  display:flex;
  justify-content:center;
  align-items:flex-start;
  padding:56px 20px;
  font-family: Inter, Arial, sans-serif;
}

.wrap{
  width:100%;
  max-width:1180px;
  display:flex;
  justify-content:center;
}

.card{
  width:100%;
  background:#fff;
  border-radius:22px;
  padding:22px;
  box-shadow:
    0 10px 30px rgba(15,23,42,.08),
    0 30px 60px rgba(15,23,42,.06);
}

.header{
  display:flex;
  justify-content:space-between;
  gap:12px;
  flex-wrap:wrap;
  margin-bottom:16px;
}

.title h1{
  margin:0 0 6px;
  font-size:20px;
}
.sub{
  font-size:13px;
  color:var(--muted);
}

.btns{
  display:flex;
  gap:10px;
}

a.btn{
  padding:10px 14px;
  border-radius:12px;
  text-decoration:none;
  font-weight:700;
  font-size:14px;
  background:#1f2a44;
  color:#fff;
}
a.btn.secondary{
  background:#eef2ff;
  color:#1f2a44;
  border:1px solid #d7ddff;
}

.table-wrap{
  margin-top:12px;
  border-radius:16px;
  overflow:hidden;
  border:1px solid #eef2f7;
  box-shadow:0 8px 24px rgba(15,23,42,.06);
}

table{
  width:100%;
  border-collapse:collapse;
  font-size:13px;
}

thead th{
  background:#f8fafc;
  padding:14px;
  border-bottom:1px solid #e5e7eb;
  font-size:12px;
  font-weight:700;
  color:#475569;
  white-space:nowrap;
}

tbody td{
  padding:14px;
  border-bottom:1px solid #eef2f7;
  color:#1f2937;
  vertical-align:top;
}

tbody tr:nth-child(even){
  background:#fafbff;
}
tbody tr:hover{
  background:#f1f5ff;
}

td.muted{
  text-align:center;
  color:var(--muted);
}

.alert-err{
  margin-bottom:12px;
  padding:10px 12px;
  border-radius:12px;
  background:#fff5f5;
  border:1px solid #ffd0d0;
  color:#7a1f1f;
  font-size:13px;
}

@media (max-width:768px){
  thead{ display:none; }
  table, tbody, tr, td{ display:block; width:100%; }
  tr{
    margin-bottom:14px;
    background:#fff;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(15,23,42,.06);
    padding:12px;
  }
  td{
    border:none;
    padding:6px 0;
  }
  td::before{
    content: attr(data-label);
    display:block;
    font-size:12px;
    color:var(--muted);
    font-weight:700;
    margin-bottom:2px;
  }
}
</style>
</head>

<body>
<div class="wrap">
  <div class="card">

    <div class="header">
      <div class="title">
        <h1>DAFTAR ARSIP YANG DIMUSNAHKAN</h1>
        <div class="sub">Stasiun Meteorologi Kelas I Juanda – Sidoarjo</div>
      </div>
      <div class="btns">
        <a class="btn" href="index.php">⬅️ Balik</a>
        <a class="btn secondary" href="input_arsip.php">➕ Form Input</a>
      </div>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert-err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No Berkas</th>
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
          </tr>
        </thead>

        <tbody>
        <?php if (count($rows) === 0): ?>
          <tr>
            <td class="muted" colspan="12">Belum ada data</td>
          </tr>
        <?php else: ?>
          <?php $no = 1; foreach ($rows as $r): ?>
          <tr>
            <td data-label="No Berkas"><?= $no++ ?></td>
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
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>
</body>
</html>
