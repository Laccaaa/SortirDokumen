<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Klasifikasi Surat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 35px;
        }

        .header h1 {
            color: #667eea;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .required {
            color: #e74c3c;
        }

        select, input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
        }

        select:focus, input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        select:hover, input[type="text"]:hover {
            border-color: #b0b0b0;
        }

        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            left: -9999px;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            border: 2px dashed #667eea;
            border-radius: 10px;
            background: #f8f9ff;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            background: #eef1ff;
            border-color: #5568d3;
        }

        .file-upload-label svg {
            margin-right: 10px;
        }

        .file-name {
            margin-top: 10px;
            padding: 10px;
            background: #e8f5e9;
            border-radius: 8px;
            color: #2e7d32;
            font-size: 14px;
            display: none;
        }

        .file-name.show {
            display: block;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .validation-error {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

        .validation-error.show {
            display: block;
        }

        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
            color: #856404;
        }

        @media (max-width: 600px) {
            .container {
                padding: 25px;
            }

            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📬 Klasifikasi Surat</h1>
            <p>Aplikasi Manajemen Surat Masuk & Keluar</p>
        </div>

        <div class="info-box">
            ℹ️ Pastikan semua data telah terisi dengan benar sebelum mengirim
        </div>

        <form id="suratForm" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="jenisSurat">
                    Jenis Surat <span class="required">*</span>
                </label>
                <select id="jenisSurat" name="jenis_surat" required>
                    <option value="">-- Pilih Jenis Surat --</option>
                    <option value="masuk">📥 Surat Masuk</option>
                    <option value="keluar">📤 Surat Keluar</option>
                </select>
                <div class="validation-error" id="errorJenis">Pilih jenis surat terlebih dahulu</div>
            </div>

            <div class="form-group">
                <label for="nomorSurat">
                    Nomor Surat <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="nomorSurat" 
                    name="nomor_surat" 
                    placeholder="Contoh: 001/SM/XII/2024"
                    required
                >
                <div class="validation-error" id="errorNomor">Nomor surat tidak boleh kosong</div>
            </div>

            <div class="form-group">
                <label for="fileSurat">
                    Upload File Surat <span class="required">*</span>
                </label>
                <div class="file-upload">
                    <input 
                        type="file" 
                        id="fileSurat" 
                        name="file_surat" 
                        accept=".pdf,.doc,.docx"
                        required
                    >
                    <label for="fileSurat" class="file-upload-label">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#667eea" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span>Klik untuk memilih file (PDF, DOC, DOCX)</span>
                    </label>
                    <div class="file-name" id="fileName"></div>
                </div>
                <div class="validation-error" id="errorFile">File harus diupload</div>
            </div>

            <button type="submit" class="btn-submit">
                ✉️ Kirim Surat
            </button>
        </form>
    </div>

    <script>
        // Handle file upload display
        const fileInput = document.getElementById('fileSurat');
        const fileName = document.getElementById('fileName');

        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                fileName.textContent = `✓ File terpilih: ${file.name}`;
                fileName.classList.add('show');
            } else {
                fileName.classList.remove('show');
            }
        });

        // Form validation
        const form = document.getElementById('suratForm');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            
            // Validate jenis surat
            const jenisSurat = document.getElementById('jenisSurat');
            const errorJenis = document.getElementById('errorJenis');
            if (!jenisSurat.value) {
                errorJenis.classList.add('show');
                jenisSurat.style.borderColor = '#e74c3c';
                isValid = false;
            } else {
                errorJenis.classList.remove('show');
                jenisSurat.style.borderColor = '#e0e0e0';
            }
            
            // Validate nomor surat
            const nomorSurat = document.getElementById('nomorSurat');
            const errorNomor = document.getElementById('errorNomor');
            if (!nomorSurat.value.trim()) {
                errorNomor.classList.add('show');
                nomorSurat.style.borderColor = '#e74c3c';
                isValid = false;
            } else {
                errorNomor.classList.remove('show');
                nomorSurat.style.borderColor = '#e0e0e0';
            }
            
            // Validate file
            const errorFile = document.getElementById('errorFile');
            if (!fileInput.files.length) {
                errorFile.classList.add('show');
                isValid = false;
            } else {
                errorFile.classList.remove('show');
            }
            
            if (isValid) {
                // Here you would normally submit the form
                // For demo purposes, show success message
                alert('✓ Validasi berhasil! Data surat akan diproses.');
                
                // Uncomment the line below to actually submit the form
                // form.submit();
            }
        });
    </script>
</body>
</html>