<?php
require_once "koneksi.php";

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$error = "";

/* ✅ CSRF token */
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$token = $_SESSION['csrf_token'];

/* =========================
   MODE EDIT (GET ?edit=id)
========================= */
$isEdit = false;
$editId = null;

$data = [
  "kode_klasifikasi" => "",
  "nama_berkas"      => "",
  "no_isi"           => "",
  "pencipta"         => "",
  "no_surat"         => "",
  "uraian"           => "",
  "tanggal"          => "",
  "jumlah"           => "",
  "tingkat"          => "",
  "lokasi"           => "",
  "keterangan"       => "",
];

if (!empty($_GET['edit']) && ctype_digit($_GET['edit'])) {
  $isEdit = true;
  $editId = (int)$_GET['edit'];

  try {
    $stmt = $dbhandle->prepare("SELECT * FROM arsip_dimusnahkan WHERE id = :id");
    $stmt->execute([":id" => $editId]);
    $found = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$found) {
      $isEdit = false;
      $editId = null;
      $error = "Data dengan ID tersebut tidak ditemukan.";
    } else {
      foreach ($data as $k => $_) {
        $data[$k] = $found[$k] ?? "";
      }
    }
  } catch (PDOException $e) {
    $error = "Gagal ambil data edit: " . $e->getMessage();
  }
}

/* =========================
   SUBMIT (POST) CREATE / UPDATE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $postToken = $_POST['token'] ?? '';
  if (!hash_equals($_SESSION['csrf_token'], $postToken)) {
    $error = "Token tidak valid. Coba refresh halaman.";
  } else {
    $action = $_POST['action'] ?? 'create';
    $idPost = $_POST['id'] ?? '';

    // ambil input
    $data["kode_klasifikasi"] = trim($_POST["kode_klasifikasi"] ?? "");
    $data["nama_berkas"]      = trim($_POST["nama_berkas"] ?? "");
    $data["no_isi"]           = trim($_POST["no_isi"] ?? "");
    $data["pencipta"]         = trim($_POST["pencipta"] ?? "");
    $data["tanggal"]          = trim($_POST["tanggal"] ?? "");
    $data["no_surat"]         = trim($_POST["no_surat"] ?? "");
    $data["uraian"]           = trim($_POST["uraian"] ?? "");
    $data["jumlah"]           = trim($_POST["jumlah"] ?? "");
    $data["tingkat"]          = trim($_POST["tingkat"] ?? "");
    $data["lokasi"]           = trim($_POST["lokasi"] ?? "");
    $data["keterangan"]       = trim($_POST["keterangan"] ?? "");

    // validasi minimal (biar gak kosong banget)
    if ($data["kode_klasifikasi"] === "" || $data["nama_berkas"] === "" || $data["no_isi"] === "") {
      $error = "Kode Klasifikasi, Nama Berkas, dan No. Isi wajib diisi.";
    } else {
      try {
        if ($action === "update") {
          if ($idPost === "" || !ctype_digit((string)$idPost)) {
            throw new Exception("ID update tidak valid.");
          }
          $idPost = (int)$idPost;

          $sql = "UPDATE arsip_dimusnahkan SET
            kode_klasifikasi = :kode_klasifikasi,
            nama_berkas      = :nama_berkas,
            no_isi           = :no_isi,
            pencipta         = :pencipta,
            tanggal          = :tanggal,
            no_surat         = :no_surat,
            uraian           = :uraian,
            jumlah           = :jumlah,
            tingkat          = :tingkat,
            lokasi           = :lokasi,
            keterangan       = :keterangan
            WHERE id = :id";

          $stmt = $dbhandle->prepare($sql);
          $stmt->execute([
            ":kode_klasifikasi" => $data["kode_klasifikasi"],
            ":nama_berkas"      => $data["nama_berkas"],
            ":no_isi"           => $data["no_isi"],
            ":pencipta"         => $data["pencipta"],
            ":tanggal"          => $data["tanggal"],
            ":no_surat"         => $data["no_surat"],
            ":uraian"           => $data["uraian"],
            ":jumlah"           => $data["jumlah"],
            ":tingkat"          => $data["tingkat"],
            ":lokasi"           => $data["lokasi"],
            ":keterangan"       => $data["keterangan"],
            ":id"               => $idPost
          ]);

          header("Location: tabel_arsip.php?msg=updated");
          exit;
        } else {
          $sql = "INSERT INTO arsip_dimusnahkan
            (kode_klasifikasi, nama_berkas, no_isi, pencipta, tanggal, no_surat, uraian, jumlah, tingkat, lokasi, keterangan)
            VALUES
            (:kode_klasifikasi, :nama_berkas, :no_isi, :pencipta, :tanggal, :no_surat, :uraian, :jumlah, :tingkat, :lokasi, :keterangan)";

          $stmt = $dbhandle->prepare($sql);
          $stmt->execute([
            ":kode_klasifikasi" => $data["kode_klasifikasi"],
            ":nama_berkas"      => $data["nama_berkas"],
            ":no_isi"           => $data["no_isi"],
            ":pencipta"         => $data["pencipta"],
            ":tanggal"          => $data["tanggal"],
            ":no_surat"         => $data["no_surat"],
            ":uraian"           => $data["uraian"],
            ":jumlah"           => $data["jumlah"],
            ":tingkat"          => $data["tingkat"],
            ":lokasi"           => $data["lokasi"],
            ":keterangan"       => $data["keterangan"],
          ]);

          header("Location: tabel_arsip.php?msg=created");
          exit;
        }
      } catch (Throwable $e) {
        $error = "Gagal simpan: " . $e->getMessage();
      }
    }
  }
}

$pageTitle = $isEdit ? "Form Edit Arsip Dimusnahkan" : "Form Input Arsip Dimusnahkan";
$subTitle  = "Lengkapi informasi arsip yang akan dimusnahkan";
$btnText   = $isEdit ? "💾 Update ke Database" : "💾 Simpan ke Database";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= htmlspecialchars($pageTitle) ?></title>

<style>
:root{
  /* ✅ Background 3 warna flat */
  --dark-bg: #1c2229;
  --purple-dark: #5b2a86;
  --purple-light:#8e6bbf;

  --card: rgba(255,255,255,.96);
  --text:#0f172a;
  --muted:#64748b;
  --line:#e5e7eb;
  --shadow: 0 22px 70px rgba(0,0,0,.22);
  --radius: 24px;

  --btn: #0f172a;
  --btn2:#eef2ff;
}

*{ box-sizing:border-box; }
html, body{ width:100%; height:100%; margin:0; }

body{
  min-height:100vh;
  background: var(--dark-bg);
  position:relative;
  font-family: Inter, Arial, sans-serif;
  padding: 32px 18px;
  display:flex;
  align-items:flex-start;
  justify-content:center;
}

/* 2 bidang ungu */
body::before,
body::after{
  content:"";
  position:absolute;
  inset:0;
  z-index:0;
  pointer-events:none;
}
body::before{
  background: var(--purple-dark);
  clip-path: polygon(55% 0, 100% 0, 100% 100%, 70% 100%);
  opacity: .95;
}
body::after{
  background: var(--purple-light);
  clip-path: polygon(35% 0, 65% 0, 85% 100%, 55% 100%);
  opacity: .90;
}

.wrap{
  width: min(1320px, 100%);
  position:relative;
  z-index:2;
}

/* ✅ Shell ala screenshot (besar, rounded, clean) */
.shell{
  background: var(--card);
  border: 1px solid rgba(255,255,255,.55);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 22px 22px 18px;
  overflow:hidden;
}

/* header */
.top{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:14px;
  flex-wrap:wrap;
  margin-bottom: 12px;
}
.titles h1{
  margin:0;
  font-size: 26px;
  letter-spacing:.2px;
}
.titles p{
  margin:6px 0 0;
  color: var(--muted);
  font-size: 14px;
}

.actionsTop{
  display:flex;
  gap:12px;
  align-items:center;
}

a.btn{
  display:inline-flex;
  gap:8px;
  align-items:center;
  padding:12px 16px;
  border-radius: 14px;
  text-decoration:none;
  font-weight:900;
  border: 1px solid transparent;
}

a.btn.light{
  background: var(--btn2);
  color: #1f2a44;
  border-color: #d7ddff;
}
a.btn.dark{
  background: var(--btn);
  color: #fff;
}

/* alerts */
.alert-err{
  margin: 8px 0 14px;
  padding: 12px 14px;
  border-radius: 14px;
  background:#fff5f5;
  border:1px solid #ffd0d0;
  color:#7a1f1f;
  font-weight:700;
  font-size: 13px;
}

/* form grid - mirip screenshot */
.form{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px 18px;
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
  font-size: 14px;
  letter-spacing:.2px;
  text-transform: uppercase;
}

.req{ color:#ef4444; }

input, textarea, select{
  width:100%;
  padding: 14px 16px;
  border-radius: 14px;
  border: 1px solid var(--line);
  outline:none;
  font-size: 16px;
  background:#fff;
  color:#0f172a;
}

textarea{
  min-height: 120px;
  resize: vertical;
}

input:focus, textarea:focus, select:focus{
  border-color:#c7b7ff;
  box-shadow:0 0 0 4px rgba(124, 58, 237, .12);
}

.full{ grid-column: 1 / -1; }

/* row 3 kolom seperti screenshot */
.row3{
  display:grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 14px 18px;
}
.row3.full{ grid-column: 1 / -1; }

/* helper text */
.help{
  font-size: 13px;
  color: #64748b;
  margin-top: -2px;
}

/* footer buttons bottom-right */
.bottomActions{
  display:flex;
  gap:12px;
  justify-content:flex-end;
  margin-top: 14px;
  flex-wrap:wrap;
}

button.primary{
  border:none;
  padding: 14px 18px;
  border-radius: 16px;
  background: #0f172a;
  color:#fff;
  font-weight:900;
  font-size: 16px;
  cursor:pointer;
  display:inline-flex;
  gap:10px;
  align-items:center;
}
button.primary:hover{ filter: brightness(1.03); }
button.primary:active{ transform: translateY(1px); }

button.ghost{
  border:1px solid #d7ddff;
  padding: 14px 18px;
  border-radius: 16px;
  background: #eef2ff;
  color:#1f2a44;
  font-weight:900;
  font-size: 16px;
  cursor:pointer;
  display:inline-flex;
  gap:10px;
  align-items:center;
}
button.ghost:hover{ filter: brightness(0.99); }
button.ghost:active{ transform: translateY(1px); }

@media (max-width: 980px){
  .titles h1{ font-size:22px; }
  label{ font-size:13px; }
  input, textarea, select{ font-size:15px; }
  .form{ grid-template-columns: 1fr; }
  .row3{ grid-template-columns: 1fr; }
  .bottomActions{ justify-content:stretch; }
  button.primary, button.ghost{ width:100%; justify-content:center; }
  .actionsTop{ width:100%; }
  a.btn{ flex:1; justify-content:center; }
}
</style>
</head>

<body>
  <div class="wrap">
    <div class="shell">

      <div class="top">
        <div class="titles">
          <h1><?= htmlspecialchars($pageTitle) ?></h1>
          <p><?= htmlspecialchars($subTitle) ?></p>
        </div>

        <div class="actionsTop">
          <a class="btn light" href="tabel_arsip.php">📁 Lihat Data</a>
          <a class="btn dark" href="index.php">⬅️ Kembali</a>
        </div>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert-err"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'create' ?>">
        <?php if ($isEdit): ?>
          <input type="hidden" name="id" value="<?= htmlspecialchars((string)$editId) ?>">
        <?php endif; ?>

        <div class="form">
          <div class="field">
            <label>Kode Klasifikasi <span class="req">*</span></label>
            <input
              name="kode_klasifikasi"
              value="<?= htmlspecialchars($data["kode_klasifikasi"]) ?>"
              placeholder="Contoh: HM.002"
              required
            >
            <div class="help">Isi kode klasifikasi sesuai aturan arsip (contoh: HM.002 / KU.01.02).</div>
          </div>

          <div class="field">
            <label>Nama Berkas <span class="req">*</span></label>
            <input
              name="nama_berkas"
              value="<?= htmlspecialchars($data["nama_berkas"]) ?>"
              placeholder="Contoh: Informasi Meteorologi Publik"
              required
            >
            <div class="help">Nama berkas yang jelas biar gampang dicari.</div>
          </div>

          <div class="field">
            <label>No. Isi Berkas <span class="req">*</span></label>
            <input
              name="no_isi"
              value="<?= htmlspecialchars($data["no_isi"]) ?>"
              placeholder="contoh: 1"
              required
            >
            <div class="help">Nomor urut isi dokumen di berkas (misal: 1, 2, 3...).</div>
          </div>

          <div class="field">
            <label>Pencipta Arsip</label>
            <input
              name="pencipta"
              value="<?= htmlspecialchars($data["pencipta"]) ?>"
              placeholder="Contoh: BMKG Pusat Penelitian dan Pengembangan"
            >
            <div class="help">Unit/instansi yang membuat dokumen.</div>
          </div>

          <div class="field">
            <label>Tanggal Surat / Kurun Waktu</label>
            <input
              name="tanggal"
              value="<?= htmlspecialchars($data["tanggal"]) ?>"
              placeholder="YYYY atau YYYY-MM-DD"
            >
            <div class="help">Contoh: 2018 atau 2018-12-31 (boleh rentang: 2018-01 s/d 2018-12).</div>
          </div>

          <div class="field full">
            <label>No. Surat</label>
            <input
              name="no_surat"
              value="<?= htmlspecialchars($data["no_surat"]) ?>"
              placeholder="Contoh: HM.002/001/DI/XII/2018"
            >
            <div class="help">Isi kalau dokumen berbentuk surat. Kalau bukan, boleh dikosongin.</div>
          </div>

          <div class="field full">
            <label>Uraian Informasi Dokumen</label>
            <textarea
              name="uraian"
              placeholder="Jelaskan singkat isi informasi arsip..."
            ><?= htmlspecialchars($data["uraian"]) ?></textarea>
            <div class="help">Ringkasan isi dokumen (ini kepake banget buat pencarian).</div>
          </div>

          <div class="row3 full">
            <div class="field">
              <label>Jumlah</label>
              <input
                name="jumlah"
                value="<?= htmlspecialchars($data["jumlah"]) ?>"
                placeholder="Contoh: 3 lembar"
              >
              <div class="help">Isi jumlah + satuan (lembar/berkas/map).</div>
            </div>

            <div class="field">
              <label>Tingkat Perkembangan</label>
              <select name="tingkat">
                <?php
                  $opt = [
                    "" => "-- pilih --",
                    "Asli" => "Asli",
                    "Copy" => "Copy",
                    "Scan" => "Scan",
                    "Fotokopi" => "Fotokopi",
                  ];
                  foreach ($opt as $val => $label) {
                    $selected = ((string)$data["tingkat"] === (string)$val) ? "selected" : "";
                    echo '<option value="'.htmlspecialchars($val).'" '.$selected.'>'.htmlspecialchars($label).'</option>';
                  }
                ?>
              </select>
              <div class="help">Pilih jenis dokumen: asli/copy/scan.</div>
            </div>

            <div class="field">
              <label>Lokasi Simpan</label>
              <input
                name="lokasi"
                value="<?= htmlspecialchars($data["lokasi"]) ?>"
                placeholder="Contoh: Rak A1 / Lemari 1"
              >
              <div class="help">Lokasi fisik penyimpanan dokumen.</div>
            </div>
          </div>

          <div class="field full">
            <label>Keterangan</label>
            <input
              name="keterangan"
              value="<?= htmlspecialchars($data["keterangan"]) ?>"
              placeholder="Contoh: Baik / Perlu Perbaikan"
            >
            <div class="help">Catatan tambahan (status, sifat dokumen, dll).</div>
          </div>
        </div>

        <div class="bottomActions">
          <button class="primary" type="submit"><?= $btnText ?></button>
          <button class="ghost" type="reset">🔄 Reset Form</button>
        </div>
      </form>

    </div>
  </div>
</body>
</html>
