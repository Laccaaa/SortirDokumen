<?php
require_once "koneksi.php";

/**
 * ======================================================
 * GLOBAL CONNECTION (PDO SINGLETON)
 * ======================================================
 */
$GLOBALS['db_conn'] = null;

function getConnection() {
    if ($GLOBALS['db_conn'] === null) {
        global $dbhandle;
        $GLOBALS['db_conn'] = $dbhandle;
    }
    return $GLOBALS['db_conn'];
}

/**
 * ======================================================
 * HITUNG JUMLAH FILE
 * ======================================================
 */
function hitungFile($jenis = null, $tahun = null, $bulan = null, $kode = null, $subkode = null) {
    $conn = getConnection();
    $query = "SELECT COUNT(*) AS total FROM surat WHERE 1=1";
    $params = [];

    if ($jenis) {
        $query .= " AND jenis_surat = ?";
        $params[] = $jenis;
    }
    if ($tahun) {
        $query .= " AND tahun = ?";
        $params[] = (int)$tahun;
    }
    if ($bulan) {
        $query .= " AND TRIM(bulan) ILIKE TRIM(?)";
        $params[] = $bulan;
    }
    if ($kode) {
        $query .= " AND kode_utama = ?";
        $params[] = $kode;
    }
    if ($subkode) {
        $query .= " AND subkode = ?";
        $params[] = $subkode;
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

/**
 * ======================================================
 * AMBIL FOLDER / FILE BERDASARKAN PATH
 * ======================================================
 */
function getItems($path) {
    $path = urldecode($path);
    $conn = getConnection();
    $items = [];

    // HOME
    if ($path === '') {
        return [
            [
                'type' => 'folder',
                'name' => 'Surat Masuk',
                'link' => '?path=Surat Masuk',
                'count' => hitungFile('masuk')
            ],
            [
                'type' => 'folder',
                'name' => 'Surat Keluar',
                'link' => '?path=Surat Keluar',
                'count' => hitungFile('keluar')
            ]
        ];
    }

    // LEVEL 1 - TAHUN
    if ($path === 'Surat Masuk' || $path === 'Surat Keluar') {
        $jenis = ($path === 'Surat Masuk') ? 'masuk' : 'keluar';
        $stmt = $conn->prepare(
            "SELECT DISTINCT tahun FROM surat WHERE jenis_surat = ? ORDER BY tahun DESC"
        );
        $stmt->execute([$jenis]);

        while ($row = $stmt->fetch()) {
            $items[] = [
                'type' => 'folder',
                'name' => $row['tahun'],
                'link' => "?path=$path/{$row['tahun']}",
                'count' => hitungFile($jenis, $row['tahun'])
            ];
        }
        return $items;
    }

    // LEVEL 2 - BULAN
    if (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})$#', $path, $m)) {
        $jenis = ($m[1] === 'Surat Masuk') ? 'masuk' : 'keluar';
        $tahun = (int)$m[2];

        $stmt = $conn->prepare("
            SELECT DISTINCT bulan,
            CASE bulan
                WHEN 'Januari' THEN 1 WHEN 'Februari' THEN 2 WHEN 'Maret' THEN 3
                WHEN 'April' THEN 4 WHEN 'Mei' THEN 5 WHEN 'Juni' THEN 6
                WHEN 'Juli' THEN 7 WHEN 'Agustus' THEN 8 WHEN 'September' THEN 9
                WHEN 'Oktober' THEN 10 WHEN 'November' THEN 11 WHEN 'Desember' THEN 12
            END AS urut
            FROM surat
            WHERE jenis_surat = ? AND tahun = ?
            ORDER BY urut
        ");
        $stmt->execute([$jenis, $tahun]);

        while ($row = $stmt->fetch()) {
            $items[] = [
                'type' => 'folder',
                'name' => $row['bulan'],
                'link' => "?path=$path/{$row['bulan']}",
                'count' => hitungFile($jenis, $tahun, $row['bulan'])
            ];
        }
        return $items;
    }

    // LEVEL 3 - KODE
    if (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})/([^/]+)$#', $path, $m)) {
        $jenis = ($m[1] === 'Surat Masuk') ? 'masuk' : 'keluar';

        $stmt = $conn->prepare("
            SELECT DISTINCT kode_utama FROM surat
            WHERE jenis_surat = ? AND tahun = ? AND bulan = ?
            ORDER BY kode_utama
        ");
        $stmt->execute([$jenis, $m[2], $m[3]]);

        while ($row = $stmt->fetch()) {
            $items[] = [
                'type' => 'folder',
                'name' => $row['kode_utama'],
                'link' => "?path=$path/{$row['kode_utama']}",
                'count' => hitungFile($jenis, $m[2], $m[3], $row['kode_utama'])
            ];
        }
        return $items;
    }

    // LEVEL 4 - SUBKODE
    if (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})/([^/]+)/([A-Z]+)$#', $path, $m)) {
        $jenis = ($m[1] === 'Surat Masuk') ? 'masuk' : 'keluar';

        $stmt = $conn->prepare("
            SELECT DISTINCT subkode FROM surat
            WHERE jenis_surat = ? AND tahun = ? AND bulan = ? AND kode_utama = ?
            ORDER BY subkode
        ");
        $stmt->execute([$jenis, $m[2], $m[3], $m[4]]);

        while ($row = $stmt->fetch()) {
            $items[] = [
                'type' => 'folder',
                'name' => $m[4] . '.' . $row['subkode'],
                'link' => "?path=$path/{$row['subkode']}",
                'count' => hitungFile($jenis, $m[2], $m[3], $m[4], $row['subkode'])
            ];
        }
        return $items;
    }

    // LEVEL 5 - FILE
    if (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})/([^/]+)/([A-Z]+)/(.+)$#', $path, $m)) {
        $jenis = ($m[1] === 'Surat Masuk') ? 'masuk' : 'keluar';

        $stmt = $conn->prepare("
            SELECT id_surat, nama_file, path_file FROM surat
            WHERE jenis_surat = ? AND tahun = ? AND bulan = ?
            AND kode_utama = ? AND subkode = ?
            ORDER BY nama_file
        ");
        $stmt->execute([$jenis, $m[2], $m[3], $m[4], $m[5]]);

        while ($row = $stmt->fetch()) {
            $items[] = [
                'type' => 'file',
                'id' => $row['id_surat'],
                'name' => $row['nama_file'],
                'path_file' => $row['path_file']
            ];
        }
        return $items;
    }

    return [];
}

/**
 * ======================================================
 * VIEW FILE
 * ======================================================
 */
function viewFile($id) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM surat WHERE id_surat = ?");
    $stmt->execute([(int)$id]);
    $file = $stmt->fetch();

    if (!$file || !file_exists($file['path_file'])) {
        die("File tidak ditemukan");
    }

    $ext = strtolower(pathinfo($file['path_file'], PATHINFO_EXTENSION));
    $mime = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png'
    ][$ext] ?? 'application/octet-stream';

    header("Content-Type: $mime");
    readfile($file['path_file']);
}

/**
 * ======================================================
 * DOWNLOAD FILE
 * ======================================================
 */
function downloadFile($id) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM surat WHERE id_surat = ?");
    $stmt->execute([(int)$id]);
    $file = $stmt->fetch();

    if (!$file || !file_exists($file['path_file'])) {
        die("File tidak ditemukan");
    }

    header("Content-Disposition: attachment; filename=\"{$file['nama_file']}\"");
    header("Content-Length: " . filesize($file['path_file']));
    readfile($file['path_file']);
}