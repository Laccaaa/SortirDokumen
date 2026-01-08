<?php
$path = $_GET['path'] ?? '';
$parts = array_filter(explode('/', $path));
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Arsip Dokumen</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    background: linear-gradient(135deg, #4a6cf7, #6fb1c8);
    min-height: 100vh;
    padding: 20px;
}

.container {
    max-width: 1000px;
    margin: 0 auto;
    background: white;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
}

.header {
    background: linear-gradient(135deg, #4a6cf7, #6fb1c8);
    color: white;
    padding: 30px;
}

.header h1 {
    font-size: 28px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.breadcrumb {
    background: #f8f9fa;
    padding: 16px 30px;
    border-bottom: 1px solid #e9ecef;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.breadcrumb a {
    color: #4a6cf7;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
    padding: 4px 8px;
    border-radius: 6px;
}

.breadcrumb a:hover {
    background: #4a6cf7;
    color: white;
}

.breadcrumb .separator {
    color: #adb5bd;
    font-weight: 300;
}

.content {
    padding: 30px;
}

.list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.item {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
    background: white;
}

.item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 108, 247, 0.15);
    border-color: #4a6cf7;
}

.icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-right: 16px;
    border-radius: 10px;
    flex-shrink: 0;
}

.icon.folder {
    background: linear-gradient(135deg, #ffd89b 0%, #ff9a5a 100%);
}

.icon.file {
    background: linear-gradient(135deg, #a8edea 0%, #5e72e4 100%);
}

.name {
    flex: 1;
    font-size: 16px;
    color: #2d3748;
    font-weight: 500;
}

.name a {
    color: inherit;
    text-decoration: none;
}

.actions {
    display: flex;
    gap: 8px;
    margin-left: 16px;
}

.btn {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 18px;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-view {
    background: #e3f2fd;
    color: #2196f3;
}

.btn-view:hover {
    background: #2196f3;
    color: white;
    transform: scale(1.1);
}

.btn-download {
    background: #e8f5e9;
    color: #4caf50;
}

.btn-download:hover {
    background: #4caf50;
    color: white;
    transform: scale(1.1);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #adb5bd;
}

.empty-state-icon {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
}

@media (max-width: 768px) {
    body {
        padding: 10px;
    }
    
    .header {
        padding: 20px;
    }
    
    .header h1 {
        font-size: 22px;
    }
    
    .content {
        padding: 20px;
    }
    
    .item {
        padding: 12px 16px;
    }
    
    .icon {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
    
    .name {
        font-size: 14px;
    }
}
</style>
</head>

<body>
<div class="container">

<div class="header">
    <h1>📁 Arsip Dokumen</h1>
</div>

<!-- BREADCRUMB -->
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

<div class="content">
    <div class="list">

        <!-- CONTOH FOLDER -->
        <?php if ($path === ''): ?>
            <div class="item">
                <div class="icon folder">📁</div>
                <div class="name">
                    <a href="?path=2024">2024</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($path === '2024'): ?>
            <div class="item">
                <div class="icon folder">📁</div>
                <div class="name">
                    <a href="?path=2024/VI">VI (Juni)</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($path === '2024/VI'): ?>
            <div class="item">
                <div class="icon folder">📁</div>
                <div class="name">
                    <a href="?path=2024/VI/IJ">IJ</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($path === '2024/VI/IJ'): ?>
            <div class="item">
                <div class="icon folder">📁</div>
                <div class="name">
                    <a href="?path=2024/VI/IJ/PL.01.00">PL.01.00</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($path === '2024/VI/IJ/PL.01.00'): ?>
            <div class="item">
                <div class="icon file">📄</div>
                <div class="name">IJ.00.00-019.pdf</div>
                <div class="actions">
                    <a href="#" class="btn btn-view" title="Lihat">👁️</a>
                    <a href="#" class="btn btn-download" title="Unduh">⬇️</a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

</div>
</body>
</html>