<?php
session_start();

/* =======================
   SESSION MESSAGE HANDLER
======================= */
$error      = $_SESSION['error'] ?? '';
$success    = $_SESSION['success'] ?? '';
$old_input  = $_SESSION['old_input'] ?? [];

unset($_SESSION['error'], $_SESSION['success']);

/* =======================
   HELPER OLD INPUT
======================= */
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
        /* ===== RESET ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* ===== BASE ===== */
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

        /* ===== HEADER ===== */
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

        /* ===== BUTTON ===== */
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

        <!-- ALERT -->
        <?php if ($error): ?>
            <div class="alert error">
                <span>⚠️</span>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert success">
                <span>✅</span>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        <?php endif; ?>

        <!-- FORM -->
        <form method="POST" action="proses_input_arsip.php">
            <div class="grid">

                <div class="form-group">
                    <label>KODE KLASIFIKASI <span class="required">*</span></label>
                    <input name="kode_klasifikasi" required
                           placeholder="contoh: HM.002"
                           value="<?= htmlspecialchars(old('kode_klasifikasi')) ?>">
                </div>

                <div class="form-group span-2">
                    <label>NAMA BERKAS <span class="required">*</span></label>
                    <input name="nama_berkas" required
                           placeholder="contoh: Informasi Meteorologi Publik"
                           value="<?= htmlspecialchars(old('nama_berkas')) ?>">
                </div>

                <div class="form-group">
                    <label>NO. ISI <span class="required">*</span></label>
                    <input type="number" name="no_isi" required
                           placeholder="contoh: 1"
                           value="<?= htmlspecialchars($old_input['no_isi'] ?? '') ?>">
                </div>

                <div class="form-group span-2">
                    <label>PENCIPTA ARSIP</label>
                    <input name="pencipta"
                           placeholder="contoh: BMKG Stasiun ..."
                           value="<?= htmlspecialchars(old('pencipta')) ?>">
                </div>

                <div class="form-group">
                    <label>TANGGAL SURAT</label>
                    <input type="date" name="tanggal"
                           value="<?= htmlspecialchars(old('tanggal')) ?>">
                </div>

                <div class="form-group span-3">
                    <label>NO. SURAT</label>
                    <input name="no_surat"
                           placeholder="contoh: HM.002/001/XII/2018"
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
                           placeholder="contoh: 3 lembar / 1 berkas"
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
                           placeholder="contoh: Rak A1 / Lemari 1"
                           value="<?= htmlspecialchars(old('lokasi')) ?>">
                </div>

                <div class="form-group span-3">
                    <label>KETERANGAN</label>
                    <input name="keterangan"
                           placeholder="contoh: Baik / Perlu Perbaikan"
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
</body>
</html>
