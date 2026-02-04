<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../actions/prosesArsip.php";
require_once __DIR__ . "/../auth/auth_check.php";

$path = $_GET['path'] ?? '';
$action = $_GET['action'] ?? '';
$query = trim($_GET['q'] ?? '');
$filterJenis = $_GET['jenis'] ?? '';
$filterTahun = $_GET['tahun'] ?? '';
$filterBulan = $_GET['bulan'] ?? '';
$filterSub = $_GET['subkode'] ?? '';

// Handle actions (view/download)
if ($action === 'view' && isset($_GET['id'])) {
    viewFile($_GET['id']);
    exit;
}

if ($action === 'download' && isset($_GET['id'])) {
    downloadFile($_GET['id']);
    exit;
}

// Get items untuk ditampilkan
$isFilterMode = ($filterJenis !== '' || $filterTahun !== '' || $filterBulan !== '' || $filterSub !== '');
$items = $isFilterMode
    ? getFilesByFiltersAndQuery(
        $filterJenis !== '' ? $filterJenis : null,
        $filterTahun !== '' ? $filterTahun : null,
        $filterBulan !== '' ? $filterBulan : null,
        $filterSub !== '' ? $filterSub : null,
        $query !== '' ? $query : null
    )
    : (($query !== '') ? searchFilesRecursive($path, $query) : getItems($path));
$parts = array_filter(explode('/', $path));
$filterOptions = getFilterOptions(
    $filterJenis !== '' ? $filterJenis : null,
    $filterTahun !== '' ? $filterTahun : null,
    $filterBulan !== '' ? $filterBulan : null
);
?>

<!DOCTYPE htqml>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Arsip Dokumen</title>

<style>
:root{
  --bg-start: #f3f5f9;
  --bg-end: #e2e7f1;

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
  background: var(--btn);
  color: #fff;
}

.breadcrumb{
  background:#f8fafc;
  padding: 10px 12px;
  border-radius: 12px;
  border: 1px solid #e9ecef;
  font-size: 12px;
  display:flex;
  align-items:center;
  gap:8px;
  flex-wrap:wrap;
  margin-bottom: 12px;
}

.breadcrumb a{
  color:#4a6cf7;
  text-decoration:none;
  font-weight:700;
  padding: 3px 8px;
  border-radius: 8px;
}
.breadcrumb a:hover{
  background:#4a6cf7;
  color:#fff;
}
.breadcrumb .separator{
  color:#adb5bd;
}

.search-row{
  display:flex;
  align-items:center;
  gap:10px;
  margin: 0 0 12px;
  flex-wrap:wrap;
}
.search-wrap{
  flex: 1 1 320px;
  display:flex;
  align-items:center;
  gap:8px;
  background:#f8fafc;
  border: 1px solid #e9ecef;
  padding: 8px 10px;
  border-radius: 12px;
}
.search-wrap span{
  font-size: 14px;
  opacity:.65;
}
.search-input{
  border:none;
  background: transparent;
  outline:none;
  width:100%;
  font-size: 13px;
  color:#0f172a;
}
.search-hint{
  font-size: 11px;
  color: var(--muted);
}
.search-clear{
  background: var(--btn2);
  color:#1f2a44;
  border: 1px solid #d7ddff;
  padding: 6px 10px;
  border-radius: 10px;
  text-decoration:none;
  font-size: 12px;
  font-weight: 700;
}

.filter-row{
  display:flex;
  align-items:center;
  gap:10px;
  flex-wrap:wrap;
  margin: 0 0 12px;
}
.filter-group{
  display:flex;
  align-items:center;
  gap:8px;
  flex-wrap:wrap;
}
.filter-select{
  background:#f8fafc;
  border: 1px solid #e9ecef;
  padding: 7px 10px;
  border-radius: 10px;
  font-size: 12px;
  color:#0f172a;
}
.filter-actions{
  display:flex;
  gap:8px;
  align-items:center;
}
.filter-note{
  font-size: 11px;
  color: var(--muted);
}

.meta{
  font-size: 11px;
  color: var(--muted);
  margin-top: 4px;
}
.meta a{
  color:#4a6cf7;
  text-decoration:none;
  font-weight:700;
}
.meta a:hover{
  text-decoration:underline;
}

.content{
  padding: 0;
}

.list{
  display:flex;
  flex-direction:column;
  gap:8px;
}

.item{
  display:flex;
  align-items:center;
  padding: 12px 14px;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  transition: all 0.2s ease;
  background:#fff;
}

.item a.item-link{
  display:flex;
  align-items:center;
  flex:1;
  text-decoration:none;
  color: inherit;
}

.item:has(a.item-link):hover{
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(74, 108, 247, 0.12);
  border-color:#c7b7ff;
  cursor:pointer;
}

.icon{
  width:42px;
  height:42px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size: 20px;
  margin-right: 12px;
  border-radius: 10px;
  flex-shrink:0;
  pointer-events:none;
}
.icon.folder{
  background: linear-gradient(135deg, #ffd89b 0%, #ff9a5a 100%);
}
.icon.file{
  background: linear-gradient(135deg, #a8edea 0%, #5e72e4 100%);
}

.name{
  flex:1;
  font-size: 14px;
  color:#2d3748;
  font-weight: 600;
  pointer-events:none;
  display:flex;
  align-items:center;
  gap:10px;
}
.file-count{
  font-size: 12px;
  color:#6c757d;
  font-weight: 500;
  background:#f1f3f5;
  padding:2px 8px;
  border-radius: 12px;
}

.actions{
  display:flex;
  gap:8px;
  margin-left: 12px;
}

.btn{
  width:36px;
  height:36px;
  display:flex;
  align-items:center;
  justify-content:center;
  border:none;
  border-radius: 10px;
  cursor:pointer;
  font-size: 16px;
  transition: all .2s;
  text-decoration:none;
}
.btn-view{
  background:#e3f2fd;
  color:#2196f3;
}
.btn-view:hover{
  background:#2196f3;
  color:#fff;
}
.btn-download{
  background:#e8f5e9;
  color:#4caf50;
}
.btn-download:hover{
  background:#4caf50;
  color:#fff;
}

.empty-state{
  text-align:center;
  padding: 40px 20px;
  color:#94a3b8;
}
.empty-state-icon{
  font-size: 48px;
  margin-bottom: 10px;
  opacity: .6;
}

.modal{
  position:fixed;
  inset:0;
  background: rgba(15, 23, 42, .45);
  display:none;
  align-items:center;
  justify-content:center;
  z-index: 999;
  padding: 20px;
}
.modal.show{ display:flex; }
.modal-card{
  width: min(960px, 92vw);
  height: min(80vh, 720px);
  background:#fff;
  border-radius: 16px;
  box-shadow: 0 24px 80px rgba(0,0,0,.25);
  display:flex;
  flex-direction:column;
  overflow:hidden;
}
.modal-head{
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding: 12px 14px;
  border-bottom:1px solid #e9ecef;
  background:#f8fafc;
  font-size: 13px;
  font-weight: 700;
  color:#0f172a;
}
.modal-close{
  border:none;
  background:#e2e8f0;
  color:#0f172a;
  padding:6px 10px;
  border-radius: 10px;
  cursor:pointer;
  font-weight:700;
}
.modal-body{
  flex:1;
  background:#0b1220;
  display:flex;
  align-items:center;
  justify-content:center;
}
.modal-body iframe{
  width:100%;
  height:100%;
  border:0;
  background:#0b1220;
}
.modal-body img{
  max-width:100%;
  max-height:100%;
  width:auto;
  height:auto;
  object-fit:contain;
  background:#0b1220;
}

@media (max-width: 980px){
  body{ padding: 10px; }
  .layout{ flex-direction:column; }
  .sidebar{
    width:100%;
    height:auto;
  }
  .shell{
    max-width:100%;
    padding:14px;
    border-radius: 20px;
  }
  .actionsTop{ width:100%; }
  a.btn{ flex:1; justify-content:center; }
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
          <a class="side-link" href="/SortirDokumen/pages/form.php">
            <div class="side-icon">🗂️</div>
            <div class="side-text">
              <strong>Sortir Dokumen</strong>
              <span>Kelola kategori dokumen.</span>
            </div>
          </a>
        </li>
        <li>
          <a class="side-link active" href="/SortirDokumen/pages/arsip.php">
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
          <h1>Arsip Dokumen</h1>
          <p>Kelola folder dan file arsip.</p>
        </div>
        <div class="actionsTop">
          <a class="btn dark" href="homepage.php">⬅️ Kembali</a>
        </div>
      </div>

      <div class="breadcrumb">
        <a href="arsip.php">🏠 Home</a>
        <?php
        $link = '';
        foreach ($parts as $p) {
            echo '<span class="separator">›</span>';
            $link .= ($link ? '/' : '') . $p;
            echo "<a href='arsip.php?path=$link'>$p</a>";
        }
        ?>
      </div>

      <form class="search-row" id="searchForm" method="get" action="arsip.php">
        <input type="hidden" name="path" value="<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="jenis" value="<?= htmlspecialchars($filterJenis, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="tahun" value="<?= htmlspecialchars($filterTahun, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="bulan" value="<?= htmlspecialchars($filterBulan, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="subkode" value="<?= htmlspecialchars($filterSub, ENT_QUOTES, 'UTF-8') ?>">
        <div class="search-wrap">
          <span>🔎</span>
          <input id="searchArsip" name="q" class="search-input" type="text" placeholder="Cari kode utama atau folder (rekursif)..." autocomplete="off" value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="search-hint">Berdasar kode utama, tahun, bulan, subkode, atau jenis</div>
        <?php if ($query !== ''): ?>
          <a class="search-clear" href="arsip.php?path=<?= urlencode($path) ?>">Reset</a>
        <?php endif; ?>
      </form>

      <form class="filter-row" method="get" action="arsip.php">
        <input type="hidden" name="path" value="<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>">
        <div class="filter-group">
          <select class="filter-select" name="jenis">
            <option value="">Semua jenis</option>
            <option value="masuk" <?= $filterJenis === 'masuk' ? 'selected' : '' ?>>Surat Masuk</option>
            <option value="keluar" <?= $filterJenis === 'keluar' ? 'selected' : '' ?>>Surat Keluar</option>
          </select>
          <select class="filter-select" name="tahun">
            <option value="">Semua tahun</option>
            <?php foreach ($filterOptions['years'] as $yr): ?>
              <option value="<?= htmlspecialchars($yr) ?>" <?= (string)$filterTahun === (string)$yr ? 'selected' : '' ?>><?= htmlspecialchars($yr) ?></option>
            <?php endforeach; ?>
          </select>
          <select class="filter-select" name="bulan">
            <option value="">Semua bulan</option>
            <?php foreach ($filterOptions['months'] as $mo): ?>
              <option value="<?= htmlspecialchars($mo) ?>" <?= (string)$filterBulan === (string)$mo ? 'selected' : '' ?>><?= htmlspecialchars($mo) ?></option>
            <?php endforeach; ?>
          </select>
          <select class="filter-select" name="subkode">
            <option value="">Semua subkode</option>
            <?php foreach ($filterOptions['subkodes'] as $sub): ?>
              <option value="<?= htmlspecialchars($sub) ?>" <?= (string)$filterSub === (string)$sub ? 'selected' : '' ?>><?= htmlspecialchars($sub) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="filter-actions">
          <button class="search-clear" type="submit">Terapkan</button>
          <?php if ($isFilterMode): ?>
            <a class="search-clear" href="arsip.php?path=<?= urlencode($path) ?>">Reset</a>
          <?php endif; ?>
        </div>
        <div class="filter-note">Filter update otomatis mengikuti data baru.</div>
      </form>

      <div class="content">
        <div class="list">
          <?php if (empty($items)): ?>
              <div class="empty-state">
                  <div class="empty-state-icon">📭</div>
                  <p>Tidak ada data di folder ini</p>
              </div>
          <?php else: ?>
              <?php foreach ($items as $item): ?>
                  <?php if ($item['type'] === 'folder'): ?>
                      <div class="item" data-search="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>">
                          <a href="<?= htmlspecialchars($item['link']) ?>" class="item-link">
                              <div class="icon folder">📁</div>
                              <div class="name">
                                  <span><?= htmlspecialchars($item['name']) ?></span>
                                  <span class="file-count">(<?= $item['count'] ?>)</span>
                              </div>
                          </a>
                      </div>
                  <?php else: ?>
                      <div class="item" data-search="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>">
                          <div class="icon file">📄</div>
                          <div class="name">
                              <span><?= htmlspecialchars($item['name']) ?></span>
                              <?php if (!empty($item['location'])): ?>
                                <div class="meta">
                                  Lokasi: <?= htmlspecialchars($item['location']) ?> ·
                                  <a href="<?= htmlspecialchars($item['folder_link']) ?>">Buka folder</a>
                                </div>
                              <?php endif; ?>
                          </div>
                          <div class="actions">
                              <button type="button" class="btn btn-view js-preview" data-preview="arsip.php?action=view&id=<?= $item['id'] ?>" title="Lihat">👁️</button>
                              <a href="arsip.php?action=download&id=<?= $item['id'] ?>" class="btn btn-download" title="Unduh">⬇️</a>
                          </div>
                      </div>
                  <?php endif; ?>
              <?php endforeach; ?>
              <div id="emptySearch" class="empty-state" style="display:none;">
                  <div class="empty-state-icon">🔍</div>
                  <p>Tidak ada hasil yang cocok</p>
              </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="previewModal" class="modal" aria-hidden="true">
  <div class="modal-card" role="dialog" aria-modal="true">
    <div class="modal-head">
      <span id="previewTitle">Pratinjau Dokumen</span>
      <button type="button" class="modal-close" id="closePreview">Tutup</button>
    </div>
    <div class="modal-body" id="previewBody"></div>
  </div>
</div>

<script>
const searchInput = document.getElementById("searchArsip");
const searchForm = document.getElementById("searchForm");
let searchTimer = null;

if (searchInput && searchForm) {
  searchInput.addEventListener("input", () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
      searchForm.submit();
    }, 300);
  });
}
</script>

<script>
const previewModal = document.getElementById("previewModal");
const previewBody = document.getElementById("previewBody");
const closePreview = document.getElementById("closePreview");

function openPreview(url) {
  const lower = url.toLowerCase();
  previewBody.innerHTML = "";
  if (lower.endsWith(".jpg") || lower.endsWith(".jpeg") || lower.endsWith(".png")) {
    const img = document.createElement("img");
    img.src = url;
    img.alt = "Pratinjau";
    previewBody.appendChild(img);
  } else {
    const iframe = document.createElement("iframe");
    iframe.src = url;
    iframe.title = "Pratinjau Dokumen";
    previewBody.appendChild(iframe);
  }
  previewModal.classList.add("show");
  previewModal.setAttribute("aria-hidden", "false");
}

function closePreviewModal() {
  previewModal.classList.remove("show");
  previewModal.setAttribute("aria-hidden", "true");
  previewBody.innerHTML = "";
}

document.addEventListener("click", (e) => {
  const btn = e.target.closest(".js-preview");
  if (btn) {
    e.preventDefault();
    openPreview(btn.dataset.preview);
  }
});

closePreview?.addEventListener("click", closePreviewModal);
previewModal?.addEventListener("click", (e) => {
  if (e.target === previewModal) closePreviewModal();
});
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && previewModal.classList.contains("show")) {
    closePreviewModal();
  }
});
</script>
</body>
</html>
