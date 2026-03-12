<?php
$dbhandle = require __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../auth/auth_check.php";

$id_surat = $_SESSION['old_id_surat'] ?? '';
$old_nomor_berkas = $_SESSION['old_nomor_berkas'] ?? '';
$old_jenis = $_SESSION['old_jenis_surat'] ?? '';
$old_nomor = $_SESSION['old_nomor_surat'] ?? '';
$old_kode = $_SESSION['old_kode_klasifikasi'] ?? '';
$old_unit_pengolah = $_SESSION['old_unit_pengolah'] ?? '';
$old_nama_berkas = $_SESSION['old_nama_berkas'] ?? '';
$old_no_isi = $_SESSION['old_no_isi'] ?? '';
$old_pencipta = $_SESSION['old_pencipta'] ?? '';
$old_tujuan_surat = $_SESSION['old_tujuan_surat'] ?? '';
$old_nomor_type = $_SESSION['old_nomor_surat_type'] ?? '';
$old_nomor = $_SESSION['old_nomor_surat'] ?? '';
$old_others_kode = $_SESSION['old_others_kode'] ?? '';
$old_others_bulan = $_SESSION['old_others_bulan'] ?? '';
$old_others_tahun = $_SESSION['old_others_tahun'] ?? '';
$old_perihal = $_SESSION['old_perihal'] ?? '';
$old_uraian = $_SESSION['old_uraian'] ?? '';
$old_tanggal_surat = $_SESSION['old_tanggal_surat'] ?? '';
$old_jumlah = $_SESSION['old_jumlah'] ?? '';
$old_lokasi = $_SESSION['old_lokasi'] ?? '';
$old_tingkat = $_SESSION['old_tingkat'] ?? '';
$old_keterangan = $_SESSION['old_keterangan'] ?? '';
$old_skkad = $_SESSION['old_skkad'] ?? '';
$old_jra_aktif = $_SESSION['old_jra_aktif'] ?? '';
$old_jra_inaktif = $_SESSION['old_jra_inaktif'] ?? '';
$old_nasib = $_SESSION['old_nasib'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sortir Dokumen</title>

  <style>
    :root {
      --bg-start: #f3f5f9;
      --bg-end: #e2e7f1;
      /*  */
      --card: rgba(255, 255, 255, .96);
      --text: #0f172a;
      --muted: #64748b;
      --line: #e5e7eb;
      --shadow: 0 22px 70px rgba(0, 0, 0, .18);
      --radius: 24px;

      --btn: #0f172a;
      --btn2: #eef2ff;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html,
    body {
      width: 100%;
      height: 100%;
      margin: 0;
      overflow: hidden;
    }

    body {
      height: 100vh;
      background: linear-gradient(135deg, var(--bg-start), var(--bg-end));
      position: relative;
      font-family: Inter, Arial, sans-serif;
      padding: 14px;
      display: flex;
      align-items: stretch;
      justify-content: center;
    }

    .wrap {
      width: 100%;
      max-width: none;
      height: 100%;
      position: relative;
      z-index: 2;
      display: flex;
    }

    .layout {
      width: 100%;
      height: 100%;
      display: flex;
      gap: 14px;
    }

    .sidebar {
      width: 340px;
      height: 100%;
      background: #1f2430;
      border: 1px solid #2b3242;
      border-radius: 20px;
      box-shadow: 0 16px 40px rgba(0, 0, 0, .18);
      padding: 14px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      overflow: auto;
    }

    .side-title {
      font-weight: 900;
      font-size: 14px;
      letter-spacing: .3px;
      color: #e2e8f0;
      text-transform: uppercase;
    }

    .side-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .side-link {
      display: flex;
      gap: 10px;
      align-items: center;
      padding: 10px 12px;
      border-radius: 14px;
      text-decoration: none;
      color: #e2e8f0;
      background: #232a38;
      border: 1px solid #2f3747;
      transition: all .15s ease;
    }

    .side-link:hover {
      transform: translateY(-1px);
      border-color: #5a63ff;
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.2);
    }

    .side-link.active {
      background: #2a3350;
      border-color: #5a63ff;
      box-shadow: inset 0 0 0 1px rgba(90, 99, 255, .25);
    }

    .side-accordion {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .side-accordion summary {
      list-style: none;
    }

    .side-accordion summary::-webkit-details-marker {
      display: none;
    }

    .side-sub {
      display: flex;
      flex-direction: column;
      gap: 8px;
      padding-left: 12px;
    }

    .side-sublink {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 10px;
      border-radius: 12px;
      text-decoration: none;
      color: #e2e8f0;
      background: #232a38;
      border: 1px solid #2f3747;
      font-size: 12px;
      transition: all .15s ease;
    }

    .side-sublink:hover {
      transform: translateY(-1px);
      border-color: #5a63ff;
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.2);
    }

    .side-sublink.active {
      background: #2a3350;
      border-color: #5a63ff;
      box-shadow: inset 0 0 0 1px rgba(90, 99, 255, .25);
    }

    .side-icon {
      width: 36px;
      height: 36px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      background: rgba(90, 99, 255, .18);
      border: 1px solid rgba(90, 99, 255, .25);
      font-size: 18px;
      flex: 0 0 auto;
    }

    .side-text {
      display: flex;
      flex-direction: column;
      gap: 2px;
      min-width: 0;
    }

    .side-text strong {
      font-size: 14px;
      line-height: 1.2;
    }

    .side-text span {
      font-size: 12px;
      color: #94a3b8;
      line-height: 1.25;
    }

    .shell {
      width: 100%;
      height: 100%;
      max-width: none;
      flex: 1 1 auto;
      background: var(--card);
      border: 1px solid rgba(255, 255, 255, .55);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 12px 12px 10px;
      overflow: auto;
      overflow-x: hidden;
      -webkit-overflow-scrolling: touch;
      backdrop-filter: blur(10px);
    }

    .top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 10px;
    }

    .titles h1 {
      margin: 0;
      font-size: 20px;
      letter-spacing: .2px;
      line-height: 1.15;
    }

    .titles p {
      margin: 6px 0 0;
      color: var(--muted);
      font-size: 12px;
    }

    .actionsTop {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
    }

    a.btn {
      display: inline-flex;
      gap: 8px;
      align-items: center;
      padding: 8px 12px;
      border-radius: 14px;
      text-decoration: none;
      font-weight: 900;
      font-size: 12px;
      border: 1px solid transparent;
      white-space: nowrap;
    }

    a.btn.light {
      background: var(--btn2);
      color: #1f2a44;
      border-color: #d7ddff;
    }

    a.btn.dark {
      background: #334155;
      color: #ffffff;
      border: 1px solid #334155;
    }

    a.btn.dark:hover {
      background: #475569;
      color: #ffffff;
    }

    .alert-error {
      background: #ffe6e6;
      color: #b30000;
      padding: 12px 14px;
      border-radius: 12px;
      margin-bottom: 12px;
      text-align: center;
      font-size: 13px;
      border: 1px solid #f5c2c2;
    }

    .form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px 12px;
      margin-top: 10px;
    }

    .field {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    label {
      font-weight: 900;
      color: #1f2a44;
      font-size: 12px;
      letter-spacing: .2px;
      text-transform: uppercase;
    }

    .label-hint {
      display: none;
      font-weight: 600;
      font-size: 11px;
      color: rgba(180, 83, 9, 0.65);
      text-transform: none;
      letter-spacing: 0;
      margin-left: 4px;
    }

    .label-hint.show {
      display: inline;
    }

    .required {
      color: #ef4444;
    }

    input,
    select {
      width: 100%;
      padding: 10px 12px;
      border-radius: 12px;
      border: 1.5px solid #d7dee9;
      outline: none;
      font-size: 14px;
      background: #fff;
      color: #0f172a;
    }

    input.error,
    select.error {
      border-color: #ff4444;
      animation: shake 0.5s;
    }

    @keyframes shake {

      0%,
      100% {
        transform: translateX(0);
      }

      10%,
      30%,
      50%,
      70%,
      90% {
        transform: translateX(-5px);
      }

      20%,
      40%,
      60%,
      80% {
        transform: translateX(5px);
      }
    }

    .full {
      grid-column: 1 / -1;
    }

    .row3 {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 10px 12px;
      width: 100%;
    }

    .row3.full {
      grid-column: 1 / -1;
    }

    .file-label {
      display: block;
      text-align: center;
      padding: 16px;
      border: 2px dashed #4a6cf7;
      border-radius: 12px;
      cursor: pointer;
      background: #f6f8ff;
      font-size: 13px;
      transition: all 0.3s ease;
    }

    .file-label.error {
      border-color: #ff4444;
      background: #ffe6e6;
    }

    input[type="file"] {
      display: none;
    }

    .file-name {
      margin-top: 8px;
      color: green;
      display: none;
      font-size: 13px;
    }

    .file-help {
      margin-top: 6px;
      color: var(--muted);
      font-size: 12px;
    }

    .file-preview {
      width: 100%;
      height: 220px;
      margin-top: 8px;
      border-radius: 12px;
      overflow: hidden;
      border: 1.5px solid #d7dce3;
      background: #f5f7fb;
    }

    .file-preview iframe,
    .file-preview embed {
      width: 100%;
      height: 100%;
      border: none;
    }

    .file-preview img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .bottomActions {
      display: flex;
      gap: 10px;
      justify-content: space-between;
      margin-top: 12px;
      flex-wrap: wrap;
      padding-bottom: 4px;
    }

    button.primary {
      border: none;
      padding: 12px 18px;
      border-radius: 14px;
      background: #0f172a;
      color: #fff;
      font-weight: 900;
      font-size: 14px;
      cursor: pointer;
      display: inline-flex;
      gap: 10px;
      align-items: center;
    }

    button.secondary {
      border: 1px solid #d7ddff;
      padding: 12px 18px;
      border-radius: 14px;
      background: #eef2ff;
      color: #1f2a44;
      font-weight: 900;
      font-size: 14px;
      cursor: pointer;
      display: inline-flex;
      gap: 10px;
      align-items: center;
    }

    button.secondary:active {
      transform: translateY(1px);
    }

    button.primary:active {
      transform: translateY(1px);
    }

    @media (max-width: 980px) {
      body {
        padding: 10px;
      }

      .layout {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        height: auto;
      }

      .shell {
        max-width: 100%;
        padding: 14px;
        border-radius: 20px;
      }

      .form {
        grid-template-columns: 1fr;
      }

      .row3 {
        grid-template-columns: 1fr;
      }

      .actionsTop {
        width: 100%;
      }

      a.btn {
        flex: 1;
        justify-content: center;
      }
    }

    /* MODAL STYLES */
    .modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.45);
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    .modal.show {
      display: flex;
    }

    .modal-content {
      background: #ffffff;
      padding: 30px 35px;
      border-radius: 18px;
      text-align: center;
      width: 90%;
      max-width: 420px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
      animation: scaleIn .25s ease;
      position: relative;
    }

    .modal-icon {
      font-size: 48px;
      margin-bottom: 10px;
    }

    .modal-icon.success {
      color: #2ecc71;
    }

    .modal-icon.error {
      color: #ff4444;
    }

    .modal-icon.warning {
      color: #f59e0b;
    }

    .modal-content h3 {
      margin-bottom: 8px;
      color: #2f3a5f;
    }

    .modal-content p {
      font-size: 14px;
      margin-bottom: 18px;
      line-height: 1.6;
    }

    .modal-content button {
      background: #4a6cf7;
      border: none;
      color: white;
      padding: 10px 22px;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .modal-content button:hover {
      background: #3a5ce7;
      transform: translateY(-2px);
    }

    .modal-actions {
      display: flex;
      gap: 10px;
      justify-content: center;
    }

    .modal-actions .btn-cancel {
      background: #64748b;
    }

    .modal-actions .btn-cancel:hover {
      background: #475569;
    }

    @keyframes scaleIn {
      from {
        transform: scale(.85);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    /* Mobile */
    @media (max-width: 768px) {
      .file-preview {
        height: 220px;
      }
    }

    .row2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px 12px;
      width: 100%;
    }

    @media (max-width: 980px) {
      .row2 {
        grid-template-columns: 1fr;
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
            <a class="side-link active" href="/SortirDokumen/pages/form.php">
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

      <div class="shell">
        <div class="top">
          <div class="titles">
            <h1>Sortir Dokumen</h1>
            <p>Lengkapi informasi arsip untuk proses sortir dokumen.</p>
          </div>

          <div class="actionsTop">
            <a class="btn dark" href="homepage.php">
              <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">
                <path d="M15 6L9 12l6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              Kembali
            </a>
          </div>
        </div>

        <?php if (isset($_SESSION['error_nomor'])): ?>
          <div class="alert-error">
            ❌ <?= htmlspecialchars($_SESSION['error_nomor']); ?><br>
            <small>Contoh benar: ME.002/003/DI/XII/2016 atau e.B/PL.01.00/001/KSUB/V/2024</small>
          </div>
        <?php unset($_SESSION['error_nomor']);
        endif; ?>

        <form id="suratForm" name="suratForm" action="/SortirDokumen/proses.php" method="POST" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="id_s" value="<?= htmlspecialchars($id_surat) ?>">

          <div class="form">
            <div class="field">
              <label>Kode Klasifikasi<span class="required">*</span></label>
              <input
                type="text"
                name="kode_klasifikasi"
                placeholder="ME.03.02"
                value="<?= htmlspecialchars($old_kode) ?>"
                required>
            </div>

            <div class="field">
              <label>Unit Pengolah <span class="required">*</span></label>
              <input
                type="text"
                name="unit_pengolah"
                placeholder="Tata Usaha"
                required>
              </input>
            </div>

            <div class="field">
              <label>Nama Berkas <span class="required">*</span></label>
              <input
                type="text"
                name="nama_berkas"
                value="<?= htmlspecialchars($_SESSION['old_nama_berkas'] ?? '') ?>"
                placeholder="Produk Data dan Informasi Radar Cuaca"
                required>
            </div>

            <div class="field">
              <label>Nomor Berkas <span class="required">*</span></label>
              <input
                type="text"
                name="nomor_berkas"
                maxlength="50"
                value="<?= htmlspecialchars($old_nomor_berkas) ?>"
                required>
            </div>

            <div class="field">
              <label>Nomor Isi <span class="required">*</span></label>
              <input
                type="text"
                name="no_isi"
                placeholder="1"
                required>
            </div>

            <div class="field">
              <label>Pencipta Arsip <span class="required">*</span></label>
              <input
                type="text"
                name="pencipta"
                placeholder="Stasiun Meteorologi Kelas I"
                required>
            </div>

            <div class="field">
              <label>Tujuan Surat <span class="required">*</span></label>
              <input
                type="text"
                name="tujuan_surat"
                placeholder="Stasiun Meteorologi"
                required>
            </div>

            <div class="field">
              <label>Jenis Surat <span class="required">*</span></label>
              <select name="jenis_surat" id="jenis_surat" required>
                <option value="">-- Pilih Jenis Surat --</option>
                <option value="masuk" <?= $old_jenis === 'masuk' ? 'selected' : '' ?>>Surat Masuk</option>
                <option value="keluar" <?= $old_jenis === 'keluar' ? 'selected' : '' ?>>Surat Keluar</option>
              </select>
            </div>

            <!-- NOMOR SURAT (FULL 1 ROW) -->
            <div class="field full" id="nomor_surat_block">
              <label>Kategori Surat <span class="required">*</span></label>

              <select name="nomor_surat_type" id="nomor_surat_type" required>
                <option value="">-- pilih --</option>
                <option value="lokal">Lokal</option>
                <option value="others">Lainnya</option>
              </select>

              <!-- Nomor surat final buat backend -->
              <input type="hidden" name="nomor_surat" id="nomor_surat" value="<?= htmlspecialchars($old_nomor) ?>">

              <!-- muncul kalau pilih lokal -->
              <div id="nomor_surat_lokal_wrap" style="display:none; margin-top:10px;">
                <label style="margin-top:4px;">Isi Nomor Surat (Lokal) <span class="required">*</span></label>
                <input
                  type="text"
                  id="nomor_surat_lokal"
                  placeholder="Contoh: e.B/PL.01.00/001/KSUB/V/2019"
                  required>
              </div>

              <!-- muncul kalau pilih others -->
              <div id="nomor_surat_others_wrap" style="display:none; margin-top:10px;">
                <label style="margin-top:4px;">Nomor Surat (Lainnya) <span class="required">*</span></label>

                <div class="row3">
                  <input
                    type="text"
                    id="others_kode"
                    name="others_kode"
                    placeholder="Kode surat">

                  <select id="others_bulan" name="others_bulan">
                    <option value="">-- bulan --</option>
                    <option value="I">Januari</option>
                    <option value="II">Februari</option>
                    <option value="III">Maret</option>
                    <option value="IV">April</option>
                    <option value="V">Mei</option>
                    <option value="VI">Juni</option>
                    <option value="VII">Juli</option>
                    <option value="VIII">Agustus</option>
                    <option value="IX">September</option>
                    <option value="X">Oktober</option>
                    <option value="XI">November</option>
                    <option value="XII">Desember</option>
                  </select>

                  <input type="text" id="others_tahun" name="others_tahun" inputmode="numeric" placeholder="Tahun">
                </div>
              </div>
            </div>

            <div class="field">
              <label>Perihal <span class="required">*</span></label>
              <input
                type="text"
                name="perihal"
                placeholder="Laporan Bulanan Radar Maritim"
                required>
            </div>

            <div class="field full">
              <label>Uraian Informas <span class="required">*</span></label>
              <input
                type="text"
                name="uraian"
                placeholder="Surat dari Stasiun Meteorologi Kelas III ...">
            </div>

            <div class="field">
              <label>Tanggal Surat / Kurun <span class="required">*</span></label>
              <input
                type="date"
                name="tanggal_surat">
            </div>

            <div class="field">
              <label>Jumlah <span class="required">*</span></label>
              <input
                type="text"
                name="jumlah"
                maxlength="50"
                placeholder="3 lembar">
            </div>

            <div class="field">
              <label>Lokasi Simpan <span class="required">*</span></label>
              <input
                type="text"
                name="lokasi"
                maxlength="150"
                placeholder="Filling Kabinet">
            </div>

            <div class="field">
              <label>Tingkat <span class="required">*</span></label>
              <select name="tingkat">
                <option value="">-- pilih --</option>
                <option value="Penting">Penting</option>
                <option value="Biasa">Biasa</option>
                <option value="Rahasia">Rahasia</option>
              </select>
            </div>

            <div class="field">
              <label>Keterangan (Baik/Rusak) <span class="required">*</span></label>
              <select name="keterangan">
                <option value="">-- pilih --</option>
                <option value="Baik">Baik</option>
                <option value="Rusak">Rusak</option>
              </select>
            </div>

            <div class="field">
              <label>SKKAD <span class="required">*</span></label>
              <select name="skkad">
                <option value="">-- pilih --</option>
                <option value="Biasa" <?= $old_skkad === 'Biasa' ? 'selected' : '' ?>>Biasa</option>
                <option value="Terbuka" <?= $old_skkad === 'Terbuka' ? 'selected' : '' ?>>Terbuka</option>
                <option value="Rahasia" <?= $old_skkad === 'Rahasia' ? 'selected' : '' ?>>Rahasia</option>
              </select>
            </div>

            <div class="field">
              <label>JRA Aktif <span class="required">*</span></label>
              <input
                type="text"
                name="jra_aktif"
                placeholder="1 Tahun setelah tidak digunakan"
                maxlength="100"
                required
                title="Wajib diisi">
            </div>

            <div class="field">
              <label>JRA Inaktif <span class="required">*</span></label>
              <input
                type="text"
                name="jra_inaktif"
                placeholder="1 Tahun"
                maxlength="100"
                required
                title="Wajib diisi">
            </div>

            <div class="field">
              <label>Nasib akhir <span class="required">*</span></label>
              <input
                type="text"
                name="nasib"
                placeholder="Musnah">
            </div>

            <div class="field full">
              <label>Upload File Surat <span class="required">*</span></label>
              <label class="file-label" id="fileLabel">
                Klik untuk memilih file
                <input type="file" id="fileInput" name="fileInput" accept=".pdf,.jpg,.jpeg,.png" required>
              </label>
              <div class="file-help">Format yang didukung: PDF, JPG, JPEG, PNG</div>
              <div class="file-name" id="fileName"></div>
            </div>
          </div>

          <div class="file-preview" id="filePreview"></div>

          <div class="bottomActions">
            <button class="secondary" type="reset" id="resetFormBtn">🔄 Reset</button>
            <button class="primary" type="submit">💾 Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="errorModal" class="modal">
    <div class="modal-content">
      <div class="modal-icon error">⚠️</div>
      <h3>Perhatian!</h3>
      <p id="errorMessage"></p>
      <button onclick="closeErrorModal()">OK, Saya Mengerti</button>
    </div>
  </div>

  <div id="successModal" class="modal">
    <div class="modal-content">
      <div class="modal-icon success">✔</div>
      <h3>Berhasil</h3>
      <p id="modalMessage"></p>
      <button onclick="closeModal()">OK</button>
    </div>
  </div>

  <div id="resetConfirmModal" class="modal">
    <div class="modal-content">
      <div class="modal-icon warning">⚠</div>
      <h3>Konfirmasi Reset</h3>
      <p>Semua data yang sudah diisi akan dihapus. Lanjutkan reset?</p>
      <div class="modal-actions">
        <button type="button" class="btn-cancel" id="cancelReset">Batal</button>
        <button type="button" id="okReset">Ya, Reset</button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {

      const form = document.getElementById("suratForm");
      const jenisSurat = document.getElementById("jenis_surat");
      const nomor = document.getElementById("nomor_surat");
      const fileInput = document.getElementById("fileInput");
      const fileLabel = document.getElementById("fileLabel");
      const fileName = document.getElementById("fileName");
      const filePreview = document.getElementById("filePreview");
      const errorModal = document.getElementById("errorModal");
      const errorMsg = document.getElementById("errorMessage");
      const resetConfirmModal = document.getElementById("resetConfirmModal");
      const cancelReset = document.getElementById("cancelReset");
      const okReset = document.getElementById("okReset");
      const kodeKlasifikasi = form.querySelector('input[name="kode_klasifikasi"]');
      const namaBerkas = form.querySelector('input[name="nama_berkas"]');
      const jraAktif = form.querySelector('input[name="jra_aktif"]');
      const jraInaktif = form.querySelector('input[name="jra_inaktif"]');
      const jraAktifHint = document.getElementById("jraAktifHint");
      const jraInaktifHint = document.getElementById("jraInaktifHint");

      // preview file
      fileInput.addEventListener("change", function() {
        filePreview.innerHTML = "";
        fileLabel.classList.remove("error");

        if (!this.files.length) return;

        const file = this.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
          showErrorModal("Ukuran file maksimal 5MB!");
          this.value = "";
          fileName.innerText = "";
          fileName.style.display = "none";
          fileLabel.classList.add("error");
          filePreview.innerHTML = "";
          return;
        }

        const allowedExt = ["pdf", "jpg", "jpeg", "png"];
        const allowedMime = ["application/pdf", "image/jpeg", "image/png"];
        const ext = (file.name.split(".").pop() || "").toLowerCase();

        if (!allowedExt.includes(ext) || (file.type && !allowedMime.includes(file.type))) {
          showErrorModal("Format file tidak didukung! Gunakan PDF atau gambar (JPG, JPEG, PNG).");
          this.value = "";
          fileName.innerText = "";
          fileName.style.display = "none";
          fileLabel.classList.add("error");
          return;
        }

        fileName.style.display = "block";
        fileName.innerText = "File dipilih: " + file.name;

        const url = URL.createObjectURL(file);

        if (file.type === "application/pdf") {
          filePreview.innerHTML = `<iframe src="${url}"></iframe>`;
        } else if (file.type.startsWith("image/")) {
          filePreview.innerHTML = `<img src="${url}">`;
        }
      });

      //reset form
      const resetBtn = document.getElementById("resetFormBtn");
      resetBtn?.addEventListener("click", (e) => {
        e.preventDefault();
        resetConfirmModal?.classList.add("show");
        resetConfirmModal?.setAttribute("aria-hidden", "false");
        okReset?.focus();
      });

      window.confirmResetForm = function() {
        filePreview.innerHTML = "";
        fileName.innerText = "";
        fileName.style.display = "none";
        fileLabel.classList.remove("error");
        jenisSurat.classList.remove("error");
        kodeKlasifikasi?.classList.remove("error");
        namaBerkas?.classList.remove("error");
        nomor.classList.remove("error");
        jraAktif?.classList.remove("error");
        jraInaktif?.classList.remove("error");
        form.reset();
        closeResetConfirmModal();
      };

      window.closeResetConfirmModal = function() {
        resetConfirmModal?.classList.remove("show");
        resetConfirmModal?.setAttribute("aria-hidden", "true");
      };

      cancelReset?.addEventListener("click", closeResetConfirmModal);
      okReset?.addEventListener("click", confirmResetForm);
      resetConfirmModal?.addEventListener("click", (e) => {
        if (e.target === resetConfirmModal) closeResetConfirmModal();
      });

      form.addEventListener("reset", () => {
        const shell = document.querySelector(".shell");
        requestAnimationFrame(() => {
          shell?.scrollTo({
            top: 0,
            behavior: "smooth"
          });
        });
      });

      form.addEventListener("submit", function(e) {
        e.preventDefault();

        // Reset semua error styling
        jenisSurat.classList.remove("error");
        kodeKlasifikasi?.classList.remove("error");
        namaBerkas?.classList.remove("error");
        nomor.classList.remove("error");
        fileLabel.classList.remove("error");
        jraAktif?.classList.remove("error");
        jraInaktif?.classList.remove("error");

        const kodeValue = (kodeKlasifikasi?.value || "").trim();
        if (!kodeValue) {
          showErrorModal("Kode belum diisi!");
          kodeKlasifikasi?.classList.add("error");
          kodeKlasifikasi?.focus();
          return;
        }

        const namaBerkasValue = (namaBerkas?.value || "").trim();
        if (!namaBerkasValue) {
          showErrorModal("Nama Berkas belum diisi!");
          namaBerkas?.classList.add("error");
          namaBerkas?.focus();
          return;
        }

        // Cek Jenis Surat
        if (!jenisSurat.value) {
          showErrorModal("Jenis Surat belum dipilih!");
          jenisSurat.classList.add("error");
          jenisSurat.focus();
          return;
        }

        // Cek Nomor Surat (kosong)
        if (!nomorType.value) {
          showErrorModal("Pilih kategori surat dulu (Lokal / Lainnya).");
          nomorType.classList.add("error");
          nomorType.focus();
          return;
        }

        if (nomorType.value === "lokal") {
          const v = (inputLokal.value || "").trim();
          if (!v) {
            showErrorModal("Harap isi nomor surat dahulu (kategori Lokal).");
            inputLokal.classList.add("error");
            inputLokal.focus();
            return;
          }
        }

        if (nomorType.value === "others") {
          const k = (inputKodeOthers.value || "").trim();
          const b = (selBulan.value || "").trim();
          const y = (inputTahun.value || "").trim();

          if (!k) {
            showErrorModal("Harap isi kode surat dahulu (kategori Lainnya).");
            inputKodeOthers.classList.add("error");
            inputKodeOthers.focus();
            return;
          }

          if (!b || !y) {
            showErrorModal("Kategori Lainnya: lengkapi penempatan bulan dan tahun surat.");
            if (!b) selBulan.classList.add("error");
            if (!y) inputTahun.classList.add("error");
            (!b ? selBulan : inputTahun).focus();
            return;
          }

          // opsional: tahun wajib 4 digit
          if (!/^\d{4}$/.test(y)) {
            showErrorModal("Tahun harus 4 digit (misal: 2026).");
            inputTahun.classList.add("error");
            inputTahun.focus();
            return;
          }
        }

        // setelah validasi input, pastikan hidden nomor terisi
        toggleNomorSurat();
        const no = nomor.value.trim();
        if (!no) {
          showErrorModal("Nomor surat belum terbentuk. Periksa kembali input kategori.");
          return;
        }

        if (jenisSurat.value === "keluar") {
          // ketat: harus segment lengkap + bulan romawi + tahun
          const regexKeluar =
            /^(?:[a-zA-Z0-9.]+\/)?[^\/]+\/[^\/]+\/[^\/]+\/(I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII)\/\d{4}$/;

          if (!regexKeluar.test(no)) {
            showErrorModal(
              "Format Nomor Surat keluar tidak valid!<br><small>Contoh: ME.002/003/DI/XII/2016 atau e.B/PL.01.00/001/KSUB/V/2024</small>"
            );
            nomor.classList.add("error");
            nomor.focus();
            return;
          }
        } else if (jenisSurat.value === "masuk") {
          // fleksibel: minimal ada bulan romawi dan tahun agar bisa dipetakan (kalau tidak ada → backend masuk OTHER)
          const regexMasuk = /\b(I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII)\b.*\b(\d{4})\b/;

          if (!regexMasuk.test(no)) {
            showErrorModal(
              'Nomor surat masuk tidak mengandung BULAN ROMAWI & TAHUN (mis. "V 2024").<br><small>File akan masuk folder <b>OTHER</b>.</small>'
            );
          }
        }

        const jraAktifValue = (jraAktif?.value || "").trim();
        const jraInaktifValue = (jraInaktif?.value || "").trim();

        if (!jraAktifValue) {
          showErrorModal("JRA Aktif wajib diisi!");
          jraAktif?.classList.add("error");
          jraAktif?.focus();
          return;
        }

        if (!jraInaktifValue) {
          showErrorModal("JRA Inaktif wajib diisi!");
          jraInaktif?.classList.add("error");
          jraInaktif?.focus();
          return;
        }

        if (!fileInput.files.length) {
          showErrorModal("File surat belum dipilih!");
          fileLabel.classList.add("error");
          fileInput.focus();
          return;
        }

        const file = fileInput.files[0];
        const maxSize = 5 * 1024 * 1024;

        if (file.size > maxSize) {
          showErrorModal("Ukuran file maksimal 5MB!");
          fileLabel.classList.add("error");
          return;
        }

        form.submit();
      });

      // show error modal function
      function showErrorModal(message) {
        errorMsg.innerHTML = message;
        errorModal.classList.add("show");
      }

      // modal sukses
      <?php if (isset($_SESSION['status']) && $_SESSION['status'] === 'success'): ?>
        const modal = document.getElementById("successModal");
        const msg = document.getElementById("modalMessage");

        msg.innerText = "<?= addslashes($_SESSION['pesan']); ?>";
        modal.classList.add("show");
        setTimeout(() => {
          closeModal();
        }, 2000);
      <?php unset($_SESSION['status'], $_SESSION['pesan']);
      endif; ?>

      // modal eror dari server
      <?php if (isset($_SESSION['status']) && $_SESSION['status'] === 'error'): ?>
        showErrorModal("<?= addslashes($_SESSION['pesan']); ?>");
      <?php unset($_SESSION['status'], $_SESSION['pesan']);
      endif; ?>

      const getFields = () => Array.from(
        form.querySelectorAll("input, select, textarea, button")
      ).filter(el => {
        if (el.type === "hidden") return false;
        if (el.type === "file") return false;
        if (el.type === "reset") return false;
        if (el.disabled) return false;
        return true;
      });

      form.addEventListener("keydown", function(e) {
        if (e.key !== "Enter") return;
        if (resetConfirmModal?.classList.contains("show")) return;
        const target = e.target;
        if (!(target instanceof HTMLElement)) return;
        if (target.tagName === "TEXTAREA") return;

        const fields = getFields();
        const idx = fields.indexOf(target);
        if (idx === -1) return;

        e.preventDefault();
        const next = fields[idx + 1];
        if (next) {
          next.focus();
        } else {
          form.requestSubmit();
        }
      });

      document.addEventListener("keydown", function(e) {
        if (!resetConfirmModal?.classList.contains("show")) return;
        if (e.key === "Escape") {
          e.preventDefault();
          closeResetConfirmModal();
        }
        if (e.key === "Enter") {
          e.preventDefault();
          confirmResetForm();
        }
      });

      const nomorType = document.getElementById("nomor_surat_type");
      const wrapLokal = document.getElementById("nomor_surat_lokal_wrap");
      const inputLokal = document.getElementById("nomor_surat_lokal");
      const wrapOthers = document.getElementById("nomor_surat_others_wrap");
      const selBulan = document.getElementById("others_bulan");
      const inputTahun = document.getElementById("others_tahun");
      const inputKodeOthers = document.getElementById("others_kode");

      function toggleNomorSurat() {
        wrapLokal.style.display = "none";
        wrapOthers.style.display = "none";
        nomor.value = "";

        if (nomorType.value === "lokal") {
          wrapLokal.style.display = "block";
          const v = (inputLokal.value || "").trim();
          if (v) nomor.value = v;

        } else if (nomorType.value === "others") {
          wrapOthers.style.display = "block";

          const k = (inputKodeOthers.value || "").trim().replace(/\/+$/g, "");
          const b = (selBulan.value || "").trim();
          const y = (inputTahun.value || "").trim();

          // wajib lengkap: kode + bulan + tahun
          if (k && b && y) {
            nomor.value = `${k}/${b}/${y}`;
          } else {
            nomor.value = "";
          }
        }
      }

      nomorType.addEventListener("change", toggleNomorSurat);
      inputLokal.addEventListener("input", toggleNomorSurat);
      selBulan.addEventListener("change", toggleNomorSurat);
      inputTahun.addEventListener("input", toggleNomorSurat);
      inputKodeOthers.addEventListener("input", toggleNomorSurat);

      toggleNomorSurat();

      // if surat keluar = kunci lokal
      function syncNomorTypeByJenis() {
        if (jenisSurat.value === "keluar") {
          nomorType.value = "lokal";
          toggleNomorSurat();

          setTimeout(() => inputLokal?.focus(), 0);
        } else {
          nomorType.value = "";
          toggleNomorSurat();
        }
      }

      jenisSurat.addEventListener("change", syncNomorTypeByJenis);

      function lockNomorTypeToLokal(isLocked) {
        nomorType.disabled = isLocked;

        const optOthers = nomorType.querySelector('option[value="others"]');
        if (optOthers) optOthers.disabled = isLocked;

        if (isLocked) {
          nomorType.style.opacity = "0.8";
          nomorType.style.cursor = "not-allowed";
        } else {
          nomorType.style.opacity = "";
          nomorType.style.cursor = "";
        }
      }

      function syncNomorTypeByJenis() {
        if (jenisSurat.value === "keluar") {
          nomorType.value = "lokal";
          lockNomorTypeToLokal(true);
          toggleNomorSurat();
          setTimeout(() => inputLokal?.focus(), 0);
        } else {
          lockNomorTypeToLokal(false);
          nomorType.value = "";
          toggleNomorSurat();
        }
      }

      jenisSurat.addEventListener("change", syncNomorTypeByJenis);
      syncNomorTypeByJenis();
    });

    function closeErrorModal() {
      document.getElementById("errorModal").classList.remove("show");
    }

    function closeModal() {
      document.getElementById("successModal").classList.remove("show");
    }

    document.addEventListener("input", function(e) {
      const el = e.target;

      if (el.classList.contains("number-limit")) {
        el.value = el.value.replace(/\D/g, "");
        if (el.value.length > 10) {
          el.value = el.value.slice(0, 10);
        }
      }

      const limits = {
        jumlah: 50,
        lokasi: 150
      };

      const name = el.name;
      if (!limits[name]) return;

      const max = limits[name];
      if (el.value.length > max) {
        el.value = el.value.slice(0, max);
      }
    });
  </script>

</body>

</html>