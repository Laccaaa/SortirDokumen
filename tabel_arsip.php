<?php
// tabel_arsip.php — hanya tabel (kosong), siap diisi dari database nanti
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tabel Arsip</title>
  <style>
    *{ margin:0; padding:0; box-sizing:border-box; }
    body{
      min-height:100vh;
      background: linear-gradient(135deg, #4a6cf7, #6fb1c8);
      font-family: Arial, sans-serif;
      padding: 24px;
    }
    .wrap{
      width:100%;
      max-width: 1180px;
      margin: 0 auto;
      display:flex;
      flex-direction:column;
      gap:18px;
    }
    .card{
      background:#fff;
      border-radius:20px;
      padding:22px;
      box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    }
    .header{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:12px;
      margin-bottom: 14px;
    }
    h1{
      font-size: 20px;
      color:#1f2a44;
      margin-bottom:6px;
    }
    .sub{
      font-size: 13px;
      color:#667085;
    }
    .btns{ display:flex; gap:10px; flex-wrap:wrap; }
    a.btn{
      appearance:none;
      border:0;
      cursor:pointer;
      text-decoration:none;
      padding: 10px 14px;
      border-radius: 12px;
      font-weight: 700;
      background:#1f2a44;
      color:#fff;
      transition: transform .12s ease, opacity .12s ease;
      font-size: 14px;
      display:inline-flex;
      align-items:center;
      gap:8px;
    }
    a.btn:hover{ transform: translateY(-1px); opacity:.95; }
    a.btn.secondary{
      background:#eef2ff;
      color:#1f2a44;
      border: 1px solid #d7ddff;
    }

    .table-wrap{ overflow:auto; border-radius:14px; border:1px solid #e6e8ef; }
    table{
      width: 1400px;
      border-collapse: collapse;
      font-size: 12px;
    }
    thead th{
      position: sticky;
      top: 0;
      background:#f6f7fb;
      z-index:1;
    }
    th, td{
      border: 1px solid #e6e8ef;
      padding: 8px 8px;
      vertical-align: top;
      text-align:left;
      white-space: nowrap;
    }
    th{ font-size: 12px; color:#1f2a44; }
    .hint{
      margin-top: 10px;
      font-size: 12px;
      color:#667085;
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="header">
        <div>
          <h1>DAFTAR ARSIP YANG DIMUSNAHKAN</h1>
          <div class="sub">Stasiun Meteorologi Kelas I Juanda - Sidoarjo (tabel kosong, menunggu database)</div>
        </div>
        <div class="btns">
          <a class="btn" href="index.php">⬅️ Balik ke Sortir Dokumen</a>
          <a class="btn secondary" href="input_arsip.php">➕ Ke Form Input</a>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>No Berkas</th>
              <th>Kode Klasifikasi</th>
              <th>Nama Berkas</th>
              <th>No. Isi Berkas</th>
              <th>Pencipta Arsip</th>
              <th>No. Surat</th>
              <th>Uraian Informasi</th>
              <th>Tanggal Surat / Kurun Waktu</th>
              <th>Jumlah</th>
              <th>Tingkat Perkembangan</th>
              <th>Lokasi Simpan</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <!-- NANTI DATA DARI DATABASE MASUK DI SINI -->
            <tr>
              <td colspan="12" style="text-align:center;color:#667085;">
                Belum ada data (menunggu database)
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="hint">
      </div>
    </div>
  </div>
</body>
</html>