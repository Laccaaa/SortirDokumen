<?php

$dbhandle = require __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../auth/auth_check.php";

/**
 * ======================================================
 * GLOBAL CONNECTION (PDO SINGLETON)
 * ======================================================
 */
$GLOBALS['db_conn'] = null;

function getConnection()
{
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
function hitungFile($jenis = null, $tahun = null, $bulan = null, $kode = null, $subkode = null)
{
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
        $query .= " AND TRIM(kode_utama) ILIKE TRIM(?)";
        $params[] = $kode;
    }
    if ($subkode) {
        $query .= " AND TRIM(subkode) ILIKE TRIM(?)";
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
function getItems($path)
{
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
    if (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})/([^/]+)/([^/]+)$#', $path, $m)) {
        $jenis = ($m[1] === 'Surat Masuk') ? 'masuk' : 'keluar';

        $stmt = $conn->prepare("
            SELECT DISTINCT subkode FROM surat
            WHERE jenis_surat = ? AND tahun = ? AND bulan = ? AND kode_utama = ?
            AND NULLIF(TRIM(subkode), '') IS NOT NULL
            ORDER BY subkode
        ");
        $stmt->execute([$jenis, $m[2], $m[3], $m[4]]);

        $subRows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($subRows)) {
            foreach ($subRows as $subkode) {
                $items[] = [
                    'type' => 'folder',
                    'name' => $m[4] . '.' . $subkode,
                    'link' => "?path=$path/{$subkode}",
                    'count' => hitungFile($jenis, $m[2], $m[3], $m[4], $subkode)
                ];
            }
            return $items;
        }

        // Fallback: jika subkode kosong/null, tampilkan file langsung di level kode
        $stmtFiles = $conn->prepare("
            SELECT id_surat, nama_file, path_file FROM surat
            WHERE jenis_surat = ? AND tahun = ? AND bulan = ? AND kode_utama = ?
            AND NULLIF(TRIM(subkode), '') IS NULL
            ORDER BY nama_file
        ");
        $stmtFiles->execute([$jenis, $m[2], $m[3], $m[4]]);

        while ($row = $stmtFiles->fetch()) {
            $items[] = [
                'type' => 'file',
                'id' => $row['id_surat'],
                'name' => $row['nama_file'],
                'path_file' => $row['path_file']
            ];
        }
        return $items;
    }

    // LEVEL 5 - FILE
    if (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})/([^/]+)/([^/]+)/(.+)$#', $path, $m)) {
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
 * PARSE PATH KE FILTERS
 * ======================================================
 */
function parsePathFilters($path)
{
    $path = urldecode($path);
    $filters = [
        'jenis' => null,
        'tahun' => null,
        'bulan' => null,
        'kode' => null,
        'subkode' => null,
        'valid' => true
    ];

    if ($path === '') {
        return $filters;
    }

    if ($path === 'Surat Masuk' || $path === 'Surat Keluar') {
        $filters['jenis'] = ($path === 'Surat Masuk') ? 'masuk' : 'keluar';
        return $filters;
    }

    if (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})$#', $path, $m)) {
        $filters['jenis'] = ($m[1] === 'Surat Masuk') ? 'masuk' : 'keluar';
        $filters['tahun'] = (int)$m[2];
        return $filters;
    }

    if (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})/([^/]+)$#', $path, $m)) {
        $filters['jenis'] = ($m[1] === 'Surat Masuk') ? 'masuk' : 'keluar';
        $filters['tahun'] = (int)$m[2];
        $filters['bulan'] = $m[3];
        return $filters;
    }

    if (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})/([^/]+)/([^/]+)$#', $path, $m)) {
        $filters['jenis'] = ($m[1] === 'Surat Masuk') ? 'masuk' : 'keluar';
        $filters['tahun'] = (int)$m[2];
        $filters['bulan'] = $m[3];
        $filters['kode'] = $m[4];
        return $filters;
    }

    if (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})/([^/]+)/([^/]+)/(.+)$#', $path, $m)) {
        $filters['jenis'] = ($m[1] === 'Surat Masuk') ? 'masuk' : 'keluar';
        $filters['tahun'] = (int)$m[2];
        $filters['bulan'] = $m[3];
        $filters['kode'] = $m[4];
        $filters['subkode'] = $m[5];
        return $filters;
    }

    $filters['valid'] = false;
    return $filters;
}

/**
 * ======================================================
 * CARI FILE SECARA REKURSIF (SUBFOLDER)
 * ======================================================
 */
function searchFilesRecursive($path, $query)
{
    $filters = parsePathFilters($path);
    if (!$filters['valid']) {
        return [];
    }

    $conn = getConnection();
    $items = [];
    $queryNorm = strtolower($query);
    $jenisQuery = $queryNorm;
    if (strpos($queryNorm, 'surat masuk') !== false) {
        $jenisQuery = 'masuk';
    } elseif (strpos($queryNorm, 'surat keluar') !== false) {
        $jenisQuery = 'keluar';
    }
    $sql = "SELECT id_surat, nama_file, path_file, jenis_surat, tahun, bulan, kode_utama, subkode
            FROM surat WHERE 1=1";
    $params = [];

    if ($filters['jenis']) {
        $sql .= " AND jenis_surat = ?";
        $params[] = $filters['jenis'];
    }
    if ($filters['tahun']) {
        $sql .= " AND tahun = ?";
        $params[] = (int)$filters['tahun'];
    }
    if ($filters['bulan']) {
        $sql .= " AND bulan = ?";
        $params[] = $filters['bulan'];
    }
    if ($filters['kode']) {
        $sql .= " AND kode_utama = ?";
        $params[] = $filters['kode'];
    }
    if ($filters['subkode']) {
        $sql .= " AND subkode = ?";
        $params[] = $filters['subkode'];
    }

    $sql .= " AND (
        nama_file ILIKE ? OR
        kode_utama ILIKE ? OR
        subkode ILIKE ? OR
        bulan ILIKE ? OR
        CAST(tahun AS TEXT) ILIKE ? OR
        jenis_surat ILIKE ?
    )";
    $like = '%' . $query . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = '%' . $jenisQuery . '%';

    $sql .= "
        ORDER BY tahun DESC,
        CASE bulan
            WHEN 'Januari' THEN 1 WHEN 'Februari' THEN 2 WHEN 'Maret' THEN 3
            WHEN 'April' THEN 4 WHEN 'Mei' THEN 5 WHEN 'Juni' THEN 6
            WHEN 'Juli' THEN 7 WHEN 'Agustus' THEN 8 WHEN 'September' THEN 9
            WHEN 'Oktober' THEN 10 WHEN 'November' THEN 11 WHEN 'Desember' THEN 12
        END,
        kode_utama, subkode, nama_file
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    while ($row = $stmt->fetch()) {
        $jenisLabel = ($row['jenis_surat'] === 'masuk') ? 'Surat Masuk' : 'Surat Keluar';
        $folderPath = $jenisLabel . '/' . $row['tahun'] . '/' . $row['bulan'] . '/' . $row['kode_utama'] . '/' . $row['subkode'];
        $items[] = [
            'type' => 'file',
            'id' => $row['id_surat'],
            'name' => $row['nama_file'],
            'path_file' => $row['path_file'],
            'location' => $folderPath,
            'folder_link' => '?path=' . urlencode($folderPath)
        ];
    }

    return $items;
}

/**
 * ======================================================
 * FILTER OPTIONS & FILES
 * ======================================================
 */
function getFilterOptions($jenis = null, $tahun = null, $bulan = null)
{
    $conn = getConnection();

    $yearsSql = "SELECT DISTINCT tahun FROM surat WHERE 1=1";
    $monthsSql = "SELECT bulan FROM (
        SELECT DISTINCT bulan,
        CASE bulan
            WHEN 'Januari' THEN 1 WHEN 'Februari' THEN 2 WHEN 'Maret' THEN 3
            WHEN 'April' THEN 4 WHEN 'Mei' THEN 5 WHEN 'Juni' THEN 6
            WHEN 'Juli' THEN 7 WHEN 'Agustus' THEN 8 WHEN 'September' THEN 9
            WHEN 'Oktober' THEN 10 WHEN 'November' THEN 11 WHEN 'Desember' THEN 12
        END AS urut
        FROM surat WHERE 1=1";
    $subSql = "SELECT DISTINCT subkode FROM surat WHERE 1=1";
    $params = [];

    if ($jenis) {
        $yearsSql .= " AND jenis_surat = ?";
        $monthsSql .= " AND jenis_surat = ?";
        $subSql .= " AND jenis_surat = ?";
        $params[] = $jenis;
    }

    $yearsSql .= " ORDER BY tahun DESC";

    $yearStmt = $conn->prepare($yearsSql);
    $yearStmt->execute($params);
    $years = $yearStmt->fetchAll(PDO::FETCH_COLUMN);

    $monthParams = $params;
    if ($tahun) {
        $monthsSql .= " AND tahun = ?";
        $monthParams[] = (int)$tahun;
    }
    $monthsSql .= ") AS bulan_list ORDER BY urut";
    $monthStmt = $conn->prepare($monthsSql);
    $monthStmt->execute($monthParams);
    $months = $monthStmt->fetchAll(PDO::FETCH_COLUMN);

    $subParams = $params;
    if ($tahun) {
        $subSql .= " AND tahun = ?";
        $subParams[] = (int)$tahun;
    }
    if ($bulan) {
        $subSql .= " AND bulan = ?";
        $subParams[] = $bulan;
    }
    $subSql .= " ORDER BY subkode";
    $subStmt = $conn->prepare($subSql);
    $subStmt->execute($subParams);
    $subkodes = $subStmt->fetchAll(PDO::FETCH_COLUMN);

    return [
        'years' => $years,
        'months' => $months,
        'subkodes' => $subkodes
    ];
}

function getFilesByFilters($jenis = null, $tahun = null, $bulan = null, $subkode = null)
{
    $conn = getConnection();
    $items = [];
    $sql = "SELECT id_surat, nama_file, path_file, jenis_surat, tahun, bulan, kode_utama, subkode
            FROM surat WHERE 1=1";
    $params = [];

    if ($jenis) {
        $sql .= " AND jenis_surat = ?";
        $params[] = $jenis;
    }
    if ($tahun) {
        $sql .= " AND tahun = ?";
        $params[] = (int)$tahun;
    }
    if ($bulan) {
        $sql .= " AND bulan = ?";
        $params[] = $bulan;
    }
    if ($subkode) {
        $sql .= " AND subkode = ?";
        $params[] = $subkode;
    }

    $sql .= "
        ORDER BY tahun DESC,
        CASE bulan
            WHEN 'Januari' THEN 1 WHEN 'Februari' THEN 2 WHEN 'Maret' THEN 3
            WHEN 'April' THEN 4 WHEN 'Mei' THEN 5 WHEN 'Juni' THEN 6
            WHEN 'Juli' THEN 7 WHEN 'Agustus' THEN 8 WHEN 'September' THEN 9
            WHEN 'Oktober' THEN 10 WHEN 'November' THEN 11 WHEN 'Desember' THEN 12
        END,
        kode_utama, subkode, nama_file
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    while ($row = $stmt->fetch()) {
        $jenisLabel = ($row['jenis_surat'] === 'masuk') ? 'Surat Masuk' : 'Surat Keluar';
        $folderPath = $jenisLabel . '/' . $row['tahun'] . '/' . $row['bulan'] . '/' . $row['kode_utama'] . '/' . $row['subkode'];
        $items[] = [
            'type' => 'file',
            'id' => $row['id_surat'],
            'name' => $row['nama_file'],
            'path_file' => $row['path_file'],
            'location' => $folderPath,
            'folder_link' => '?path=' . $folderPath
        ];
    }

    return $items;
}

function getFilesByFiltersAndQuery($jenis = null, $tahun = null, $bulan = null, $subkode = null, $query = null)
{
    $conn = getConnection();
    $items = [];
    $sql = "SELECT id_surat, nama_file, path_file, jenis_surat, tahun, bulan, kode_utama, subkode
            FROM surat WHERE 1=1";
    $params = [];

    if ($jenis) {
        $sql .= " AND jenis_surat = ?";
        $params[] = $jenis;
    }
    if ($tahun) {
        $sql .= " AND tahun = ?";
        $params[] = (int)$tahun;
    }
    if ($bulan) {
        $sql .= " AND bulan = ?";
        $params[] = $bulan;
    }
    if ($subkode) {
        $sql .= " AND subkode = ?";
        $params[] = $subkode;
    }

    if ($query !== null && $query !== '') {
        $queryNorm = strtolower($query);
        $jenisQuery = $queryNorm;
        if (strpos($queryNorm, 'surat masuk') !== false) {
            $jenisQuery = 'masuk';
        } elseif (strpos($queryNorm, 'surat keluar') !== false) {
            $jenisQuery = 'keluar';
        }

        $sql .= " AND (
            nama_file ILIKE ? OR
            kode_utama ILIKE ? OR
            subkode ILIKE ? OR
            bulan ILIKE ? OR
            CAST(tahun AS TEXT) ILIKE ? OR
            jenis_surat ILIKE ?
        )";
        $like = '%' . $query . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = '%' . $jenisQuery . '%';
    }

    $sql .= "
        ORDER BY tahun DESC,
        CASE bulan
            WHEN 'Januari' THEN 1 WHEN 'Februari' THEN 2 WHEN 'Maret' THEN 3
            WHEN 'April' THEN 4 WHEN 'Mei' THEN 5 WHEN 'Juni' THEN 6
            WHEN 'Juli' THEN 7 WHEN 'Agustus' THEN 8 WHEN 'September' THEN 9
            WHEN 'Oktober' THEN 10 WHEN 'November' THEN 11 WHEN 'Desember' THEN 12
        END,
        kode_utama, subkode, nama_file
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    while ($row = $stmt->fetch()) {
        $jenisLabel = ($row['jenis_surat'] === 'masuk') ? 'Surat Masuk' : 'Surat Keluar';
        $folderPath = $jenisLabel . '/' . $row['tahun'] . '/' . $row['bulan'] . '/' . $row['kode_utama'] . '/' . $row['subkode'];
        $items[] = [
            'type' => 'file',
            'id' => $row['id_surat'],
            'name' => $row['nama_file'],
            'path_file' => $row['path_file'],
            'location' => $folderPath,
            'folder_link' => '?path=' . urlencode($folderPath)
        ];
    }

    return $items;
}

/**
 * ======================================================
 * VIEW FILE
 * ======================================================
 */
function viewFile($id)
{
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM surat WHERE id_surat = ?");
    $stmt->execute([(int)$id]);
    $file = $stmt->fetch();

    $path = $file['path_file'] ?? '';
    if ($path !== '' && $path[0] !== '/') {
        $path = __DIR__ . '/../' . ltrim($path, '/');
    }

    if (!$file || !is_file($path)) {
        die("File tidak ditemukan");
    }

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $mime = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png'
    ][$ext] ?? 'application/octet-stream';

    header("Content-Type: $mime");
    readfile($path);
}

/**
 * ======================================================
 * DOWNLOAD FILE
 * ======================================================
 */
function downloadFile($id)
{
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM surat WHERE id_surat = ?");
    $stmt->execute([(int)$id]);
    $file = $stmt->fetch();

    $path = $file['path_file'] ?? '';
    if ($path !== '' && $path[0] !== '/') {
        $path = __DIR__ . '/../' . ltrim($path, '/');
    }

    if (!$file || !is_file($path)) {
        die("File tidak ditemukan");
    }

    header("Content-Disposition: attachment; filename=\"{$file['nama_file']}\"");
    header("Content-Length: " . filesize($path));
    readfile($path);
}

/**
 * ======================================================
 * DELETE FILE
 * ======================================================
 */
function deleteFile($id, array $returnParams = [])
{
    $allowedReturnKeys = ['path', 'q', 'jenis', 'tahun', 'bulan', 'subkode'];
    $cleanReturn = [];
    foreach ($allowedReturnKeys as $k) {
        if (!array_key_exists($k, $returnParams)) continue;
        $v = trim((string)$returnParams[$k]);
        if ($v === '') continue;
        $cleanReturn[$k] = $v;
    }

    $redirect = '/SortirDokumen/pages/arsip.php';
    $toUrl = static function (string $msg) use ($redirect, $cleanReturn): string {
        $q = $cleanReturn;
        $q['msg'] = $msg;
        return $redirect . '?' . http_build_query($q);
    };

    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM surat WHERE id_surat = ?");
    $stmt->execute([(int)$id]);
    $file = $stmt->fetch();

    if (!$file) {
        header("Location: " . $toUrl('notfound'));
        exit;
    }

    $path = $file['path_file'] ?? '';
    if ($path !== '' && $path[0] !== '/') {
        $path = __DIR__ . '/../' . ltrim($path, '/');
    }

    if ($path && is_file($path)) {
        @unlink($path);
    }

    $del = $conn->prepare("DELETE FROM surat WHERE id_surat = ?");
    $del->execute([(int)$id]);

    header("Location: " . $toUrl('deleted'));
    exit;
}

function downloadFolderZip(string $path): void
{
    if (!class_exists('ZipArchive')) {
        http_response_code(500);
        exit('Fitur ZIP belum aktif di server. Hubungi administrator.');
    }

    $filters = parsePathFilters($path);
    if (!$filters['valid'] || $path === '' || $path === 'Surat Masuk' || $path === 'Surat Keluar') {
        http_response_code(403);
        exit('Tidak boleh ZIP di level ini.');
    }

    $conn = getConnection();
    $sql = "SELECT nama_file, path_file, tahun, bulan, kode_utama, subkode, jenis_surat
        FROM surat WHERE 1=1";
    $p = [];

    foreach (['jenis' => 'jenis_surat', 'tahun' => 'tahun', 'bulan' => 'bulan', 'kode' => 'kode_utama', 'subkode' => 'subkode'] as $k => $col) {
        if (!empty($filters[$k])) {
            $sql .= " AND $col = ?";
            $p[] = $filters[$k];
        }
    }

    $st = $conn->prepare($sql);
    $st->execute($p);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) {
        http_response_code(404);
        exit('Tidak ada file.');
    }

    $tmp = tempnam(sys_get_temp_dir(), 'zip_');
    $zip = new ZipArchive();
    $open = $zip->open($tmp, ZipArchive::OVERWRITE);
    if ($open !== true) {
        http_response_code(500);
        exit('Gagal membuat file ZIP sementara.');
    }

    foreach ($rows as $r) {
        $fp = $r['path_file'];
        if ($fp && $fp[0] !== '/') $fp = __DIR__ . '/../' . ltrim($fp, '/');
        if (is_file($fp)) {

            $inside = [];

            if (empty($filters['bulan']) && !empty($r['bulan'])) $inside[] = $r['bulan'];
            if (empty($filters['kode']) && !empty($r['kode_utama'])) $inside[] = $r['kode_utama'];
            if (empty($filters['subkode']) && !empty($r['subkode'])) $inside[] = $r['kode_utama'] . '.' . $r['subkode'];

            $insidePath = (count($inside) ? implode('/', $inside) . '/' : '') . ($r['nama_file'] ?: basename($fp));
            $zip->addFile($fp, $insidePath);
        }
    }
    $zip->close();

    $name = preg_replace('/[^A-Za-z0-9_\-]+/', '_', str_replace('/', '_', $path)) . '.zip';
    header('Content-Type: application/zip');
    header("Content-Disposition: attachment; filename=\"$name\"");
    header('Content-Length: ' . filesize($tmp));
    readfile($tmp);
    @unlink($tmp);
    exit;
}

function countFilesForPath(string $path): int
{
    $filters = parsePathFilters($path);
    if (!$filters['valid']) return 0;

    $conn = getConnection();
    $sql = "SELECT COUNT(*) FROM surat WHERE 1=1";
    $p = [];

    if (!empty($filters['jenis'])) {
        $sql .= " AND jenis_surat = ?";
        $p[] = $filters['jenis'];
    }
    if (!empty($filters['tahun'])) {
        $sql .= " AND tahun = ?";
        $p[] = (int)$filters['tahun'];
    }

    // PENTING: bulan pakai ILIKE/trim biar match walau beda spasi/case
    if (!empty($filters['bulan'])) {
        $sql .= " AND TRIM(bulan) ILIKE TRIM(?)";
        $p[] = $filters['bulan'];
    }

    if (!empty($filters['kode'])) {
        $sql .= " AND kode_utama = ?";
        $p[] = $filters['kode'];
    }
    if (!empty($filters['subkode'])) {
        $sql .= " AND subkode = ?";
        $p[] = $filters['subkode'];
    }

    $st = $conn->prepare($sql);
    $st->execute($p);
    return (int)$st->fetchColumn();
}
