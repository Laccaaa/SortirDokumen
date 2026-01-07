<?php
include "koneksi.php";
$jenis_surat='';
$id_surat='';
$nomor_surat='';


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


/* JUDUL */
h1 {
    text-align: center;
    margin-bottom: 22px;
    color: #2f3a5f;
    font-size: 22px;
}

/* FORM */
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

/* FILE */
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

/* BUTTON */
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
</style>
</head>

<body>

<div class="container">
    <h1>SORTIR DOKUMEN</h1>
    <form name="suratForm" action="proses.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" value="<?= $id_surat ?>" name="id_s"></input>
        <div class="form-group">
            <label>Jenis Surat <span class="required">*</span></label>
            <select name="jenis_surat" id="jenis_surat" class="form-select" required>
                <option value="">-- Pilih Jenis Surat --</option>
                <option <?php if($jenis_surat == 'masuk'){echo "selected";} ?> value="masuk">Surat Masuk</option>
                <option <?php if($jenis_surat == 'keluar'){echo "selected";} ?> value="keluar">Surat Keluar</option>
            </select>
        </div>

        <div class="form-group">
            <label>Nomor Surat <span class="required">*</span></label>
            <input type="text" name="nomor_surat" id="nomor_surat" placeholder="Contoh: 001/SM/XII/2024" required>
        </div>

        <div class="form-group">
            <label>Upload File Surat <span class="required">*</span></label>
            <label class="file-label">
                Klik untuk memilih file
                <input type="file" id="fileInput" name="fileInput" required>
            </label>
            <div class="file-name" id="fileName" name="fileName"></div>
            <div class="file-preview" id="filePreview" style="margin-top:10px;"></div>
        </div>

        <button type="submit">Submit</button>
    </form>
</div>

<script>
const fileInput = document.getElementById("fileInput");
const fileName = document.getElementById("fileName");

fileInput.addEventListener("change", function () {
    if (this.files.length > 0) {
        const file = this.files[0];
        fileName.textContent = "File dipilih: " + file.name;
        fileName.style.display = "block";

        // Clear preview sebelumnya
        filePreview.innerHTML = "";

        // Tipe file image
        if (file.type.startsWith("image/")) {
            const img = document.createElement("img");
            img.src = URL.createObjectURL(file);
            img.style.maxWidth = "200px";
            img.style.maxHeight = "200px";
            img.style.marginTop = "8px";
            filePreview.appendChild(img);
        }
        // Tipe file PDF
        else if (file.type === "application/pdf") {
            const embed = document.createElement("embed");
            embed.src = URL.createObjectURL(file);
            embed.type = "application/pdf";
            embed.style.width = "100%";
            embed.style.height = "400px";
            filePreview.appendChild(embed);
        }
        // Lainnya (misal docx, xlsx) hanya tampil nama file
        else {
            const p = document.createElement("p");
            p.textContent = "Preview tidak tersedia untuk tipe file ini.";
            filePreview.appendChild(p);
        }
    }
});

</script>

</body>
</html>