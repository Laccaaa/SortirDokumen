<?php
// tabel_arsip.php — FULLSCREEN, NO SCROLL (X & Y), mobile safe
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Tabel Arsip</title>

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
  --radius: 20px;
}

/* 🔒 MATIIN SCROLL TOTAL */
html, body{
  width:100%;
  height:100vh;
  overflow: hidden;   /* KUNCI UTAMA */
  margin:0;
}

body{
  background: linear-gradient(135deg, var(--bg1), var(--bg2));
  font-family: Arial, sans-serif;
  color: var(--text);
  display:flex;
  align-items:center;     /* tengah vertikal */
  justify-content:center; /* tengah horizontal */
}

/* container full height */
.wrap{
  width:100%;
  max-width:1180px;
  height:100%;
  display:flex;
  align-items:center;
  justify-content:center;
  padding: 16px;
}

/* card utama */
.card{
  width:100%;
  max-height:100%;
  background: var(--card);
  border-radius: var(--radius);
  padding:22px;
  box-shadow: var(--shadow);
  display:flex;
  flex-direction:column;
}

/* header */
.header{
  display:flex;
  justify-content:space-between;
  gap:12px;
  margin-bottom:12px;
}

h1{
  font-size:20px;
  margin-bottom:6px;
}

.sub{
  font-size:13px;
  color:var(--muted);
}

/* buttons */
.btns{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
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
}

a.btn.secondary{
  background:#eef2ff;
  color:#1f2a44;
  border:1px solid #d7ddff;
}

/* tabel wrapper */
.table-wrap{
  flex:1;                 /* isi sisa tinggi card */
  overflow:hidden;        /* 🔒 NO SCROLL */
  border:1px solid var(--line);
  border-radius:14px;
  background:#fff;
}

/* tabel */
table{
  width:100%;
  border-collapse:collapse;
  font-size:12px;
}

thead th{
  background:var(--soft);
  padding:10px;
  border:1px solid var(--line);
  text-align:left;
}

td{
  padding:14px;
  border:1px solid var(--line);
  text-align:center;
  color:var(--muted);
}

/* hint */
.hint{
  font-size:12px;
  color:var(--muted);
  margin-top:8px;
}

/* 📱 MOBILE */
@media (max-width:700px){
  .header{
    flex-direction:column;
    align-items:stretch;
  }
  .btns{
    flex-direction:column;
  }
  a.btn{
    width:100%;
    text-align:center;
  }
}
</style>
</head>

<body>
  <div class="wrap">
    <div class="card">
      <div class="header">
        <div>
          <h1>DAFTAR ARSIP YANG DIMUSNAHKAN</h1>
          <div class="sub">
            Stasiun Meteorologi Kelas I Juanda – Sidoarjo (menunggu database)
          </div>
        </div>
        <div class="btns">
          <a class="btn" href="index.php">⬅️ Balik</a>
          <a class="btn secondary" href="input_arsip.php">➕ Form Input</a>
        </div>
      </div>

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
            <tr>
              <td colspan="12">Belum ada data (menunggu database)</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="hint">
        Catatan: halaman ini dikunci tanpa scroll (X & Y).
      </div>
    </div>
  </div>
</body>
</html>
