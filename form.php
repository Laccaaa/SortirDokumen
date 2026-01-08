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
    max-width: 30000px;
    height: 1000px;
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
    aspect-ratio: 16 / 9;
    margin-top: 15px;
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

</style>
</head>

<body>
<div class="container">
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

        /* ========= PREVIEW FILE (PDF & IMAGE) ========= */
        fileInput.addEventListener("change", function () {
        filePreview.innerHTML = "";

        if (!this.files || this.files.length === 0) {
            fileName.style.display = "none";
            return;
        }

        const file = this.files[0];
        fileName.style.display = "block";
        fileName.innerText = "File dipilih: " + file.name;

        const fileURL = URL.createObjectURL(file);

        if (file.type === "application/pdf") {
            const iframe = document.createElement("iframe");
            iframe.src = fileURL;
            iframe.style.width = "100%";
            iframe.style.height = "100%";
            iframe.style.border = "none";
            filePreview.appendChild(iframe);
        }
        else if (file.type.startsWith("image/")) {
            const img = document.createElement("img");
            img.src = fileURL;
            img.style.width = "100%";
            img.style.height = "100%";
            img.style.objectFit = "contain";
            filePreview.appendChild(img);
        }
        else {
            filePreview.innerHTML =
                "<p style='color:#999;text-align:center;margin-top:20px'>Preview tidak tersedia</p>";
        }
    });

    /* ========= VALIDASI NOMOR SURAT ========= */
    form.addEventListener("submit", function (e) {
        const regex = /^[A-Za-z0-9.\/]+\/(I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII)\/\d{4}$/;

        nomor.style.border = "1.8px solid #ddd";

        if (!regex.test(nomor.value.trim())) {
            e.preventDefault();

            alert(
                "❌ Format nomor surat tidak valid\n\n"
            );

            nomor.focus();
            nomor.style.border = "2px solid red";
        }
    });

});
</script>

</body>
</html>
