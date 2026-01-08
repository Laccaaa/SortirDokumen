<?php
include "koneksi.php";

// Global connection
$GLOBALS['db_conn'] = null;

// Fungsi untuk mendapatkan koneksi (singleton pattern)
function getConnection() {
    if ($GLOBALS['db_conn'] === null) {
        $GLOBALS['db_conn'] = koneksiDB();
    }
    return $GLOBALS['db_conn'];
}

// Fungsi untuk menutup koneksi
function closeConnection() {
    if ($GLOBALS['db_conn'] !== null) {
        pg_close($GLOBALS['db_conn']);
        $GLOBALS['db_conn'] = null;
    }
}

// Fungsi untuk menghitung jumlah file
function hitungFile($jenis = null, $tahun = null, $bulan = null, $kode = null, $subkode = null) {
    $conn = getConnection();
    
    $query = "SELECT COUNT(*) as total FROM surat WHERE 1=1";
    
    if ($jenis) {
        $jenis = pg_escape_string($conn, $jenis);
        $query .= " AND jenis_surat = '$jenis'";
    }
    if ($tahun) {
        $query .= " AND tahun = " . (int)$tahun;
    }
    if ($bulan) {
        $bulan = pg_escape_string($conn, $bulan);
        $query .= " AND TRIM(bulan) ILIKE TRIM('$bulan')";
    }
    if ($kode) {
        $kode = pg_escape_string($conn, $kode);
        $query .= " AND kode_utama = '$kode'";
    }
    if ($subkode) {
        $subkode = pg_escape_string($conn, $subkode);
        $query .= " AND subkode = '$subkode'";
    }
    
    $result = pg_query($conn, $query);
    $row = pg_fetch_assoc($result);
    
    return $row['total'];
}

// Fungsi untuk mendapatkan daftar folder/file berdasarkan path
function getItems($path) {
    // Decode URL terlebih dahulu
    $path = urldecode($path);
    
    $conn = getConnection();
    $items = [];
    
    if ($path === '') {
        // Home: tampilkan Surat Masuk & Keluar
        $items[] = [
            'type' => 'folder',
            'name' => 'Surat Masuk',
            'link' => '?path=Surat Masuk',
            'count' => hitungFile('masuk')
        ];
        $items[] = [
            'type' => 'folder',
            'name' => 'Surat Keluar',
            'link' => '?path=Surat Keluar',
            'count' => hitungFile('keluar')
        ];
    }
    elseif ($path === 'Surat Masuk' || $path === 'Surat Keluar') {
        // Level 1: Tampilkan tahun
        $jenis = ($path === 'Surat Masuk') ? 'masuk' : 'keluar';
        $jenis_escaped = pg_escape_string($conn, $jenis);
        
        $query = "SELECT DISTINCT tahun FROM surat WHERE jenis_surat = '$jenis_escaped' AND tahun IS NOT NULL ORDER BY tahun DESC";
        $result = pg_query($conn, $query);
        
        while ($row = pg_fetch_assoc($result)) {
            $tahun = $row['tahun'];
            $items[] = [
                'type' => 'folder',
                'name' => $tahun,
                'link' => "?path=" . urlencode($path) . "/$tahun",
                'count' => hitungFile($jenis, $tahun)
            ];
        }
    }
    elseif (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})$#', $path, $matches)) {
        // Level 2: Tampilkan bulan
        $jenis = ($matches[1] === 'Surat Masuk') ? 'masuk' : 'keluar';
        $tahun = (int)$matches[2];
        
        $jenis_escaped = pg_escape_string($conn, $jenis);
        
        $query = "SELECT DISTINCT bulan, 
            CASE bulan 
                WHEN 'Januari' THEN 1 WHEN 'Februari' THEN 2 WHEN 'Maret' THEN 3 
                WHEN 'April' THEN 4 WHEN 'Mei' THEN 5 WHEN 'Juni' THEN 6 
                WHEN 'Juli' THEN 7 WHEN 'Agustus' THEN 8 WHEN 'September' THEN 9 
                WHEN 'Oktober' THEN 10 WHEN 'November' THEN 11 WHEN 'Desember' THEN 12
            END as bulan_order
            FROM surat WHERE jenis_surat = '$jenis_escaped' AND tahun = $tahun AND bulan IS NOT NULL 
            ORDER BY bulan_order";
        $result = pg_query($conn, $query);
        
        while ($row = pg_fetch_assoc($result)) {
            $bulan = $row['bulan'];
            $items[] = [
                'type' => 'folder',
                'name' => $bulan,
                'link' => "?path=" . urlencode($path) . "/" . urlencode($bulan),
                'count' => hitungFile($jenis, $tahun, $bulan)
            ];
        }
    }
    elseif (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})/(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)$#', $path, $matches)) {
        // Level 3: Tampilkan kode utama
        $jenis = ($matches[1] === 'Surat Masuk') ? 'masuk' : 'keluar';
        $tahun = (int)$matches[2];
        $bulan = $matches[3];
        
        $jenis_escaped = pg_escape_string($conn, $jenis);
        $bulan_escaped = pg_escape_string($conn, $bulan);
        
        $query = "SELECT DISTINCT kode_utama FROM surat 
                  WHERE jenis_surat = '$jenis_escaped' 
                  AND tahun = $tahun 
                  AND TRIM(bulan) ILIKE TRIM('$bulan_escaped') 
                  AND kode_utama IS NOT NULL 
                  ORDER BY kode_utama";
        $result = pg_query($conn, $query);
        
        while ($row = pg_fetch_assoc($result)) {
            $kode = $row['kode_utama'];
            $items[] = [
                'type' => 'folder',
                'name' => $kode,
                'link' => "?path=" . urlencode($path) . "/" . urlencode($kode),
                'count' => hitungFile($jenis, $tahun, $bulan, $kode)
            ];
        }
    }
    elseif (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})/(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)/([A-Z]+)$#', $path, $matches)) {
        // Level 4: Tampilkan subkode
        $jenis = ($matches[1] === 'Surat Masuk') ? 'masuk' : 'keluar';
        $tahun = (int)$matches[2];
        $bulan = $matches[3];
        $kode = $matches[4];
        
        $jenis_escaped = pg_escape_string($conn, $jenis);
        $bulan_escaped = pg_escape_string($conn, $bulan);
        $kode_escaped = pg_escape_string($conn, $kode);
        
        $query = "SELECT DISTINCT subkode FROM surat 
                  WHERE jenis_surat = '$jenis_escaped' 
                  AND tahun = $tahun 
                  AND TRIM(bulan) ILIKE TRIM('$bulan_escaped')
                  AND kode_utama = '$kode_escaped' 
                  AND subkode IS NOT NULL 
                  ORDER BY subkode";
        
        $result = pg_query($conn, $query);
        
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                $subkode = $row['subkode'];
                
                $items[] = [
                    'type' => 'folder',
                    'name' => $kode . '.' . $subkode, // Tampilkan dengan kode utama di depan
                    'link' => "?path=" . urlencode($path . "/" . $subkode),
                    'count' => hitungFile($jenis, $tahun, $bulan, $kode, $subkode)
                ];
            }
        }
    }
    elseif (preg_match('#^(Surat Masuk|Surat Keluar)/(\d{4})/(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)/([A-Z]+)/(.+)$#', $path, $matches)) {
        // Level 5: Tampilkan file
        $jenis = ($matches[1] === 'Surat Masuk') ? 'masuk' : 'keluar';
        $tahun = (int)$matches[2];
        $bulan = $matches[3];
        $kode = $matches[4];
        $subkode = $matches[5];
        
        $jenis_escaped = pg_escape_string($conn, $jenis);
        $bulan_escaped = pg_escape_string($conn, $bulan);
        $kode_escaped = pg_escape_string($conn, $kode);
        $subkode_escaped = pg_escape_string($conn, $subkode);
        
        $query = "SELECT id_surat, nama_file, path_file FROM surat 
                  WHERE jenis_surat = '$jenis_escaped' 
                  AND tahun = $tahun 
                  AND TRIM(bulan) ILIKE TRIM('$bulan_escaped')
                  AND kode_utama = '$kode_escaped' 
                  AND subkode = '$subkode_escaped' 
                  ORDER BY nama_file";
        $result = pg_query($conn, $query);
        
        while ($row = pg_fetch_assoc($result)) {
            $items[] = [
                'type' => 'file',
                'name' => $row['nama_file'],
                'id' => $row['id_surat'],
                'path_file' => $row['path_file']
            ];
        }
    }
    
    return $items;
}

// Fungsi untuk view file
function viewFile($id) {
    $conn = koneksiDB();
    $id = (int)$id;
    
    $query = "SELECT * FROM surat WHERE id_surat = $id";
    $result = pg_query($conn, $query);
    $file = pg_fetch_assoc($result);
    
    pg_close($conn);
    
    if ($file && file_exists($file['path_file'])) {
        $filepath = $file['path_file'];
        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        
        if ($ext === 'pdf') {
            header('Content-Type: application/pdf');
        } elseif (in_array($ext, ['jpg', 'jpeg'])) {
            header('Content-Type: image/jpeg');
        } elseif ($ext === 'png') {
            header('Content-Type: image/png');
        }
        
        readfile($filepath);
    } else {
        echo "File tidak ditemukan";
    }
}

// Fungsi untuk download file
function downloadFile($id) {
    $conn = koneksiDB();
    $id = (int)$id;
    
    $query = "SELECT * FROM surat WHERE id_surat = $id";
    $result = pg_query($conn, $query);
    $file = pg_fetch_assoc($result);
    
    pg_close($conn);
    
    if ($file && file_exists($file['path_file'])) {
        $filepath = $file['path_file'];
        $filename = $file['nama_file'];
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
    } else {
        echo "File tidak ditemukan";
    }
}
?>