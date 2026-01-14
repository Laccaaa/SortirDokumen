<?php
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
  height:100vh;
  overflow:hidden;
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
  margin-bottom:10px;
}

.title h1{
  margin:0 0 6px;
  font-size:20px;
}
.sub{
  font-size:13px;
  color:var(--muted);
}

/* tombol */
.btns{
  display:flex;
  gap:10px;
  align-items:center;
}

/* urutan tombol: Form Input kiri, Balik kanan */
.btns .secondary{ order: 1; }
.btns .primary{ order: 2; }

a.btn{
  padding:10px 14px;
  border-radius:12px;
  text-decoration:none;
  font-weight:700;
  font-size:14px;
  background:#1f2a44;
  color:#fff;
  display:inline-flex;
  gap:8px;
  align-items:center;
}
a.btn.secondary{
  background:#eef2ff;
  color:#1f2a44;
  border:1px solid #d7ddff;
}

/* search bar */
.search-row{
  display:flex;
  justify-content:space-between;
  gap:12px;
  flex-wrap:wrap;
  margin: 6px 0 12px;
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
  font-weight:800;
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

  body{
    padding:14px;                 
    align-items:flex-start;
    overflow:auto;                
  }

  .card{
    padding:14px;
    border-radius:18px;
  }

  .header{
    flex-direction:column;
    align-items:flex-start;
    gap:10px;
    margin-bottom:10px;
  }

  .title h1{
    font-size:18px;
    line-height:1.15;
  }

  .sub{
    font-size:12px;
  }

  .btns{
    width:100%;
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:10px;
  }

  a.btn{
    width:100%;
    justify-content:center;
    padding:10px 12px;
    border-radius:14px;
  }

  .search-row{
    width:100%;
    flex-direction:column;
    gap:10px;
    margin: 6px 0 12px;
  }

  .search{
    width:100%;
    min-width:unset;
    display:grid;
    grid-template-columns: 1fr auto;
    gap:10px;
  }

  .search input{
    width:100%;
    min-width:0;
  }

  .search button{
    white-space:nowrap;
  }

  a.clear{
    width:100%;
    text-align:center;
    padding:10px 12px;
    border-radius:14px;
  }

  .table-wrap{
    border-radius:16px;
    overflow:hidden;              
  }

  thead{ display:none; }
  table, tbody, tr, td{ display:block; width:100%; }

  tr{
    margin:0;
    border-bottom:1px solid #eef2f7;
    border-radius:0;
    box-shadow:none;
    padding:12px 14px;
    background:#fff;
  }

  tbody tr:nth-child(even){
    background:#fbfcff;
  }

  td{
    border:none;
    padding:10px 0;
  }

  td::before{
    content: attr(data-label);
    display:block;
    font-size:12px;
    color:var(--muted);
    font-weight:800;
    margin-bottom:4px;
  }

  /* Biar value gak kepotong */
  td{
    overflow-wrap:anywhere;
    word-break:break-word;
  }

  /* baris "Belum ada data" */
  td.muted{
    padding:14px;
    text-align:center;
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

      <!-- Form Input di kiri, Balik di kanan -->
      <div class="btns">
        <a class="btn secondary" href="input_arsip.php">➕ Form Input</a>
        <a class="btn primary" href="index.php">⬅️ Balik</a>
      </div>
    </div>

    <!-- SEARCH -->
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
            <td class="muted" colspan="12">
              <?= !empty($q) ? "Data tidak ditemukan untuk pencarian: " . htmlspecialchars($q) : "Belum ada data" ?>
            </td>
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
