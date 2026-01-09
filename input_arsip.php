<?php

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Input Arsip</title>
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
    a.btn, button.btn{
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
    a.btn:hover, button.btn:hover{ transform: translateY(-1px); opacity:.95; }
    a.btn.secondary, button.btn.secondary{
      background:#eef2ff;
      color:#1f2a44;
      border: 1px solid #d7ddff;
    }

    .grid{
      display:grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 14px;
    }
    .form-group{ display:flex; flex-direction:column; gap:6px; }
    label{ font-size: 13px; color:#1f2a44; font-weight:700; }
    input, select, textarea{
      width:100%;
      border: 1.8px solid #ddd;
      border-radius: 12px;
      padding: 10px 12px;
      font-size: 14px;
      outline:none;
    }
    textarea{ min-height: 90px; resize: vertical; }
    .span-2{ grid-column: span 2; }
    .span-3{ grid-column: span 3; }

    .actions{
      display:flex;
      gap:10px;
      justify-content:flex-end;
      margin-top: 12px;
      flex-wrap:wrap;
    }

    @media (max-width: 900px){
      .grid{ grid-template-columns: 1fr; }
      .span-2,.span-3{ grid-column: span 1; }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="header">
        <div>
          <h1>Form Input Arsip</h1>
          <div class="sub">Form ini belum nyambung database — nanti tinggal bikin INSERT ✨</div>
        </div>
        <div class="btns">
          <a class="btn secondary" href="tabel_arsip.php">📋 Ke Tabel Arsip</a>
          <a class="btn" href="index.php">⬅️ Balik ke Sortir Dokumen</a>
        </div>
      </div>

      <form method="POST" action="#">
        <div class="grid">
          <div class="form-group">
            <label>NO BERKAS *</label>
            <input name="no_berkas" placeholder="contoh: 1" required />
          </div>
          <div class="form-group">
            <label>KODE KLASIFIKASI *</label>
            <input name="kode_klasifikasi" placeholder="contoh: HM.002" required />
          </div>
          <div class="form-group">
            <label>NAMA BERKAS *</label>
            <input name="nama_berkas" placeholder="contoh: Informasi Meteorologi Publik" required />
          </div>

          <div class="form-group">
            <label>NO. ISI BERKAS</label>
            <input name="no_isi_berkas" placeholder="contoh: 1" />
          </div>
          <div class="form-group span-2">
            <label>PENCIPTA ARSIP</label>
            <input name="pencipta_arsip" placeholder="contoh: BMKG Stasiun ... / instansi ..." />
          </div>

          <div class="form-group">
            <label>NO. SURAT</label>
            <input name="no_surat" placeholder="contoh: HM.002/001/XII/2018" />
          </div>
          <div class="form-group span-2">
            <label>TANGGAL SURAT / KURUN WAKTU</label>
            <input name="tanggal_surat" placeholder="contoh: 2019 / 2019-12-01 / 2018-2019" />
          </div>

          <div class="form-group span-3">
            <label>URAIAN INFORMASI</label>
            <textarea name="uraian_informasi" placeholder="jelasin singkat isi informasinya..."></textarea>
          </div>

          <div class="form-group">
            <label>JUMLAH</label>
            <input name="jumlah" placeholder="contoh: 3 lembar / 1 berkas" />
          </div>
          <div class="form-group">
            <label>TINGKAT PERKEMBANGAN</label>
            <select name="tingkat_perkembangan">
              <option value="">-- pilih --</option>
              <option>Asli</option>
              <option>Fotocopy</option>
              <option>Scan</option>
            </select>
          </div>
          <div class="form-group">
            <label>LOKASI SIMPAN</label>
            <input name="lokasi_simpan" placeholder="contoh: 1" />
          </div>

          <div class="form-group span-3">
            <label>KETERANGAN</label>
            <input name="keterangan" placeholder="contoh: Baik" />
          </div>
        </div>

        <div class="actions">
          <button class="btn" type="button"
            onclick="alert('Database belum disambungkan 😄\\nNanti tombol ini akan melakukan INSERT ke DB.');">
            Simpan ke Database (belum aktif)
          </button>
          <a class="btn secondary" href="input_arsip.php">Refresh</a>
        </div>
      </form>

    </div>
  </div>
</body>
</html>
