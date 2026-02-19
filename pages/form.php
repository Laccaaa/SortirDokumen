<?php
$dbhandle = require __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../auth/auth_check.php";

$id_surat = $_SESSION['old_id_surat'] ?? '';
$old_jenis = $_SESSION['old_jenis_surat'] ?? '';
$old_nomor = $_SESSION['old_nomor_surat'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sortir Dokumen</title>

<style>
:root{
  --bg-start: #f3f5f9;
  --bg-end: #e2e7f1;
/*  */
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

html, body{
  width:100%;
  height:100%;
  margin:0;
  overflow:hidden;
}

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

.wrap{
  width:100%;
  max-width:none;
  height:100%;
  position:relative;
  z-index:2;
  display:flex;
}

.layout{
  width:100%;
  height:100%;
  display:flex;
  gap:14px;
}

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
.side-sublink:hover{
  transform: translateY(-1px);
  border-color:#5a63ff;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.2);
}
.side-sublink.active{
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

.top{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:12px;
  flex-wrap:wrap;
  margin-bottom: 10px;
}

.titles h1{
  margin:0;
  font-size: 20px;
  letter-spacing:.2px;
  line-height: 1.15;
}
.titles p{
  margin:6px 0 0;
  color: var(--muted);
  font-size: 12px;
}

.actionsTop{
  display:flex;
  gap:10px;
  align-items:center;
  flex-wrap:wrap;
}

a.btn{
  display:inline-flex;
  gap:8px;
  align-items:center;
  padding:8px 12px;
  border-radius: 14px;
  text-decoration:none;
  font-weight:900;
  font-size: 12px;
  border: 1px solid transparent;
  white-space:nowrap;
}
a.btn.light{
  background: var(--btn2);
  color: #1f2a44;
  border-color: #d7ddff;
}
a.btn.dark{
  background:#334155;
  color:#ffffff;
  border:1px solid #334155;
}
a.btn.dark:hover{
  background:#475569;
  color:#ffffff;
}

.alert-error{
  background: #ffe6e6;
  color: #b30000;
  padding: 12px 14px;
  border-radius: 12px;
  margin-bottom: 12px;
  text-align: center;
  font-size: 13px;
  border: 1px solid #f5c2c2;
}

.form{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px 12px;
  margin-top: 10px;
}

.field{
  display:flex;
  flex-direction:column;
  gap:8px;
}

label{
  font-weight:900;
  color:#1f2a44;
  font-size: 12px;
  letter-spacing:.2px;
  text-transform: uppercase;
}
.label-hint{
  display:none;
  font-weight:600;
  font-size:11px;
  color:rgba(180, 83, 9, 0.65);
  text-transform:none;
  letter-spacing:0;
  margin-left:4px;
}
.label-hint.show{
  display:inline;
}
.required{ color: #ef4444; }

input, select{
  width:100%;
  padding: 10px 12px;
  border-radius: 12px;
  border: 1px solid var(--line);
  outline:none;
  font-size: 14px;
  background:#fff;
  color:#0f172a;
}

input.error, select.error{
  border-color: #ff4444;
  animation: shake 0.5s;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
  20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.full{ grid-column: 1 / -1; }

.row3{
  display:grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 10px 12px;
  width:100%;
}
.row3.full{ grid-column: 1 / -1; }

.file-label{
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
.file-label.error{
  border-color: #ff4444;
  background: #ffe6e6;
}
input[type="file"]{ display:none; }
.file-name{
  margin-top: 8px;
  color: green;
  display: none;
  font-size: 13px;
}
.file-help{
  margin-top: 6px;
  color: var(--muted);
  font-size: 12px;
}

.file-preview{
  width:100%;
  height: 220px;
  margin-top: 8px;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid #ddd;
  background: #f5f7fb;
}
.file-preview iframe,
.file-preview embed{
  width:100%;
  height:100%;
  border:none;
}
.file-preview img{
  width:100%;
  height:100%;
  object-fit: contain;
}

.bottomActions{
  display:flex;
  gap:10px;
  justify-content:space-between;
  margin-top: 12px;
  flex-wrap:wrap;
  padding-bottom: 4px;
}

button.primary{
  border:none;
  padding: 12px 18px;
  border-radius: 14px;
  background: #0f172a;
  color:#fff;
  font-weight:900;
  font-size: 14px;
  cursor:pointer;
  display:inline-flex;
  gap:10px;
  align-items:center;
}
button.secondary{
  border: 1px solid #d7ddff;
  padding: 12px 18px;
  border-radius: 14px;
  background: #eef2ff;
  color:#1f2a44;
  font-weight:900;
  font-size: 14px;
  cursor:pointer;
  display:inline-flex;
  gap:10px;
  align-items:center;
}
button.secondary:active{ transform: translateY(1px); }
button.primary:active{ transform: translateY(1px); }

@media (max-width: 980px){
  body{ padding: 10px; }
  .layout{ flex-direction:column; }
  .sidebar{
    width:100%;
    height:auto;
  }
  .shell{
    max-width: 100%;
    padding: 14px;
    border-radius: 20px;
  }
  .form{ grid-template-columns: 1fr; }
  .row3{ grid-template-columns: 1fr; }
  .actionsTop{ width:100%; }
  a.btn{ flex:1; justify-content:center; }
}

/* MODAL STYLES */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
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
    box-shadow: 0 20px 50px rgba(0,0,0,0.3);
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
    from { transform: scale(.85); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}

/* Mobile */
@media (max-width: 768px) {
  .file-preview {
    height: 220px;
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
              <a class="side-sublink" href="/SortirDokumen/pages/export_menu.php">📥 Export Sortir Dokumen</a>
              <a class="side-sublink" href="/SortirDokumen/pages/export_pemusnahan.php">🗑️ Export Dokumen Musnah</a>
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
              <path d="M15 6L9 12l6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
      <?php unset($_SESSION['error_nomor']); endif; ?>

      <form id="suratForm" name="suratForm" action="/SortirDokumen/proses.php" method="POST" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="id_s" value="<?= htmlspecialchars($id_surat) ?>">

        <div class="form">
          <div class="field">
            <label>Kode <span class="required">*</span></label>
            <input
              type="text"
              name="kode_klasifikasi"
              placeholder="ME.03.02"
            >
          </div>

          <div class="field">
            <label>Unit Pengolah</label>
            <input
              type="text"
              name="unit_pengolah"
              placeholder="Tata Usaha"
            >
          </div>

          <div class="field">
            <label>Nama Berkas <span class="required">*</span></label>
            <input
              type="text"
              name="nama_berkas"
              placeholder="Produk Data dan Informasi Radar Cuaca"
            >
          </div>

          <div class="field">
            <label>Nomor Isi</label>
            <input
              type="text"
              name="no_isi"
              placeholder="1"
            >
          </div>

          <div class="field">
            <label>Pencipta Arsip</label>
            <input
              type="text"
              name="pencipta"
              placeholder="Stasiun Meteorologi Kelas I"
            >
          </div>

          <div class="field">
            <label>Tujuan Surat</label>
            <input
              type="text"
              name="tujuan_surat"
              placeholder="Stasiun Meteorologi"
            >
          </div>

          <div class="field">
            <label>Nomor Surat <span class="required">*</span></label>
            <input
              type="text"
              name="nomor_surat"
              id="nomor_surat"
              value="<?= htmlspecialchars($old_nomor) ?>"
              placeholder="Contoh: ME.002/003/DI/XII/2016"
            >
          </div>

          <div class="field full">
            <label>Perihal</label>
            <input
              type="text"
              name="perihal"
              placeholder="Laporan Bulanan Radar Maritim"
            >
          </div>

          <div class="field full">
            <label>Uraian Informasi</label>
            <input
              type="text"
              name="uraian"
              placeholder="Surat dari Stasiun Meteorologi Kelas III ..."
            >
          </div>

          <div class="field">
            <label>Tanggal Surat / Kurun</label>
            <input
              type="date"
              name="tanggal_surat"
            >
          </div>

          <div class="field">
            <label>Jumlah</label>
            <input
              type="text"
              name="jumlah"
              placeholder="3 lembar"
            >
          </div>

          <div class="field">
            <label>Lokasi Simpan</label>
            <input
              type="text"
              name="lokasi"
              placeholder="Filling Kabinet"
            >
          </div>

          <div class="field">
            <label>Tingkat</label>
            <select name="tingkat">
              <option value="">-- pilih --</option>
              <option value="Penting">Penting</option>
              <option value="Biasa">Biasa</option>
              <option value="Rahasia">Rahasia</option>
            </select>
          </div>

          <div class="field">
            <label>Keterangan</label>
            <input
              type="text"
              name="keterangan"
              placeholder="Arsip"
            >
          </div>

          <div class="field">
            <label>SKKAD</label>
            <input
              type="text"
              name="skkad"
              placeholder="Arsip / Terbuka"
            >
          </div>

          <div class="field">
            <label>JRA Aktif (Tahun) <span class="label-hint" id="jraAktifHint">wajib berupa angka</span></label>
            <input
              type="text"
              name="jra_aktif"
              placeholder="1"
              inputmode="numeric"
              pattern="[0-9]+"
              required
              title="Wajib isi angka"
            >
          </div>

          <div class="field">
            <label>JRA Inaktif (Tahun) <span class="label-hint" id="jraInaktifHint">wajib berupa angka</span></label>
            <input
              type="text"
              name="jra_inaktif"
              placeholder="1"
              inputmode="numeric"
              pattern="[0-9]+"
              required
              title="Wajib isi angka"
            >
          </div>

          <div class="field full">
            <label>Nasib</label>
            <input
              type="text"
              name="nasib"
              placeholder="Musnah"
            >
          </div>

          <div class="field">
            <label>Jenis Surat <span class="required">*</span></label>
            <select name="jenis_surat" id="jenis_surat">
              <option value="">-- Pilih Jenis Surat --</option>
              <option value="masuk" <?= $old_jenis === 'masuk' ? 'selected' : '' ?>>Surat Masuk</option>
              <option value="keluar" <?= $old_jenis === 'keluar' ? 'selected' : '' ?>>Surat Keluar</option>
            </select>
          </div>

          <div class="field full">
            <label>Upload File Surat <span class="required">*</span></label>
            <label class="file-label" id="fileLabel">
              Klik untuk memilih file
              <input type="file" id="fileInput" name="fileInput" accept=".pdf,.jpg,.jpeg,.png">
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

<!-- MODAL VALIDASI ERROR -->
<div id="errorModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon error">⚠️</div>
        <h3>Perhatian!</h3>
        <p id="errorMessage"></p>
        <button onclick="closeErrorModal()">OK, Saya Mengerti</button>
    </div>
</div>

<!-- MODAL SUKSES -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon success">✔</div>
        <h3>Berhasil</h3>
        <p id="modalMessage"></p>
        <button onclick="closeModal()">OK</button>
    </div>
</div>

<!-- MODAL KONFIRMASI RESET -->
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
document.addEventListener("DOMContentLoaded", function () {

    const form        = document.getElementById("suratForm");
    const jenisSurat  = document.getElementById("jenis_surat");
    const nomor       = document.getElementById("nomor_surat");
    const fileInput   = document.getElementById("fileInput");
    const fileLabel   = document.getElementById("fileLabel");
    const fileName    = document.getElementById("fileName");
    const filePreview = document.getElementById("filePreview");
    const errorModal  = document.getElementById("errorModal");
    const errorMsg    = document.getElementById("errorMessage");
    const resetConfirmModal = document.getElementById("resetConfirmModal");
    const cancelReset = document.getElementById("cancelReset");
    const okReset = document.getElementById("okReset");
    const jraAktif    = form.querySelector('input[name="jra_aktif"]');
    const jraInaktif  = form.querySelector('input[name="jra_inaktif"]');
    const jraAktifHint = document.getElementById("jraAktifHint");
    const jraInaktifHint = document.getElementById("jraInaktifHint");

    /* ========= PREVIEW FILE ========= */
    fileInput.addEventListener("change", function () {
        filePreview.innerHTML = "";
        fileLabel.classList.remove("error");

        if (!this.files.length) return;

        const file = this.files[0];
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

    /* ========= RESET FORM ========= */
    const resetBtn = document.getElementById("resetFormBtn");
    resetBtn?.addEventListener("click", (e) => {
        e.preventDefault();
        resetConfirmModal?.classList.add("show");
        resetConfirmModal?.setAttribute("aria-hidden", "false");
        okReset?.focus();
    });

    window.confirmResetForm = function () {
        filePreview.innerHTML = "";
        fileName.innerText = "";
        fileName.style.display = "none";
        fileLabel.classList.remove("error");
        jenisSurat.classList.remove("error");
        nomor.classList.remove("error");
        jraAktif?.classList.remove("error");
        jraInaktif?.classList.remove("error");
        jraAktifHint?.classList.remove("show");
        jraInaktifHint?.classList.remove("show");
        form.reset();
        closeResetConfirmModal();
    };

    window.closeResetConfirmModal = function () {
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
            shell?.scrollTo({ top: 0, behavior: "smooth" });
        });
    });

    /* ========= VALIDASI FORM SUBMIT ========= */
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Reset semua error styling
        jenisSurat.classList.remove("error");
        nomor.classList.remove("error");
        fileLabel.classList.remove("error");
        jraAktif?.classList.remove("error");
        jraInaktif?.classList.remove("error");

        // Cek Jenis Surat
        if (!jenisSurat.value) {
            showErrorModal("Jenis Surat belum dipilih!");
            jenisSurat.classList.add("error");
            jenisSurat.focus();
            return;
        }

        // Cek Nomor Surat (kosong)
        if (!nomor.value.trim()) {
            showErrorModal("Nomor Surat belum diisi!");
            nomor.classList.add("error");
            nomor.focus();
            return;
        }

        // Cek Format Nomor Surat (Dinamis - hanya validasi bulan romawi & tahun)
        const regex = /^(?:[a-zA-Z0-9.]+\/)?[^\/]+\/[^\/]+\/[^\/]+\/(I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII)\/\d{4}$/;
        if (!regex.test(nomor.value.trim())) {
            showErrorModal("Format Nomor Surat tidak valid!<br><small>Contoh: ME.002/003/DI/XII/2016 atau e.B/PL.01.00/001/KSUB/V/2024</small>");
            nomor.classList.add("error");
            nomor.focus();
            return;
        }

        const jraAktifValue = (jraAktif?.value || "").trim();
        const jraInaktifValue = (jraInaktif?.value || "").trim();

        if (!jraAktifValue || !/^\d+$/.test(jraAktifValue)) {
            showErrorModal("JRA Aktif wajib diisi angka saja!");
            jraAktif?.classList.add("error");
            jraAktif?.focus();
            return;
        }

        if (!jraInaktifValue || !/^\d+$/.test(jraInaktifValue)) {
            showErrorModal("JRA Inaktif wajib diisi angka saja!");
            jraInaktif?.classList.add("error");
            jraInaktif?.focus();
            return;
        }

        // Cek File Upload
        if (!fileInput.files.length) {
            showErrorModal("File surat belum dipilih!");
            fileLabel.classList.add("error");
            fileInput.focus();
            return;
        }

        // Semua validasi OK -> submit form
        form.submit();
    });

    [jraAktif, jraInaktif].forEach((field) => {
        field?.addEventListener("input", function () {
            const rawValue = this.value;
            const cleanValue = rawValue.replace(/\D/g, "");
            const hasInvalidChar = rawValue !== cleanValue;
            this.value = cleanValue;

            if (this === jraAktif) {
                jraAktifHint?.classList.toggle("show", hasInvalidChar);
            }
            if (this === jraInaktif) {
                jraInaktifHint?.classList.toggle("show", hasInvalidChar);
            }
        });
    });

    /* ========= FUNGSI SHOW ERROR MODAL ========= */
    function showErrorModal(message) {
        errorMsg.innerHTML = message;
        errorModal.classList.add("show");
    }

    /* ========= TAMPILKAN MODAL SUKSES ========= */
    <?php if (isset($_SESSION['status']) && $_SESSION['status'] === 'success'): ?>
        const modal = document.getElementById("successModal");
        const msg   = document.getElementById("modalMessage");

        msg.innerText = "<?= addslashes($_SESSION['pesan']); ?>";
        modal.classList.add("show");
        setTimeout(() => {
            closeModal();
        }, 2000);
    <?php unset($_SESSION['status'], $_SESSION['pesan']); endif; ?>

    /* ========= TAMPILKAN MODAL ERROR (SERVER) ========= */
    <?php if (isset($_SESSION['status']) && $_SESSION['status'] === 'error'): ?>
        showErrorModal("<?= addslashes($_SESSION['pesan']); ?>");
    <?php unset($_SESSION['status'], $_SESSION['pesan']); endif; ?>

    /* ========= ENTER TO NEXT FIELD ========= */
    const getFields = () => Array.from(
        form.querySelectorAll("input, select, textarea, button")
    ).filter(el => {
        if (el.type === "hidden") return false;
        if (el.type === "file") return false;
        if (el.type === "reset") return false;
        if (el.disabled) return false;
        return true;
    });

    form.addEventListener("keydown", function (e) {
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

    document.addEventListener("keydown", function (e) {
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

});

/* ========= FUNGSI GLOBAL ========= */
function closeErrorModal() {
    document.getElementById("errorModal").classList.remove("show");
}

function closeModal() {
    document.getElementById("successModal").classList.remove("show");
}
</script>

</body>
</html>
