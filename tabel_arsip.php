<?php
// tabel_arsip.php — TOP ALIGNED, NO BODY SCROLL (X & Y), MOBILE FRIENDLY (table -> cards)
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
  background: linear-gradient(135deg, var(--bg1), var(--bg2));
  height:100vh;
  overflow:hidden;           /* 🔒 NO SCROLL TOTAL */

  display:flex;
  justify-content:center;    /* center horizontal */
  align-items:flex-start;    /* ✅ NEMPEL ATAS */
  padding: 18px;
  color:var(--text);
  font-family: Arial, sans-serif;
}

/* wrapper */
.wrap{
  width:100%;
  max-width:1180px;
  height:100%;
  display:flex;
  align-items:flex-start;    /* ✅ NEMPEL ATAS */
  justify-content:center;
}

/* card utama */
.card{
  width:100%;
  background: var(--card);
  border-radius: var(--radius);
  padding: 18px;
  box-shadow: var(--shadow);

  display:flex;
  flex-direction:column;

  /* ✅ penting: biar card gak “ngambang” dan tetap di atas */
  margin-top: 0;
}

/* header */
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

/* actions */
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

/* table wrapper */
.table-wrap{
  border:1px solid var(--line);
  border-radius: 14px;
  background:#fff;
  overflow:hidden;           /* 🔒 NO SCROLL di wrapper juga */
}

/* tabel desktop */
table{
  width:100%;
  border-collapse:collapse;
  font-size:12px;
}
thead th{
  background:var(--soft);
  padding:10px 10px;
  border:1px solid var(--line);
  text-align:left;
}
tbody td{
  padding:12px 10px;
  border:1px solid var(--line);
  text-align:left;
  color: var(--text);
}
tbody td.muted{
  color: var(--muted);
  text-align:center;
}

/* helper kecil */
.hint{
  margin-top:10px;
  font-size:12px;
  color:var(--muted);
}

/* =========================
   ✅ MOBILE MODE: TABLE -> CARDS
   biar 12 kolom gak bikin layout ancur & tetap NO SCROLL X
========================= */
@media (max-width: 768px){
  body{ padding: 12px; }

  .card{
    padding: 14px;
    border-radius: 16px;
  }

  .title h1{
    font-size: 18px;
    line-height: 1.15;
  }
  .sub{ font-size: 12px; }

  /* tombol full width biar rapi */
  .btns{
    width:100%;
  }
  .btns a.btn{
    width:100%;
  }

  /* hide header table, transform rows to cards */
  table{
    font-size: 13px;
  }
  thead{
    display:none;
  }

  tbody, tr, td{
    display:block;
    width:100%;
  }

  tr{
    border-bottom: 1px solid var(--line);
    padding: 10px 12px;
  }

  td{
    border:none !important;
    padding: 8px 0 !important;
    text-align:left !important;
  }

  /* label di kiri, value di kanan */
  td::before{
    content: attr(data-label);
    display:block;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 3px;
    font-weight: 700;
  }

  /* row kosong (colspan 12) */
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
            <!-- contoh data kosong -->
            <tr>
              <td class="muted" colspan="12">Belum ada data (menunggu database)</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="hint"></div>
    </div>
  </div>
</body>
</html>
