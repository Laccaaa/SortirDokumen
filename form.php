<?php
session_start();
require_once "koneksi.php";

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
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.container {
    width: 80vw;
    max-width: 900px;
    min-height: auto;
    background: white;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
}

.header {
    background: linear-gradient(135deg, #4a6cf7, #6fb1c8);
    color: white;
    padding: 25px 35px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h1 {
    font-size: 22px;
    font-weight: 600;
    margin: 0;
}

.btn-home {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 8px 18px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-home:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.form-container {
    padding: 40px 50px;
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
    transition: border-color 0.3s ease;
}

input.error,
select.error {
    border-color: #ff4444;
    animation: shake 0.5s;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
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

button[type="submit"] {
    width: 100%;
    height: 46px;
    font-size: 14px;
    background: #4a6cf7;
    color: white;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
}

button[type="submit"]:hover {
    background: #3a5ce7;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 108, 247, 0.3);
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

@keyframes scaleIn {
    from { transform: scale(.85); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}

/* Mobile */
@media (max-width: 768px) {
    .container {
        width: 95vw;
    }

    .header {
        padding: 20px;
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
    }

    .header h1 {
        font-size: 20px;
    }

    .btn-home {
        width: 100%;
        justify-content: center;
    }

    .form-container {
        padding: 30px 25px;
    }

    .file-preview {
        aspect-ratio: 3 / 4;
    }
}
</style>
</head>

<body>
<div class="container">
    <div class="header">
        <h1>📑 Sortir Dokumen</h1>
        <a href="homepage.php" class="btn-home">
            <span>🏠</span>
            <span>Kembali ke Menu</span>
        </a>
    </div>

    <div class="form-container">
        <?php if (isset($_SESSION['error_nomor'])): ?>
        <div class="alert-error">
            ❌ <?= htmlspecialchars($_SESSION['error_nomor']); ?><br>
            <small>Contoh benar: ME.002/003/DI/XII/2016 atau e.B/PL.01.00/001/KSUB/V/2024</small>
        </div>
        <?php unset($_SESSION['error_nomor']); endif; ?>

        <form id="suratForm" name="suratForm" action="proses.php" method="POST" enctype="multipart/form-data" novalidate>

            <input type="hidden" name="id_s" value="<?= htmlspecialchars($id_surat) ?>">

            <div class="form-group">
                <label>Jenis Surat <span class="required">*</span></label>
                <select name="jenis_surat" id="jenis_surat">
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
                       placeholder="Contoh: ME.002/003/DI/XII/2016">
            </div>

            <div class="form-group">
                <label>Upload File Surat <span class="required">*</span></label>
                <label class="file-label" id="fileLabel">
                    Klik untuk memilih file
                    <input type="file" id="fileInput" name="fileInput" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                </label>
                <div class="file-name" id="fileName"></div>
            </div>

            <div class="file-preview" id="filePreview" style="margin-top:10px;"></div>
            
            <button type="submit">SIMPAN</button>

        </form>
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

    /* ========= PREVIEW FILE ========= */
    fileInput.addEventListener("change", function () {
        filePreview.innerHTML = "";
        fileLabel.classList.remove("error");

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

    /* ========= VALIDASI FORM SUBMIT ========= */
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Reset semua error styling
        jenisSurat.classList.remove("error");
        nomor.classList.remove("error");
        fileLabel.classList.remove("error");

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
    <?php unset($_SESSION['status'], $_SESSION['pesan']); endif; ?>

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