<?php
session_start();
include "koneksi.php";

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
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    min-height: 100vh;
    background: linear-gradient(135deg, #4a6cf7, #6fb1c8);
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
}

.container {
     width: 80vw;
    max-width: 900px;
    min-height: auto;
    background: white;
    border-radius: 22px;
    padding: 40px 50px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
}

h1 {
    text-align: center;
    margin-bottom: 22px;
    color: #2f3a5f;
    font-size: 22px;
}

.form-group {
    margin-bottom: 16px;
}

label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
    font-size: 14px;
}

.required {
    color: red;
}

input,
select {
    width: 100%;
    height: 44px;
    padding: 0 14px;
    font-size: 14px;
    border-radius: 10px;
    border: 1.8px solid #ddd;
}

.file-label {
    display: block;
    text-align: center;
    padding: 18px;
    border: 2px dashed #4a6cf7;
    border-radius: 12px;
    cursor: pointer;
    background: #f6f8ff;
    font-size: 14px;
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

button {
    width: 100%;
    height: 46px;
    font-size: 14px;
    background: #4a6cf7;
    color: white;
    border: none;
    border-radius: 12px;
    cursor: pointer;
}

.file-preview {
    width: 100%;
    height: 280px;
    aspect-ratio: 16 / 9;
    margin-top: 15px;
    margin-bottom: 20px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #ddd;
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

/* Mobile */
@media (max-width: 768px) {
    .file-preview {
        aspect-ratio: 3 / 4;
    }
}

.alert-error {
    background: #ffe6e6;
    color: #b30000;
    padding: 14px;
    border-radius: 12px;
    margin-bottom: 18px;
    text-align: center;
    font-size: 14px;
    border: 1px solid #f5c2c2;
}

.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

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

.modal-icon {
    font-size: 48px;
    color: #2ecc71;
    margin-bottom: 10px;
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

.modal-content h3 {
    margin-bottom: 8px;
    color: #2f3a5f;
}

.modal-content p {
    font-size: 14px;
    margin-bottom: 18px;
}

.modal-content button {
    background: #4a6cf7;
    border: none;
    color: white;
    padding: 10px 22px;
    border-radius: 10px;
    cursor: pointer;
}

@keyframes scaleIn {
    from { transform: scale(.85); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}
</style>
</head>

<body>
<div class="container">
    <?php if (isset($_SESSION['error_nomor'])): ?>
    <div class="alert-error">
        ❌ <?= htmlspecialchars($_SESSION['error_nomor']); ?><br>
        <small>Contoh benar: IJ.00.00/123/IT/VI/2024</small>
    </div>
<?php unset($_SESSION['error_nomor']); endif; ?>
<h1>SORTIR DOKUMEN</h1>

<form id="suratForm" name="suratForm" action="proses.php" method="POST" enctype="multipart/form-data">

    <input type="hidden" name="id_s" value="<?= htmlspecialchars($id_surat) ?>">

    <div class="form-group">
        <label>Jenis Surat <span class="required">*</span></label>
        <select name="jenis_surat" id="jenis_surat" required>
            <option value="">-- Pilih Jenis Surat --</option>
            <option value="masuk" <?= $old_jenis === 'masuk' ? 'selected' : '' ?>>Surat Masuk</option>
            <option value="keluar" <?= $old_jenis === 'keluar' ? 'selected' : '' ?>>Surat Keluar</option>
        </select>
    </div>

    <div class="form-group">
        <label>Nomor Surat <span class="required">*</span></label>
        <input type="text"
               name="nomor_surat"
               id="nomor_surat"
               value="<?= htmlspecialchars($old_nomor) ?>"
               placeholder="Contoh: 001/SM/XII/2024"
               required>
    </div>

    <div class="form-group">
        <label>Upload File Surat <span class="required">*</span></label>
        <label class="file-label">
            Klik untuk memilih file
            <input type="file" id="fileInput" name="fileInput" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
        </label>
        <div class="file-name" id="fileName"></div>
    </div>

    <div class="file-preview" id="filePreview" style="margin-top:10px;"></div>
    
    <button type="submit">SIMPAN</button>

</form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const form        = document.getElementById("suratForm");
    const nomor       = document.getElementById("nomor_surat");
    const fileInput   = document.getElementById("fileInput");
    const fileName    = document.getElementById("fileName");
    const filePreview = document.getElementById("filePreview");

    /* ========= PREVIEW FILE ========= */
    fileInput.addEventListener("change", function () {
        filePreview.innerHTML = "";

        if (!this.files.length) return;

        const file = this.files[0];
        fileName.style.display = "block";
        fileName.innerText = "File dipilih: " + file.name;

        const url = URL.createObjectURL(file);

        if (file.type === "application/pdf") {
            filePreview.innerHTML = `<iframe src="${url}"></iframe>`;
        } else if (file.type.startsWith("image/")) {
            filePreview.innerHTML = `<img src="${url}">`;
        }
    });

    /* ========= VALIDASI NOMOR SURAT ========= */
    form.addEventListener("submit", function (e) {
        const regex = /^(?:[a-zA-Z]+(?:\.[a-zA-Z]+)+\/)?[A-Z]{2,5}\.[0-9]{2}\.[0-9]{2}\/[0-9]{3}\/[A-Z]{2,10}\/(I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII)\/\d{4}$/;

        if (!regex.test(nomor.value.trim())) {
            e.preventDefault();
            alert("❌ Format nomor surat tidak valid");
            nomor.focus();
            nomor.style.border = "2px solid red";
        }
    });

    /* ========= TAMPILKAN MODAL SUKSES ========= */
    <?php if (isset($_SESSION['status']) && $_SESSION['status'] === 'success'): ?>
        const modal = document.getElementById("successModal");
        const msg   = document.getElementById("modalMessage");

        msg.innerText = "<?= addslashes($_SESSION['pesan']); ?>";
        modal.classList.add("show");
    <?php unset($_SESSION['status'], $_SESSION['pesan']); endif; ?>

});

/* ========= FUNGSI GLOBAL ========= */
function closeModal() {
    document.getElementById("successModal").classList.remove("show");
}
</script>

    <!-- MODAL SUKSES -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-icon">✔</div>
            <h3>Berhasil</h3>
            <p id="modalMessage"></p>
            <button onclick="closeModal()">OK</button>
        </div>
    </div>
</body>
</html>
