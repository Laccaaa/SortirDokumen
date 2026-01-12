<?php
require_once "koneksi.php";

$error = "";
$rows = [];

try {
  $stmt = $pdo->query("
    SELECT no_berkas, kode_klasifikasi, nama_berkas, no_isi, pencipta, no_surat, uraian, tanggal, jumlah, tingkat, lokasi, keterangan
    FROM arsip_dimusnahkan
    ORDER BY created_at DESC
  ");
  $rows = $stmt->fetchAll();
} catch (PDOException $e) {
  $error = "Gagal ambil data: " . $e->getMessage();
  $rows = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Daftar Arsip</title>

<style>
:root{
  --bg1:#4a6cf7;
  --bg2:#6fb1c8;
  --card:#ffffff;
  --text:#1f2a44;
  --muted:#667085;
  --line:#e6e8ef;
  --soft:#f6f7fb;
  --shadow: 0 20px 50px rgba(0,0,0,0.18);
  --radius: 18px;
}

*{ box-sizing:border-box; }
html, body{ height:100%; }

body{
  margin:0;
  min-height:100vh;

  background:
    radial-gradient(
      150% 90% at 50% 120%,
      #4d8879ff 0%,     
      #47a084ff 25%,    
      #7ccfb3 45%,    
      #cfeee2 60%,    
      transparent 75%
    ),
    
    linear-gradient(
      180deg,
      #f4f4f4ff 0%,
      #f2fbf7 55%,
      #e6f7f1 100%
    );

  display:flex;
  justify-content:center;
  align-items:flex-start;
  padding: 56px 20px;
  font-family: Inter, Arial, sans-serif;
}




.wrap{
  height: 100%;
  width:100%;
  max-width:1180px;
  display:flex;
  align-items:flex-start;
  justify-content:center;
}


.card{
  width:100%;
  max-width:1200px;
  background:#ffffff;
  border-radius:22px;
  padding: 22px;
  box-shadow:
    0 10px 30px rgba(15,23,42,0.08),
    0 30px 60px rgba(15,23,42,0.06);
  display:flex;
  flex-direction:column;
}

.header{
  display:flex;
  align-items:flex-start;
  justify-content:space-between;
  gap:12px;
  flex-wrap:wrap;
  margin-bottom: 12px;
}

.title h1{
  margin:0 0 6px 0;
  font-size:20px;
  letter-spacing:0.2px;
}
.sub{
  font-size:13px;
  color:var(--muted);
}

.btns{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
  align-items:center;
}

a.btn{
  text-decoration:none;
  padding:10px 14px;
  border-radius:12px;
  font-weight:700;
  background:#1f2a44;
  color:#fff;
  font-size:14px;
  white-space:nowrap;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
}
a.btn.secondary{
  background:#eef2ff;
  color:#1f2a44;
  border:1px solid #d7ddff;
}

.table-wrap{
  margin-top: 12px;
  border-radius:16px;
  background:#ffffff;
  box-shadow: 0 8px 24px rgba(15,23,42,0.06);
  overflow:hidden;
  border:1px solid #eef2f7;
}

table{
  width:100%;
  border-collapse:collapse;
  font-size:12px;
}
thead th{
  background:#f8fafc;
  padding:14px 14px;
  border-bottom:1px solid #e5e7eb;
  font-size:12px;
  font-weight:700;
  color:#475569;
  white-space:nowrap;
}

tbody td{
  padding:14px 14px;
  border-bottom:1px solid #eef2f7;
  font-size:13px;
  color:#1f2937;
  vertical-align:top;
}

tbody tr:nth-child(even){
  background:#fafbff;
}

tbody tr:hover{
  background:#f1f5ff;
}

tbody td.muted{
  color: var(--muted);
  text-align:center;
}

.alert-ok{
  padding: 10px 12px;
  border-radius: 12px;
  margin: 10px 0 12px 0;
  font-size: 13px;
  border: 1px solid #bfe7c7;
  background: #f1fff4;
  color: #14532d;
}
.alert-err{
  padding: 10px 12px;
  border-radius: 12px;
  margin: 10px 0 12px 0;
  font-size: 13px;
  border: 1px solid #ffd0d0;
  background: #fff5f5;
  color: #7a1f1f;
}

@media (max-width: 768px){
  body{ padding: 12px; }
  .card{ padding: 14px; border-radius: 16px; }
  .title h1{ font-size: 18px; line-height: 1.15; }
  .sub{ font-size: 12px; }

  .btns{ width:100%; }
  .btns a.btn{ width:100%; }

  table{ font-size: 13px; }
  thead{ display:none; }

  tbody, tr, td{ display:block; width:100%; }
  tr{ border-bottom: 1px solid var(--line); padding: 10px 12px; }
  td{ border:none !important; padding: 8px 0 !important; text-align:left !important; }

  td::before{
    content: attr(data-label);
    display:block;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 3px;
    font-weight: 700;
  }

  td.muted{
    text-align:left !important;
    color: var(--muted);
    padding: 6px 0 !important;
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

      <?php if (isset($_GET["success"]) && $_GET["success"] == "1"): ?>
        <div class="alert-ok">Data berhasil disimpan ✅</div>
      <?php endif; ?>

      <?php if ($error !== ""): ?>
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
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td data-label="No Berkas"><?= htmlspecialchars($r["no_berkas"]) ?></td>
                  <td data-label="Kode Klasifikasi"><?= htmlspecialchars($r["kode_klasifikasi"]) ?></td>
                  <td data-label="Nama Berkas"><?= htmlspecialchars($r["nama_berkas"]) ?></td>
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
