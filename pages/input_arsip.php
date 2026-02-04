<?php
require_once __DIR__ . "/../actions/proses_tabel.php";

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

    // validasi minimal
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
  /* ✅ Background gradasi seperti homepage */
  --bg-start: #f3f5f9;
  --bg-end: #e2e7f1;

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

/* ✅ KUNCI SCROLL BACKGROUND */
html, body{
  width:100%;
  height:100%;
  margin:0;
  overflow:hidden;                 /* 🔒 body ga bisa geser */
}

/* ✅ BACKGROUND FIXED */
body{
  height:100vh;
  background: linear-gradient(135deg, var(--bg-start), var(--bg-end));
  position:relative;
  font-family: Inter, Arial, sans-serif;

  padding: 14px;                   /* compact */
  display:flex;
  align-items:stretch;             /* shell bisa tinggi */
  justify-content:center;
}

/* background bersih tanpa layer tambahan */

/* wrapper */
.wrap{
  width: 100%;
  max-width: none;
  height: 100%;
  position:relative;
  z-index:2;
  display:flex;
}

/* layout */
.layout{
  width:100%;
  height:100%;
  display:flex;
  gap:14px;
}

/* sidebar */
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

  overflow:auto;                   /* ✅ scroll di sini */
  overflow-x:hidden;
  -webkit-overflow-scrolling: touch;

  backdrop-filter: blur(10px);
}

/* cegah overflow horizontal dari elemen dalam */
.shell *{ max-width:100%; }
input, textarea, select{ max-width:100%; }

/* header */
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
  background: var(--btn);
  color: #fff;
}

/* alerts */
.alert-err{
  margin: 8px 0 12px;
  padding: 12px 14px;
  border-radius: 14px;
  background:#fff5f5;
  border:1px solid #ffd0d0;
  color:#7a1f1f;
  font-weight:700;
  font-size: 13px;
}

/* form */
.form{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px 12px;                  /* lebih compact */
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
.req{ color:#ef4444; }

input, textarea, select{
  width:100%;
  padding: 10px 12px;              /* lebih compact */
  border-radius: 12px;
  border: 1px solid var(--line);
  outline:none;
  font-size: 14px;
  background:#fff;
  color:#0f172a;
}

textarea{
  min-height: 90px;
  resize: vertical;
}

input:focus, textarea:focus, select:focus{
  border-color:#c7b7ff;
  box-shadow:0 0 0 4px rgba(124, 58, 237, .12);
}

.full{ grid-column: 1 / -1; }

/* row3 */
.row3{
  display:grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 10px 12px;
  width:100%;
}
.row3.full{ grid-column: 1 / -1; }

.help{
  font-size: 12.5px;
  color: #64748b;
  margin-top: -2px;
}

/* bottom actions */
.bottomActions{
  display:flex;
  gap:10px;
  justify-content:flex-end;
  margin-top: 12px;
  flex-wrap:wrap;
  padding-bottom: 4px;
}

button.primary{
  border:none;
  padding: 10px 14px;
  border-radius: 14px;
  background: #0f172a;
  color:#fff;
  font-weight:900;
  font-size: 13px;
  cursor:pointer;
  display:inline-flex;
  gap:10px;
  align-items:center;
}
button.primary:active{ transform: translateY(1px); }

button.ghost{
  border:1px solid #d7ddff;
  padding: 10px 14px;
  border-radius: 14px;
  background: #eef2ff;
  color:#1f2a44;
  font-weight:900;
  font-size: 13px;
  cursor:pointer;
  display:inline-flex;
  gap:10px;
  align-items:center;
}
button.ghost:active{ transform: translateY(1px); }

/* ✅ MOBILE */
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

  .titles h1{ font-size: 20px; }
  .form{ grid-template-columns: 1fr; }
  .row3{ grid-template-columns: 1fr; }

  .bottomActions{ justify-content:stretch; }
  button.primary, button.ghost{ width:100%; justify-content:center; }

  .actionsTop{ width:100%; }
  a.btn{ flex:1; justify-content:center; }
}

/* ✅ super small */
@media (max-width: 380px){
  input, textarea, select{ font-size: 14px; }
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
            <a class="side-link" href="/SortirDokumen/pages/form.php">
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
            <a class="side-link active" href="/SortirDokumen/pages/input_arsip.php">
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
          <a class="side-link" href="/SortirDokumen/pages/export_menu.php">
            <div class="side-icon">📥</div>
            <div class="side-text">
              <strong>Export CSV</strong>
              <span>Unduh data CSV/Excel.</span>
            </div>
          </a>
        </li>
        <li>
          <a class="side-link" href="/SortirDokumen/pages/export_pemusnahan.php">
            <div class="side-icon">🗑️</div>
            <div class="side-text">
              <strong>Export Dokumen Musnah</strong>
              <span>Unduh data arsip dimusnahkan.</span>
            </div>
          </a>
        </li>
      </ul>
    </aside>

      <div class="shell">

      <div class="top">
        <div class="titles">
          <h1><?= htmlspecialchars($pageTitle) ?></h1>
          <p><?= htmlspecialchars($subTitle) ?></p>
        </div>

        <div class="actionsTop">
          <a class="btn light" href="tabel_arsip.php"> Lihat Data</a>
          <a class="btn dark" href="homepage.php"> Kembali</a>
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
              placeholder="HM.002"
              required
            >
            
          </div>

          <div class="field">
            <label>Nama Berkas <span class="req">*</span></label>
            <input
              name="nama_berkas"
              value="<?= htmlspecialchars($data["nama_berkas"]) ?>"
              placeholder="Informasi Meteorologi Publik"
              required
            >
            
          </div>

          <div class="field">
            <label>No. Isi Berkas <span class="req">*</span></label>
            <input
              name="no_isi"
              value="<?= htmlspecialchars($data["no_isi"]) ?>"
              placeholder="1"
              required
            >
            
          </div>

          <div class="field">
            <label>Pencipta Arsip</label>
            <input
              name="pencipta"
              value="<?= htmlspecialchars($data["pencipta"]) ?>"
              placeholder="BMKG Pusat Penelitian dan Pengembangan"
            >
            
          </div>

          <div class="field">
            <label>Tanggal Surat / Kurun Waktu</label>
            <input
              name="tanggal"
              value="<?= htmlspecialchars($data["tanggal"]) ?>"
              placeholder="YYYY atau YYYY-MM-DD"
            >
            
          </div>

          <div class="field full">
            <label>No. Surat</label>
            <input
              name="no_surat"
              value="<?= htmlspecialchars($data["no_surat"]) ?>"
              placeholder="HM.002/001/DI/XII/2018"
            >
            
          </div>

          <div class="field full">
            <label>Uraian Informasi Dokumen</label>
            <textarea
              name="uraian"
              placeholder="Jelaskan singkat isi informasi arsip..."
            ><?= htmlspecialchars($data["uraian"]) ?></textarea>
            
          </div>

          <div class="row3 full">
            <div class="field">
              <label>Jumlah</label>
              <input
                name="jumlah"
                value="<?= htmlspecialchars($data["jumlah"]) ?>"
                placeholder="3 lembar"
              >
              
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
              
            </div>

            <div class="field">
              <label>Lokasi Simpan</label>
              <input
                name="lokasi"
                value="<?= htmlspecialchars($data["lokasi"]) ?>"
                placeholder="Rak A1 / Lemari 1"
              >
              
            </div>
          </div>

          <div class="field full">
            <label>Keterangan</label>
            <input
              name="keterangan"
              value="<?= htmlspecialchars($data["keterangan"]) ?>"
              placeholder="Baik / Perlu Perbaikan"
            >
            
          </div>

        </div>

        <div class="bottomActions">
          <button class="primary" type="submit"><?= $btnText ?></button>
          <button class="ghost" type="reset">🔄 Reset Form</button>
        </div>
      </form>

      </div>
    </div>
  </div>
</body>
</html>
