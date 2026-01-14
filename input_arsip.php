<?php
session_start();

$error      = $_SESSION['error'] ?? '';
$success    = $_SESSION['success'] ?? '';
$old_input  = $_SESSION['old_input'] ?? [];
$errorField = $_SESSION['error_field'] ?? '';

if ($success) {
    unset($_SESSION['success'], $_SESSION['old_input']);
}
if ($error) {
    unset($_SESSION['error'], $_SESSION['error_field']);
    // old_input tetap ada untuk mengisi form
}

function old($field, $default = '')
{
    global $old_input;
    return $old_input[$field] ?? $default;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Arsip Dimusnahkan</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            padding: 24px;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #4a6cf7, #6fb1c8);
        }

        .wrap {
            max-width: 1180px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 22px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }

        h1 {
            font-size: 20px;
            color: #1f2a44;
            margin-bottom: 6px;
        }

        .sub {
            font-size: 13px;
            color: #667085;
        }

        .btns {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            background: #1f2a44;
            color: #fff;
            transition: transform .12s ease, opacity .12s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            opacity: .95;
        }

        .btn.secondary {
            background: #eef2ff;
            color: #1f2a44;
            border: 1px solid #d7ddff;
        }

        /* ===== FORM ===== */
        .grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        label {
            font-size: 13px;
            font-weight: 700;
            color: #1f2a44;
        }

        label .required {
            color: #e53e3e;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border-radius: 12px;
            border: 1.8px solid #ddd;
            outline: none;
            transition: border-color .15s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #4a6cf7;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        .span-2 { grid-column: span 2; }
        .span-3 { grid-column: span 3; }

        .actions {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ===== ALERT ===== */
        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .alert.error {
            background: #fff5f5;
            border: 1px solid #ffd0d0;
            color: #7a1f1f;
        }

        .alert.success {
            background: #f5fff5;
            border: 1px solid #d0ffd0;
            color: #1f7a1f;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .span-2,
            .span-3 {
                grid-column: span 1;
            }

            .header {
                flex-direction: column;
            }

            .btns {
                flex-direction: column;
            }
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-box {
            background: #fff;
            padding: 26px;
            border-radius: 18px;
            text-align: center;
            width: 100%;
            max-width: 360px;
            box-shadow: 0 20px 50px rgba(0,0,0,.25);
            animation: zoomIn .2s ease;
        }

        .modal-icon {
            font-size: 42px;
            margin-bottom: 10px;
        }

        .modal-box h3 {
            margin-bottom: 8px;
            color: #1f2a44;
        }

        .modal-box p {
            font-size: 14px;
            color: #555;
            margin-bottom: 16px;
        }

        .modal-box button {
            padding: 10px 18px;
            border-radius: 10px;
            border: none;
            background: #4a6cf7;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        @keyframes zoomIn {
            from { transform: scale(.9); opacity: 0 }
            to   { transform: scale(1); opacity: 1 }
        }
    </style>
</head>

<body>
<div class="wrap">
    <div class="card">

        <!-- HEADER -->
        <div class="header">
            <div>
                <h1>Form Input Arsip Dimusnahkan</h1>
                <p class="sub">Lengkapi informasi arsip yang akan dimusnahkan</p>
            </div>
            <div class="btns">
                <a class="btn secondary" href="tabel_arsip.php">📋 Lihat Data</a>
                <a class="btn" href="homepage.php">⬅️ Kembali</a>
            </div>
        </div>

        <!-- FORM -->
        <form method="POST" action="proses_input_arsip.php" novalidate>
            <div class="grid">

                <div class="form-group">
                    <label>KODE KLASIFIKASI <span class="required">*</span></label>
                    <input name="kode_klasifikasi"
                        placeholder="HM.002"
                        value="<?= htmlspecialchars(old('kode_klasifikasi')) ?>">
                </div>

                <div class="form-group span-2">
                    <label>NAMA BERKAS <span class="required">*</span></label>
                    <input name="nama_berkas"
                        placeholder="Informasi Meteorologi Publik"
                        value="<?= htmlspecialchars(old('nama_berkas')) ?>">
                </div>

                <div class="form-group">
                    <label>NO. ISI BERKAS<span class="required">*</span></label>
                    <input type="number" name="no_isi"
                        placeholder="contoh: 1" min="1" step="1" 
                        oninput="this.value = this.value.replace(/[^0-9]/g,'')"
                        value="<?= htmlspecialchars($old_input['no_isi'] ?? '') ?>">
                </div>

                <div class="form-group span-2">
                    <label>PENCIPTA ARSIP</label>
                    <input name="pencipta"
                        placeholder="BMKG Pusat Penelitian dan Pengembangan"
                        value="<?= htmlspecialchars(old('pencipta')) ?>">
                </div>

                <div class="form-group">
                    <label>TANGGAL SURAT/ KURUN WAKTU</label>
                    <input type="text" name="tanggal" 
                        placeholder="YYYY atau YYYY-MM-DD"
                        pattern="^\d{4}(-\d{2}-\d{2})?$"
                        title="Isi dengan tahun (YYYY) atau tanggal lengkap (YYYY-MM-DD)"
                        value="<?= htmlspecialchars(old('tanggal')) ?>">
                </div>

                <div class="form-group span-3">
                    <label>NO. SURAT</label>
                    <input name="no_surat"
                        placeholder="HM.002/001/DI/XII/2018"
                        value="<?= htmlspecialchars(old('no_surat')) ?>">
                </div>

                <div class="form-group span-3">
                    <label>URAIAN INFORMASI DOKUMEN</label>
                    <textarea name="uraian"
                            placeholder="Jelaskan singkat isi informasi arsip..."><?= htmlspecialchars(old('uraian')) ?></textarea>
                </div>

                <div class="form-group">
                    <label>JUMLAH</label>
                    <input name="jumlah"
                        placeholder="3 lembar"
                        value="<?= htmlspecialchars(old('jumlah')) ?>">
                </div>

                <div class="form-group">
                    <label>TINGKAT PERKEMBANGAN</label>
                    <select name="tingkat">
                        <option value="">-- pilih --</option>
                        <option <?= old('tingkat') === 'Asli' ? 'selected' : '' ?>>Asli</option>
                        <option <?= old('tingkat') === 'Fotocopy' ? 'selected' : '' ?>>Fotocopy</option>
                        <option <?= old('tingkat') === 'Scan' ? 'selected' : '' ?>>Scan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>LOKASI SIMPAN</label>
                    <input name="lokasi"
                        placeholder="Rak A1 / Lemari 1"
                        value="<?= htmlspecialchars(old('lokasi')) ?>">
                </div>

                <div class="form-group span-3">
                    <label>KETERANGAN</label>
                    <input name="keterangan"
                        placeholder="Baik / Perlu Perbaikan"
                        value="<?= htmlspecialchars(old('keterangan')) ?>">
                </div>

            </div>

            <div class="actions">
                <button type="submit" class="btn">💾 Simpan ke Database</button>
                <a href="input_arsip.php" class="btn secondary">🔄 Reset Form</a>
            </div>
        </form>
    </div>
</div>

<?php if ($success): ?>
<div id="successModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-icon">✅</div>
        <h3>Berhasil</h3>
        <p><?= htmlspecialchars($success) ?></p>
        <button onclick="closeModal()">OK</button>
    </div>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div id="errorModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <h3>Form Belum Lengkap</h3>
        <p><?= $error ?></p>
        <button onclick="closeErrorModal()">OK</button>
    </div>
</div>
<?php endif; ?>

<script>
function closeModal() {
    const modal = document.getElementById('successModal');
    if (modal) modal.remove();
}

function closeErrorModal() {
    const modal = document.getElementById('errorModal');
    if (modal) modal.remove();

    <?php if (!empty($errorField)): ?>
        const field = document.querySelector('[name="<?= $errorField ?>"]');
        if (field) {
            field.focus();
            field.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    <?php endif; ?>
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const noIsi = document.querySelector('[name="no_isi"]');
    
    // Fungsi untuk menampilkan modal error
    function showErrorModal(message, fieldName) {
        // Hapus modal yang ada
        const existingModal = document.getElementById('errorModal');
        if (existingModal) existingModal.remove();
        
        // Buat modal baru
        const modal = document.createElement('div');
        modal.id = 'errorModal';
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-box">
                <div class="modal-icon">⚠️</div>
                <h3>Form Belum Lengkap</h3>
                <p>${message}</p>
                <button onclick="closeValidationModal('${fieldName}')">OK</button>
            </div>
        `;
        document.body.appendChild(modal);

    noIsi.addEventListener('input', function () {
        // Hapus nilai minus atau nol
        if (this.value !== '' && parseInt(this.value) < 1) {
            this.value = '';
        }
    });

    noIsi.addEventListener('keydown', function (e) {
        // Blok tombol minus
        if (e.key === '-' || e.key === 'e') {
            e.preventDefault();
        }
    });
    }
    
    // Fungsi untuk menutup modal validasi dan fokus ke field
    window.closeValidationModal = function(fieldName) {
        const modal = document.getElementById('errorModal');
        if (modal) modal.remove();
        
        if (fieldName) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.focus();
                field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Tambahkan efek highlight
                field.style.borderColor = '#e53e3e';
                setTimeout(() => {
                    field.style.borderColor = '';
                }, 2000);
            }
        }
    };
    
    // Event listener untuk form submit
    form.addEventListener('submit', function(e) {
        // Reset semua styling error
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.style.borderColor = '';
        });
        
        // Validasi Kode Klasifikasi
        const kodeKlasifikasi = document.querySelector('[name="kode_klasifikasi"]');
        if (!kodeKlasifikasi.value.trim()) {
            e.preventDefault();
            showErrorModal('Kode klasifikasi belum diisi', 'kode_klasifikasi');
            return;
        }
        
        // Validasi Nama Berkas
        const namaBerkas = document.querySelector('[name="nama_berkas"]');
        if (!namaBerkas.value.trim()) {
            e.preventDefault();
            showErrorModal('Nama berkas belum diisi', 'nama_berkas');
            return;
        }
        
        // Validasi No. Isi
        const noIsi = document.querySelector('[name="no_isi"]');
        if (!noIsi.value.trim()) {
            e.preventDefault();
            showErrorModal('Nomor isi belum diisi', 'no_isi');
            return;
        }
        
        // Validasi No. Isi harus angka
        if (isNaN(noIsi.value) || noIsi.value <= 0) {
            e.preventDefault();
            showErrorModal('Nomor isi harus berupa angka yang valid', 'no_isi');
            return;
        }
        
        // Jika semua validasi OK, form akan ter-submit
    });
});
</script>

</body>
</html>