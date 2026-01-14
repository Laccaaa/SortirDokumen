<?php
require_once "proses_tabel.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Daftar Arsip</title>

<style>
:root{
  /* ✅ Background 3 warna flat */
  --dark-bg: #1c2229;
  --purple-dark: #5b2a86;
  --purple-light:#8e6bbf;

  --text:#1f2a44;
  --muted:#667085;
  --line:#e6e8ef;
}

*{ box-sizing:border-box; }

html, body{
  width:100%;
  height:100%;
  margin:0;
}

/* ✅ body NO scroll, scroll ada di shell-body */
body{
  height:100vh;
  overflow:hidden;
  background: var(--dark-bg);
  position:relative;
  font-family: Inter, Arial, sans-serif;

  display:flex;
  justify-content:center;
  align-items:flex-start;
  padding:56px 20px;
}

/* ✅ 2 bidang ungu (3 warna bareng base dark) */
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
  width:100%;
  max-width:1180px;
  position:relative;
  z-index:2;
}

/* ✅ ini shell utama: fixed height -> isi bisa scroll */
.card{
  width:100%;
  background:#fff;
  border-radius:22px;
  box-shadow:
    0 10px 30px rgba(15,23,42,.14),
    0 30px 60px rgba(15,23,42,.12);

  /* kunci tinggi agar scroll jalan */
  height: calc(100vh - 112px); /* 56px top/bottom padding total = 112 */
  overflow:hidden;

  display:flex;
  flex-direction:column;
}

/* ✅ header tetap di atas */
.shell-head{
  padding:22px 22px 12px;
  border-bottom:1px solid #eef2f7;
  flex:0 0 auto;
}

/* ✅ bagian ini yang scroll */
.shell-body{
  padding:14px 22px 22px;
  overflow:auto;
  -webkit-overflow-scrolling: touch;
  flex:1 1 auto;
}

.header{
  display:flex;
  justify-content:space-between;
  gap:12px;
  flex-wrap:wrap;
}

.title h1{
  margin:0 0 6px;
  font-size:20px;
}
.sub{
  font-size:13px;
  color:var(--muted);
}

/* tombol */
.btns{
  display:flex;
  gap:10px;
  align-items:center;
}

a.btn{
  padding:10px 14px;
  border-radius:12px;
  text-decoration:none;
  font-weight:800;
  font-size:14px;
  background:#1f2a44;
  color:#fff;
  display:inline-flex;
  gap:8px;
  align-items:center;
}
a.btn.secondary{
  background:#eef2ff;
  color:#1f2a44;
  border:1px solid #d7ddff;
}

/* alerts */
.alert-err{
  margin-bottom:12px;
  padding:10px 12px;
  border-radius:12px;
  background:#fff5f5;
  border:1px solid #ffd0d0;
  color:#7a1f1f;
  font-size:13px;
  font-weight:700;
}
.alert-ok{
  margin-bottom:12px;
  padding:10px 12px;
  border-radius:12px;
  background:#f0fdf4;
  border:1px solid #bbf7d0;
  color:#166534;
  font-size:13px;
  font-weight:800;
}

/* search */
.search-row{
  display:flex;
  justify-content:space-between;
  gap:12px;
  flex-wrap:wrap;
  margin: 6px 0 12px;
}
.search{
  flex:1;
  min-width: 280px;
  display:flex;
  gap:10px;
  align-items:center;
}
.search input{
  width:100%;
  padding:11px 14px;
  border-radius:12px;
  border:1px solid #e5e7eb;
  outline:none;
  font-size:14px;
  background:#fff;
}
.search input:focus{
  border-color:#b7c3ff;
  box-shadow:0 0 0 4px rgba(99,102,241,.12);
}
.search button{
  padding:11px 14px;
  border-radius:12px;
  border:1px solid #d7ddff;
  background:#eef2ff;
  color:#1f2a44;
  font-weight:900;
  cursor:pointer;
}
a.clear{
  font-size:13px;
  color:#475569;
  text-decoration:none;
  padding:10px 12px;
  border-radius:12px;
  border:1px solid #e5e7eb;
  background:#fff;
}

/* table */
.table-wrap{
  margin-top:12px;
  border-radius:16px;
  overflow:hidden;
  border:1px solid #eef2f7;
  box-shadow:0 8px 24px rgba(15,23,42,.06);
}

table{
  width:100%;
  border-collapse:collapse;
  font-size:13px;
}

thead th{
  background:#f8fafc;
  padding:14px;
  border-bottom:1px solid #e5e7eb;
  font-size:12px;
  font-weight:900;
  color:#475569;
  white-space:nowrap;

  /* ✅ sticky biar keren pas scroll */
  position: sticky;
  top: 0;
  z-index: 3;
}

tbody td{
  padding:14px;
  border-bottom:1px solid #eef2f7;
  color:#1f2937;
  vertical-align:top;
}

tbody tr:nth-child(even){ background:#fafbff; }
tbody tr:hover{ background:#f1f5ff; }

td.muted{
  text-align:center;
  color:var(--muted);
}

/* aksi */
.actions{
  display:flex;
  gap:8px;
  flex-wrap:wrap;
  align-items:center;
}

a.btn-edit{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:8px 12px;
  border-radius:12px;
  text-decoration:none;
  font-weight:900;
  font-size:12px;
  background:#eef2ff;
  color:#1f2a44;
  border:1px solid #d7ddff;
}
a.btn-edit:hover{ filter:brightness(.98); }

.btn-del{
  border:1px solid #ffd0d0;
  background:#fff5f5;
  color:#7a1f1f;
  font-weight:900;
  font-size:12px;
  padding:8px 12px;
  border-radius:12px;
  cursor:pointer;
}
.btn-del:hover{ filter:brightness(.98); }
.btn-del:active{ transform: translateY(1px); }

/* ✅ Mobile */
@media (max-width:768px){
  body{
    padding:14px;
  }

  .card{
    height: calc(100vh - 28px);
    border-radius:18px;
  }

  .shell-head{
    padding:14px 14px 10px;
  }
  .shell-body{
    padding:12px 14px 14px;
  }

  .header{
    flex-direction:column;
    align-items:flex-start;
    gap:10px;
  }

  .title h1{ font-size:18px; line-height:1.15; }
  .sub{ font-size:12px; }

  .btns{
    width:100%;
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:10px;
  }
  a.btn{
    width:100%;
    justify-content:center;
    border-radius:14px;
  }

  .search-row{
    width:100%;
    flex-direction:column;
    gap:10px;
  }

  .search{
    width:100%;
    min-width:unset;
    display:grid;
    grid-template-columns: 1fr auto;
    gap:10px;
  }

  a.clear{
    width:100%;
    text-align:center;
    border-radius:14px;
  }

  /* mobile table -> card mode */
  thead{ display:none; }
  table, tbody, tr, td{ display:block; width:100%; }

  tr{
    border-bottom:1px solid #eef2f7;
    padding:12px 14px;
    background:#fff;
  }

  tbody tr:nth-child(even){ background:#fbfcff; }

  td{
    border:none;
    padding:10px 0;
    overflow-wrap:anywhere;
    word-break:break-word;
  }

  td::before{
    content: attr(data-label);
    display:block;
    font-size:12px;
    color:var(--muted);
    font-weight:900;
    margin-bottom:4px;
  }

  .actions{ width:100%; }
  a.btn-edit, .btn-del{
    width:100%;
    justify-content:center;
    padding:10px 12px;
    border-radius:14px;
  }
}
</style>
</head>

<body>
  <div class="wrap">
    <div class="card">

      <!-- ✅ HEAD -->
      <div class="shell-head">
        <div class="header">
          <div class="title">
            <h1>DAFTAR ARSIP YANG DIMUSNAHKAN</h1>
            <div class="sub">Stasiun Meteorologi Kelas I Juanda – Sidoarjo</div>
          </div>

          <div class="btns">
            <a class="btn secondary" href="input_arsip.php">➕ Form Input</a>
            <a class="btn primary" href="index.php">⬅️ Balik</a>
          </div>
        </div>
      </div>

      <!-- ✅ BODY (scroll) -->
      <div class="shell-body">

        <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
          <div class="alert-ok">✅ Data berhasil dihapus.</div>
        <?php elseif (!empty($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
          <div class="alert-ok">✅ Data berhasil diupdate.</div>
        <?php elseif (!empty($_GET['msg']) && $_GET['msg'] === 'created'): ?>
          <div class="alert-ok">✅ Data berhasil ditambahkan.</div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
          <div class="alert-err"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- SEARCH -->
        <div class="search-row">
          <form class="search" method="GET" action="">
            <input
              type="text"
              name="q"
              placeholder="Cari: kode, nama berkas, pencipta, uraian, tanggal, dll..."
              value="<?= htmlspecialchars($q ?? '') ?>"
            />
            <button type="submit">🔎 Cari</button>
          </form>

          <?php if (!empty($q)): ?>
            <a class="clear" href="tabel_arsip.php">✖ Reset</a>
          <?php endif; ?>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>No</th>
                <th>Kode Klasifikasi</th>
                <th>Nama Berkas</th>
                <th>No. Isi</th>
                <th>Pencipta</th>
                <th>No. Surat</th>
                <th>Uraian</th>
                <th>Tanggal</th>
                <th>Jumlah</th>
                <th>Tingkat</th>
                <th>Lokasi</th>
                <th>Keterangan</th>
                <th>Aksi</th>
              </tr>
            </thead>

            <tbody>
            <?php if (count($rows) === 0): ?>
              <tr>
                <td class="muted" colspan="13">
                  <?= !empty($q) ? "Data tidak ditemukan untuk pencarian: " . htmlspecialchars($q) : "Belum ada data" ?>
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; foreach ($rows as $r): ?>
              <?php $rowId = $r["id"]; ?>
              <tr>
                <td data-label="No"><?= $no++ ?></td>
                <td data-label="Kode Klasifikasi"><?= htmlspecialchars($r["kode_klasifikasi"] ?? "") ?></td>
                <td data-label="Nama Berkas"><?= htmlspecialchars($r["nama_berkas"] ?? "") ?></td>
                <td data-label="No. Isi"><?= htmlspecialchars($r["no_isi"] ?? "") ?></td>
                <td data-label="Pencipta"><?= htmlspecialchars($r["pencipta"] ?? "") ?></td>
                <td data-label="No. Surat"><?= htmlspecialchars($r["no_surat"] ?? "") ?></td>
                <td data-label="Uraian"><?= htmlspecialchars($r["uraian"] ?? "") ?></td>
                <td data-label="Tanggal"><?= htmlspecialchars($r["tanggal"] ?? "") ?></td>
                <td data-label="Jumlah"><?= htmlspecialchars($r["jumlah"] ?? "") ?></td>
                <td data-label="Tingkat"><?= htmlspecialchars($r["tingkat"] ?? "") ?></td>
                <td data-label="Lokasi"><?= htmlspecialchars($r["lokasi"] ?? "") ?></td>
                <td data-label="Keterangan"><?= htmlspecialchars($r["keterangan"] ?? "") ?></td>

                <td data-label="Aksi">
                  <div class="actions">
                    <a class="btn-edit" href="input_arsip.php?edit=<?= urlencode((string)$rowId) ?>">✏️ Edit</a>

                    <form method="POST" action="" onsubmit="return confirm('Yakin mau hapus data ini? 😬');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= htmlspecialchars((string)$rowId) ?>">
                      <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                      <button class="btn-del" type="submit">🗑️ Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</body>
</html>
