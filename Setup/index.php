<?php
$template_json_path = __DIR__ . '/api-keys-template.json';
$template_json_content = file_exists($template_json_path) ? file_get_contents($template_json_path) : '{}';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticaret Sistemi Kurulum (Adım Adım)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .setup-container { max-width: 900px; margin: 2rem auto; }
        .json-textarea { min-height: 300px; font-family: 'Courier New', monospace; }
        .log-pre {
            background-color: #212529;
            color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
            font-size: 0.875em;
            display: none; /* Başlangıçta gizli */
        }
        .step-card {
            transition: all 0.3s ease-in-out;
        }
        .step-header {
            cursor: pointer;
        }
        .step-status i {
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container setup-container">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h3 class="mb-0">🚀 E-Ticaret Sistemi Kurulum Yöneticisi</h3>
                <small class="text-muted">Adım adım interaktif kurulum</small>
            </div>
            <div class="card-body">
                <form id="setupForm">
                    <!-- Form içeriği (önceki gibi sekmeli yapıda) -->
                    <ul class="nav nav-tabs" id="setupTabs" role="tablist">
                        <li class="nav-item" role="presentation"><button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button">Temel Bilgiler</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="db-tab" data-bs-toggle="tab" data-bs-target="#db" type="button">Veritabanları</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button">Servisler (JSON)</button></li>
                    </ul>
                    <div class="tab-content p-3 border border-top-0 rounded-bottom mb-4">
                         <!-- Temel Bilgiler -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Ana Domain</label>
                                    <input type="text" class="form-control" name="domain" id="domain" placeholder="ornek.com" required oninput="updateDatabaseFields()">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ek Domainler</label>
                                    <input type="text" class="form-control" name="domains" placeholder="alt.ornek.com,test.ornek.com">
                                    <small class="text-muted">Virgülle ayırın</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Şifreleme Anahtarı</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="keyCode" id="keyCode" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateRandomPassword('keyCode', 32)">🎲 Oluştur</button>
                                    </div>
                                    <small class="text-muted">32 karakter uzunluğunda güvenli anahtar (.env için APP_KEY)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Veritabanları -->
                        <div class="tab-pane fade" id="db" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-12"><h5>🗄️ Production Veritabanı (Uzak Sunucu)</h5></div>
                                <div class="col-md-6"><label class="form-label">Sunucu</label><input type="text" class="form-control" name="serverUrl" id="serverUrl" placeholder="localhost" value="localhost" required></div>
                                <div class="col-md-6"><label class="form-label">Veritabanı Adı</label><input type="text" class="form-control" name="databaseName" id="databaseName" required></div>
                                <div class="col-md-6"><label class="form-label">Kullanıcı Adı</label><input type="text" class="form-control" name="username" id="username" required></div>
                                <div class="col-md-6"><label class="form-label">Şifre</label><div class="input-group"><input type="password" class="form-control" name="password" id="password" required><button type="button" class="btn btn-outline-secondary" onclick="generateRandomPassword('password', 16)">🎲 Oluştur</button></div></div>

                                <hr class="my-4">

                                <div class="col-12"><h5>💻 Local Veritabanı (Geliştirme)</h5></div>
                                <div class="col-md-6"><label class="form-label">Sunucu</label><input type="text" class="form-control" name="localServerUrl" value="localhost"></div>
                                <div class="col-md-6"><label class="form-label">Veritabanı Adı</label><input type="text" class="form-control" name="localDatabaseName" id="localDatabaseName" required></div>
                                <div class="col-md-6"><label class="form-label">Kullanıcı Adı</label><input type="text" class="form-control" name="localUsername" value="root"></div>
                                <div class="col-md-6"><label class="form-label">Şifre</label><input type="password" class="form-control" name="localPassword" id="localPassword" placeholder="Boş bırakılabilir" value="Global2019*"></div>
                            </div>
                        </div>

                        <!-- Servisler (JSON) -->
                        <div class="tab-pane fade" id="services" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">⚙️ Servis Konfigürasyonları</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" >📋 Örnek Yükle</button>
                            </div>
                            <textarea class="form-control json-textarea" name="jsonConfig" id="jsonConfig" placeholder="Servis konfigürasyonlarınızı JSON formatında buraya yapıştırın..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kurulum Adımları -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">📋 Kurulum Adımları</h4>
                <button class="btn btn-success" id="runAllBtn">Tümünü Çalıştır</button>
            </div>
            <div class="card-body">
                <div id="stepsContainer"></div>
            </div>
        </div>

        <!-- Reset Butonu -->
        <div class="mt-4">
            <button type="button" id="resetButton" class="btn btn-danger btn-sm">
                <i class="fas fa-trash-alt"></i> Sistemi Sıfırla
            </button>
        </div>
    </div>

    <!-- Reset Onay Modal -->
    <div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="resetModalLabel">
                        <i class="fas fa-exclamation-triangle"></i> Sistem Sıfırlama Onayı
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>DİKKAT:</strong> Bu işlem geri alınamaz!
                    </div>
                    <p><strong>Bu işlem şunları silecek:</strong></p>
                    <ul>
                        <li>Plesk üzerindeki domain (eğer seçilirse)</li>
                        <li>Yerel veritabanı</li>
                        <li>.env dosyası</li>
                        <li>Debug log dosyaları</li>
                        <li>robots.txt dosyası</li>
                    </ul>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="deletePleskDomain" checked>
                            <label class="form-check-label" for="deletePleskDomain">
                                Plesk domain'ini de sil
                            </label>
                        </div>
                    </div>
                    <p><strong>Devam etmek için "RESET" yazın:</strong></p>
                    <input type="text" id="resetConfirmation" class="form-control" placeholder="RESET yazın">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" id="confirmResetButton" class="btn btn-danger" disabled>
                        <i class="fas fa-trash-alt"></i> Sistemi Sıfırla
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Log Modal -->
    <div class="modal fade" id="resetLogModal" tabindex="-1" aria-labelledby="resetLogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetLogModalLabel">
                        <i class="fas fa-list-alt"></i> Reset İşlem Logları
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="resetProgress" class="mb-3" style="display: none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 style="width: 100%">İşlem devam ediyor...</div>
                        </div>
                    </div>
                    <pre id="resetLogContent" class="bg-light p-3" style="max-height: 400px; overflow-y: auto;"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // --- Helper Fonksiyonlar (Mevcut) ---
        function generateRandomPassword(elementId, length) {
            const chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%^&*()_-=';
            let result = '';
            for (let i = 0; i < length; i++) { result += chars.charAt(Math.floor(Math.random() * chars.length)); }
            document.getElementById(elementId).value = result;
        }

        function updateDatabaseFields() {
            const domain = document.getElementById('domain').value.trim();
            if (domain) {
                const sanitizedDomain = domain.replace(/\./g, '_');
                document.getElementById('localDatabaseName').value = sanitizedDomain;
                document.getElementById('databaseName').value = sanitizedDomain;
                document.getElementById('username').value = domain;
            }
        }

        function loadSampleJson() {
            const sampleJson = <?php echo $template_json_content; ?>;
            document.getElementById('jsonConfig').value = JSON.stringify(sampleJson, null, 2);
        }

        // --- YENİ ADIM ADIM KURULUM MANTIĞI ---
        let setupSteps = [];

        document.addEventListener('DOMContentLoaded', async () => {
            // Form verilerini localStorage'dan yükle
            loadFormData();
            // Sayfa yüklendiğinde adımları API'den çek
            await loadSteps();
        });

        function loadFormData() {
             if(!document.getElementById('keyCode').value) {
                generateRandomPassword('keyCode', 32);
            }
            if(!document.getElementById('password').value) {
                generateRandomPassword('password', 16);
            }
            const savedData = localStorage.getItem('setupFormData');
            if (savedData) {
                const data = JSON.parse(savedData);
                const form = document.getElementById('setupForm');
                for (const key in data) {
                    if (form.elements[key]) {
                        form.elements[key].value = data[key];
                    }
                }
            }
            document.getElementById('setupForm').addEventListener('input', () => {
                const form = document.getElementById('setupForm');
                const formData = new FormData(form);
                const formObject = Object.fromEntries(formData.entries());
                localStorage.setItem('setupFormData', JSON.stringify(formObject));
            });
        }

        async function loadSteps() {
            try {
                // İlk yüklemede sadece adımları çek, config gönderme
                const payload = { action: 'getSteps' };
                const response = await fetchAPI(payload);
                if (response && response.status === 'success') {
                    setupSteps = response.steps;
                    renderSteps();
                }
            } catch (error) {
                console.error('Adımlar yüklenirken hata:', error);
                // Hata durumunda kullanıcıya bilgi ver
                document.getElementById('stepsContainer').innerHTML = 
                    '<div class="alert alert-warning">Adımlar yüklenemedi. Lütfen sayfayı yenileyin.</div>';
            }
        }

        function renderSteps() {
            const container = document.getElementById('stepsContainer');
            container.innerHTML = '';
            setupSteps.forEach(step => {
                container.innerHTML += `
                    <div class="card step-card mb-2" id="step-card-${step.index}">
                        <div class="card-header step-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#step-collapse-${step.index}">
                            <h6 class="mb-0">${step.index + 1}. ${step.name}</h6>
                            <span class="step-status" id="step-status-${step.index}">
                                <i class="bi bi-hourglass text-muted"></i>
                            </span>
                        </div>
                        <div id="step-collapse-${step.index}" class="collapse">
                            <div class="card-body">
                                <p>${step.description}</p>
                                <button class="btn btn-primary btn-sm run-step-btn" data-index="${step.index}">Çalıştır</button>
                                <div class="alert mt-3" id="step-result-${step.index}" style="display:none;"></div>
                                <pre class="log-pre" id="step-log-${step.index}"></pre>
                            </div>
                        </div>
                    </div>
                `;
            });

            document.querySelectorAll('.run-step-btn').forEach(btn => {
                btn.addEventListener('click', () => runSingleStep(parseInt(btn.dataset.index)));
            });
        }

        async function runSingleStep(index, isAutoRun = false) {
            const step = setupSteps[index];
            if (!step) return;

            const statusIcon = document.getElementById(`step-status-${step.index}`).querySelector('i');
            const resultDiv = document.getElementById(`step-result-${step.index}`);
            const logPre = document.getElementById(`step-log-${step.index}`);
            const runButton = document.querySelector(`.run-step-btn[data-index="${step.index}"]`);

            // Arayüzü güncelle: Çalışıyor
            step.status = 'running';
            statusIcon.className = 'spinner-border spinner-border-sm text-primary';
            runButton.disabled = true;
            runButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Çalışıyor...`;
            resultDiv.style.display = 'none';
            logPre.style.display = 'none';

            try {
                const payload = { action: 'runStep', stepIndex: index, ...getConfigs() };
                const result = await fetchAPI(payload);

                // Logları göster
                if (result.log && result.log.length > 0) {
                    logPre.textContent = result.log.join('\n');
                    logPre.style.display = 'block';
                }

                // Sonucu göster
                resultDiv.innerHTML = result.message;
                resultDiv.style.display = 'block';

                if (result.status === 'success') {
                    step.status = 'success';
                    statusIcon.className = 'bi bi-check-circle-fill text-success';
                    resultDiv.className = 'alert alert-success';
                    
                    // Butonu "Yeniden Çalıştır" durumuna getir
                    runButton.disabled = false;
                    runButton.className = 'btn btn-outline-secondary btn-sm run-step-btn';
                    runButton.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Yeniden Çalıştır';

                    // Otomatik modda ise bir sonraki adımı çalıştır
                    if (isAutoRun) {
                        const nextStep = setupSteps[index + 1];
                        if (nextStep) {
                            await runSingleStep(index + 1, true);
                        }
                    }
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                step.status = 'error';
                statusIcon.className = 'bi bi-x-circle-fill text-danger';
                resultDiv.className = 'alert alert-danger';
                resultDiv.textContent = `HATA: ${error.message}`;
                resultDiv.style.display = 'block';

                // Butonu "Tekrar Dene" durumuna getir
                runButton.disabled = false;
                runButton.className = 'btn btn-outline-danger btn-sm run-step-btn';
                runButton.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Tekrar Dene';
            }
        }

        document.getElementById('runAllBtn').addEventListener('click', async () => {
            if (confirm('Tüm kurulum adımları sırayla çalıştırılacak. Emin misiniz?')) {
                await runSingleStep(0, true);
            }
        });

        function getConfigs() {
            try {
                const form = document.getElementById('setupForm');
                if (!form) {
                    throw new Error('Form bulunamadı');
                }
                
                const formData = new FormData(form);
                const formObject = Object.fromEntries(formData.entries());
                
                let jsonConfig = {};
                if (formObject.jsonConfig && formObject.jsonConfig.trim()) {
                    try {
                        jsonConfig = JSON.parse(formObject.jsonConfig);
                    } catch (jsonError) {
                        console.warn('JSON parse hatası:', jsonError);
                        alert('JSON formatı hatalı! Lütfen kontrol edin.');
                        throw jsonError;
                    }
                }

                return { config: { form_data: formObject, json_config: jsonConfig } };
            } catch (error) {
                console.error('Config oluşturma hatası:', error);
                throw error;
            }
        }

        async function fetchAPI(payload) {
            try {
                const response = await fetch('setup-api.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify(payload)
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const result = await response.json();
                return result;
            } catch (error) {
                console.error('API çağrısı hatası:', error);
                throw error;
            }
        }

        // Form verilerini toplama fonksiyonu
        function collectFormData() {
            const formData = {};
            
            // Temel form alanları
            const formFields = [
                'domain', 'domains', 'keyCode', 'serverUrl', 'databaseName', 
                'username', 'password', 'localServerUrl', 'localDatabaseName', 
                'localUsername', 'localPassword'
            ];
            
            formFields.forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    formData[field] = element.value;
                }
            });
            
            // JSON config alanı
            const jsonConfigElement = document.getElementById('jsonConfig');
            if (jsonConfigElement) {
                formData['jsonConfig'] = jsonConfigElement.value;
            }
            
            return formData;
        }

        // Toast bildirimi gösterme fonksiyonu
        function showToast(message, type = 'info') {
            // Bootstrap toast veya basit alert kullan
            const alertClass = type === 'success' ? 'alert-success' : 
                              type === 'warning' ? 'alert-warning' : 
                              type === 'error' ? 'alert-danger' : 'alert-info';
            
            // Geçici toast div'i oluştur
            const toastDiv = document.createElement('div');
            toastDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            toastDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toastDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(toastDiv);
            
            // 5 saniye sonra otomatik kaldır
            setTimeout(() => {
                if (toastDiv.parentNode) {
                    toastDiv.parentNode.removeChild(toastDiv);
                }
            }, 5000);
        }

        // Reset butonu event listener
        document.getElementById('resetButton').addEventListener('click', function() {
            // Form verilerini modal'a aktar
            populateResetModal();
            new bootstrap.Modal(document.getElementById('resetModal')).show();
        });

        // Reset onay input kontrol
        document.getElementById('resetConfirmation').addEventListener('input', function(e) {
            const confirmButton = document.getElementById('confirmResetButton');
            confirmButton.disabled = e.target.value !== 'RESET';
        });

        // Reset onay butonu
        document.getElementById('confirmResetButton').addEventListener('click', function() {
            performReset();
        });

        function populateResetModal() {
            // Mevcut form verilerini al
            const formData = collectFormData();
            
            // Modal'da kullanılmak üzere global değişkene kaydet
            window.resetFormData = formData;
        }

        function performReset() {
            // Reset modal'ını kapat
            bootstrap.Modal.getInstance(document.getElementById('resetModal')).hide();
            
            // Log modal'ını aç
            const logModal = new bootstrap.Modal(document.getElementById('resetLogModal'));
            logModal.show();
            
            // Progress bar'ı göster
            document.getElementById('resetProgress').style.display = 'block';
            document.getElementById('resetLogContent').textContent = 'Reset işlemi başlatılıyor...\n';
            
            // Form data hazırla
            const resetData = {
                ...window.resetFormData,
                deletePleskDomain: document.getElementById('deletePleskDomain').checked
            };
            
            // AJAX ile reset işlemini başlat
            fetch('safe_reset.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(resetData)
            })
            .then(response => response.json())
            .then(data => {
                // Progress bar'ı gizle
                document.getElementById('resetProgress').style.display = 'none';
                
                // Log içeriğini göster
                document.getElementById('resetLogContent').textContent = data.log || 'Log bulunamadı';
                
                // Başarı/hata durumuna göre mesaj göster
                if (data.status === 'success') {
                    showToast('Reset işlemi başarıyla tamamlandı!', 'success');
                } else if (data.status === 'warning') {
                    showToast('Reset işlemi tamamlandı ancak bazı uyarılar var.', 'warning');
                } else {
                    showToast('Reset işlemi sırasında hata oluştu!', 'error');
                }
            })
            .catch(error => {
                // Progress bar'ı gizle
                document.getElementById('resetProgress').style.display = 'none';
                
                document.getElementById('resetLogContent').textContent = 'AJAX Hatası: ' + error.message;
                showToast('Reset işlemi sırasında bağlantı hatası oluştu!', 'error');
            });
        }

    </script>
</body>
</html>
