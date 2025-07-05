/**
 * @var {string} visitorUniqID - Ziyaretçinin benzersiz kimliği.
 */
class AssistantChat {
    /**
     * AssistantChat sınıfını başlatır.
     * @param {string} visitorUniqID - Ziyaretçinin benzersiz kimliği.
     */
    constructor(visitorUniqID,languageCode) {
        this.visitorUniqID = visitorUniqID;
        this.languageCode = languageCode;
        this.threadId = null;
        this.runId = null;
        this.checkMessagesInterval = null;
        this.lastMessageId = null;
        this.failedCheckCount = 0;
        this.checkIntervalTime = 750;
        this.MAX_IDLE_TIME = 1800000;
        this.chatOpenTime = Date.now();
        this.maxAttempts = 20;
        this.attemptCount = 0;
        this.promptToken = 0;
        this.completedToken = 0;
        this.totalTokens = 0;

        this.chatElement = document.getElementById('assistant-chat');
        this.chatMessagesElement = document.getElementById('chat-messages');
        this.userInputElement = document.getElementById('user-input');
        this.assistantIconElement = document.getElementById('assistant-icon');
        this.waitingAnimationElement = document.getElementById('waiting-animation');

        this.initializeChat();
    }

    /**
     * Sohbeti başlatır ve gerekli verileri yükler.
     */
    initializeChat() {
        this.loadMessages();
        this.loadThreadId();

        this.setupEventListeners();

        const chatOpen = localStorage.getItem('chatOpen');
        const chatMinimized = localStorage.getItem('chatMinimized');

        if (chatOpen === 'true') {
            this.chatElement.style.display = 'block';
        }
        else if (chatMinimized === 'true') {
            this.chatElement.style.display = 'none';
        }

        this.startIdleTimer(); // Boşta kalma zamanlayıcısını başlat

        this.loadMessageWaitingToBeSent();
    }

    /**
     * Merkezi loglama fonksiyonu.
     * @param {string} message - Log mesajı.
     * @param {string} level - Log seviyesi ('info', 'error', 'debug', 'warning').
     */
    log(message, level = 'info') {
        const timestamp = new Date().toISOString();
        switch(level) {
            case 'info':
                console.info(`[INFO] ${timestamp} - ${message}`);
                break;
            case 'error':
                console.error(`[ERROR] ${timestamp} - ${message}`);
                break;
            case 'debug':
                console.debug(`[DEBUG] ${timestamp} - ${message}`);
                break;
            case 'warning':
                console.warn(`[WARNING] ${timestamp} - ${message}`);
                break;
            default:
                console.log(`[LOG] ${timestamp} - ${message}`);
        }
    }

    /**
     * Merkezi fetch işlemi için yardımcı fonksiyon.
     * @param {string} url - İstek yapılacak URL.
     * @param {object} options - Fetch seçenekleri.
     * @returns {Promise<object>} - JSON olarak ayrıştırılmış yanıt.
     */
    async fetchData(url, options) {
        try {
            this.log(`Fetching data from ${url} with options: ${JSON.stringify(options)}`, 'debug');

            const response = await fetch(url, options);

            // HTTP yanıt durumunu kontrol et
            if (!response.ok) {
                const errorMessage = `Fetch error: ${response.status} ${response.statusText}`;
                this.log(errorMessage, 'error');
                console.error(errorMessage);  // Konsola hata bilgisi bastırma
                throw new Error(errorMessage);
            }

            try {
                const data = await response.json();
                this.log(`Data received from ${url}: ${JSON.stringify(data)}`, 'debug');
                return data;
            } catch (jsonError) {
                // JSON ayrıştırma hatasını yakalayarak konsola yazdırıyoruz
                const jsonErrorMessage = `JSON parsing error: ${jsonError.message}`;
                this.log(jsonErrorMessage, 'error');
                console.error(jsonErrorMessage);  // Konsola JSON ayrıştırma hatası bastırma
                throw new Error(jsonErrorMessage);
            }

        } catch (error) {
            // Genel hata yönetimi: Tüm hataları burada yakalayarak konsola detaylı olarak bastırıyoruz
            const generalErrorMessage = `General error fetching data from ${url}: ${error.message}`;
            this.log(generalErrorMessage, 'error');
            console.error(generalErrorMessage);  // Konsola genel hata bilgisi bastırma
            throw error;
        }
    }

    /**
     * Kullanıcının mesajını işler ve sunucuya iletir.
     * @param {string} userInput - Kullanıcının girdiği metin.
     */
    handleUserInput(userInput) {
        this.updateChatOpenTime();
        this.displayChatMessage('user', userInput);
        this.saveMessages();
        this.setUserInputState(true, 'Asistan düşünüyor...');

        if (!this.threadId) {
            this.createThread(userInput);
        } else {
            this.sendMessage(userInput,"user");
        }
        this.userInputElement.value = '';
    }

    /**
     * Kullanıcı mesajını ve asistan mesajını UI'ye ekler.
     * @param {string} role - Mesajın rolü ('user', 'assistant').
     * @param {string} message - Mesaj içeriği.
     */
    displayChatMessage(role, message) {
        const safeMessage = this.escapeHtml(message);
        this.chatMessagesElement.innerHTML += `<div><strong>${role === 'user' ? 'Ben' : 'Asistan'}:</strong> ${safeMessage}</div>`;
        this.chatMessagesElement.scrollTop = this.chatMessagesElement.scrollHeight;
        this.setUserInputState(false, 'Mesajınızı yazın...');
        //input focus yapalım
        this.userInputElement.focus();
        this.saveMessages();
    }

    /**
     * Kullanıcı girdi alanını devre dışı bırakır veya etkinleştirir.
     * @param {boolean} disable - Devre dışı bırakılacaksa true, etkinleştirilecekse false.
     * @param {string} placeholderText - Placeholder metni.
     */
    setUserInputState(disable, placeholderText) {
        this.userInputElement.disabled = disable;
        this.assistantIconElement.disabled = disable;
        this.userInputElement.placeholder = placeholderText;
        this.waitingAnimationElement.style.display = disable ? 'block' : 'none';
    }

    /**
     * Güvenli HTML ekleme için girdi temizleme.
     * @param {string} unsafe - Güvensiz kullanıcı girişi.
     * @returns {string} - Güvenli HTML.
     */
    escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    /**
     * Etkinlik dinleyicilerini ayarlar.
     */
    setupEventListeners() {
        this.assistantIconElement.addEventListener('click', () => {
            if (this.chatElement.style.display === '' || this.chatElement.style.display === 'none') {
                this.chatElement.style.display = 'block';
                localStorage.setItem('chatOpen', 'true');
            } else {
                this.chatElement.style.display = 'none';
                localStorage.setItem('chatOpen', 'false');
            }

            setTimeout(() => {
                this.userInputElement.focus();
            }, 10);
        });

        document.getElementById('assistant-close').addEventListener('click', () => {
            this.closeChat();
        });

        document.getElementById('assistant-minimize').addEventListener('click', (e) => {
            this.chatElement.style.display = 'none';
            localStorage.setItem('chatMinimized', 'true'); // Minimize durumunu kaydedin
        });

        this.userInputElement.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                let userInput = this.userInputElement.value;
                if (userInput.trim() !== '') {
                    this.handleUserInput(userInput);
                }
            }
        });

        // Kullanıcı etkinliklerini dinleyin
        ['click', 'mousemove', 'keydown', 'scroll'].forEach(event => {
            document.addEventListener(event, () => this.resetIdleTimer());
        });
    }

    /**
     * Kullanıcının etkinlik zamanını günceller.
     */
    updateChatOpenTime() {
        this.chatOpenTime = Date.now();
    }

    /**
     * Boşta kalma zamanlayıcısını başlatır.
     */
    startIdleTimer() {
        this.idleInterval = setInterval(() => {
            if (Date.now() - this.chatOpenTime > this.MAX_IDLE_TIME) {
                this.clearChatData();
                clearInterval(this.idleInterval);
            }
        }, 60000); // Her 1 dakikada bir kontrol eder
    }

    /**
     * Boşta kalma zamanlayıcısını sıfırlar.
     */
    resetIdleTimer() {
        this.chatOpenTime = Date.now();
    }

    /**
     * Sohbet verilerini temizler.
     */
    clearChatData() {
        localStorage.removeItem('chatMessages');
        localStorage.removeItem('threadId');
        localStorage.removeItem('chatOpen');
        this.threadId = null;
        this.lastMessageId = null;
        this.chatMessagesElement.innerHTML = '';
    }

    /**
     * Yeni bir sohbet dizisi oluşturur.
     * @param {string} prompt - Kullanıcının girdiği ilk mesaj.
     */
    async createThread(prompt) {
        this.log("Sohbet başlatılıyor...", 'info');
        try {
            const data = await this.fetchData('/?/control/AI/post/createThread', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    visitorUniqID: this.visitorUniqID,
                    languageCode: this.languageCode
                })
            });

            if (data.status === "success") {
                this.threadId = data.thread_id;
                this.saveThreadId(this.threadId);
                await this.sendMessage(prompt,"user");
            } else {
                this.log(`Yanıt hatası: ${data.message}`, 'error');
                this.handleError("Yanıt hatası: " + data.message);
            }
        } catch (error) {
            this.log(`Yanıt ayrıştırılamadı: ${error}`, 'error');
            this.handleError("Yanıt ayrıştırılamadı.");
        }
    }

    /**
     * Mesajı sunucuya gönderir.
     * @param {string} userInput - Kullanıcının girdiği mesaj.
     */
    async sendMessage(userInput,role="user") {
        this.attemptCount = 0;
        this.failedCheckCount = 0; // Başarısız deneme sayısını sıfırla

        try {
            this.log("sendMessage başladı", 'info');
            const data = await this.fetchData('/?/control/AI/post/sendMessage', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    visitorUniqID: this.visitorUniqID,
                    languageCode: this.languageCode,
                    threadId: this.threadId,
                    content: userInput,
                    role: role
                })
            });

            if (data.status === 'pending') {
                this.runId = data.run_id;
                this.messageId = data.message_id;

                // Run durumunu belirli aralıklarla kontrol et
                this.checkRunStatus();
            } else if (data.status === 'error') {
                this.handleError(data.message || 'Bir hata oluştu.');
            } else {
                this.handleError('Beklenmeyen bir durum oluştu.');
            }
        } catch (error) {
            this.log(`Hata oluştu: ${error}`, 'error');
            this.handleError('Mesaj gönderilirken bir hata oluştu.');
        }
    }

    async sendAssistantMessage(userInput,role="assistant") {
        this.attemptCount = 0;
        this.failedCheckCount = 0; // Başarısız deneme sayısını sıfırla

        try {
            this.log("sendAssistantMessage başladı", 'info');
            const data = await this.fetchData('/?/control/AI/post/sendMessage', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    visitorUniqID: this.visitorUniqID,
                    languageCode: this.languageCode,
                    threadId: this.threadId,
                    content: userInput,
                    role: role
                })
            });

            if (data.status === 'pending') {
                this.runId = data.run_id;
                this.messageId = data.message_id;
                console.log(data);
                // Run durumunu belirli aralıklarla kontrol et
                //this.checkRunStatus();
            } else if (data.status === 'error') {
                this.handleError(data.message || 'Bir hata oluştu.');
            } else {
                this.handleError('Beklenmeyen bir durum oluştu.');
            }
        } catch (error) {
            this.log(`Hata oluştu: ${error}`, 'error');
            this.handleError('Mesaj gönderilirken bir hata oluştu.');
        }
    }

    /**
     * Run durumunu kontrol eder ve tamamlandığında mesajı alır.
     */
    async checkRunStatus() {
        this.log("checkRunStatus başladı", 'info');

        // Deneme sayısını kontrol et
        if (this.attemptCount >= this.maxAttempts) {
            this.log('Yanıt oluşturulamadı, deneme süresi doldu.', 'error');
            this.handleError('Yanıt oluşturulamadı, deneme süresi doldu.');
            return;
        }

        try {
            // Run durumunu sunucudan kontrol edin
            const data = await this.fetchData('/?/control/AI/post/checkRunStatus', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    visitorUniqID: this.visitorUniqID,
                    languageCode: this.languageCode,
                    threadId: this.threadId,
                    messageId: this.messageId,
                    runId: this.runId
                })
            });

            console.log(data);
            if (data.status === 'success') {
                this.log('Run Status: Yanıt alındı, completed.', 'info');
                this.promptToken = data.promptToken;
                this.completedToken = data.completedToken;
                this.totalTokens = data.totalTokens;

                //data.action varsa
                if(data.systemAction){
                    switch(data.systemAction){
                        case "searchResult":
                            //this.saveMessageWaitingToBeSent(data.content);
                            this.displayChatMessage('assistant', data.content);
                            //window.location.href = data.referenceUrl;
                            break;
                        case "addMemberResult":
                            this.displayChatMessage('assistant', data.content);
                            break;
                        default:
                            this.displayChatMessage('assistant', data.content); // Yanıtı göster
                            break;
                    }
                }
                else{
                    this.displayChatMessage('assistant', data.content); // Yanıtı göster
                }
            } else if (data.status === 'queued' || data.status === 'in_progress' || data.status === 'pending') {
                this.log(`Run Status: Yanıt oluşturuluyor, ${data.status}...`, 'info');
                // Deneme sayısını arttır

                this.attemptCount++;
                var placeholderText = "Yanıt oluşturuluyor";
                //attemptCount sayısını kadar placeholder'ıa nokta ekle
                for(var i = 0; i < this.attemptCount; i++){
                    placeholderText += ".";
                }
                this.userInputElement.placeholder = placeholderText;

                setTimeout(() => {
                    this.checkRunStatus();
                }, this.checkIntervalTime);
            } else if (data.status === 'error') {
                this.log(`Run Status: Sunucudan hata yanıtı alındı: ${data.message}`, 'error');
                this.handleError(data.message || 'Bir hata oluştu.');
            } else {
                this.log(`Beklenmeyen durum: ${JSON.stringify(data)}`, 'error');
                this.handleError('Beklenmeyen bir durum oluştu.');
            }
        } catch (error) {
            this.log(`Run Status Hata oluştu: ${error}`, 'error');
            this.handleError('Run durumu kontrol edilirken bir hata oluştu.');
        }
    }


    /**
     * Yeni mesajları kontrol eder.
     */
    async checkForNewMessages() {
        try {
            this.log("checkForNewMessages başladı", 'info');
            const data = await this.fetchData('/?/control/AI/post/getMessageByThreadID', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    visitorUniqID: this.visitorUniqID,
                    languageCode: this.languageCode,
                    threadId: this.threadId,
                    runId: this.runId,
                    promptToken: this.promptToken,
                    completedToken: this.completedToken,
                    totalTokens: this.totalTokens,
                    lastMessageId: this.lastMessageId
                })
            });

            if (data.status === 'success' && data.content) {
                // Yanıt başarıyla alındı, interval durduruluyor
                this.log('checkForNewMessages: Yanıt alındı.', 'info');
                clearInterval(this.checkMessagesInterval);
                this.checkMessagesInterval = null;
                this.displayChatMessage('assistant', data.content);
            }
            else if (data.status === 'pending' || !data.content) {
                // Yanıt henüz hazır değil, tekrar kontrol edilecek
                this.failedCheckCount++;
                if (this.failedCheckCount >= 3) {
                    clearInterval(this.checkMessagesInterval);
                    this.checkMessagesInterval = null;
                    this.log('Mesaj alınamadı, kontrol sonlandırıldı.', 'error');
                    this.handleError('Mesaj alınamadı, kontrol sonlandırıldı.');
                }
                else {
                    clearInterval(this.checkMessagesInterval);
                    this.checkMessagesInterval = setInterval(() => {
                        this.checkForNewMessages();
                    }, this.checkIntervalTime);
                }
            }
            else if (data.status === 'waitingForNewMessages') {
                this.log(`Yeni mesaj yok`, 'info');
                clearInterval(this.checkMessagesInterval);
                this.checkMessagesInterval = null;
            }
            else {
                this.log(`Beklenmeyen durum: ${JSON.stringify(data)}`, 'error');
                this.handleError('Beklenmeyen bir durum oluştu.');
            }
        } catch (error) {
            this.log(`Yeni mesajları kontrol ederken hata oluştu: ${error}`, 'error');
            this.handleError('Yeni mesajları kontrol ederken bir hata oluştu.');
        }
    }

    /**
     * Asistanın yanıtını görüntüler.
     * @param {string} content - Asistanın yanıt metni.
     * @param {string} messageId - Mesajın benzersiz kimliği.
     */
    displayMessage(content, messageId) {
        if (this.lastMessageId === messageId) {
            this.log("Aynı mesaj tekrar alındı gösterilmiyor", 'warning');
            return;
        }

        this.lastMessageId = messageId; // Son mesaj ID'sini güncelle
        const cleanedMessage = this.cleanResponse(content);
        this.displayChatMessage('assistant', cleanedMessage);
        this.saveMessages();
        this.setUserInputState(false, 'Mesajınızı yazın...');
    }

    /**
     * Yanıt metnindeki özel formatlı metinleri temizler.
     * Örneğin, 【12:34 abc】 gibi metinleri kaldırır.
     * @param {string} responseText - Asistanın yanıt metni.
     * @returns {string} - Temizlenmiş yanıt metni.
     */
    cleanResponse(responseText) {
        if (typeof responseText !== 'string') {
            this.log('cleanResponse: responseText is not a string:', 'error');
            return '';
        }
        return responseText.replace(/【\d+:\d+[^】]+】/g, '');
    }

    /**
     * Hata durumunda çağrılır ve kullanıcıya bilgi verir.
     * @param {string} [errorMessage='Bir hata oluştu.'] - Hata mesajı.
     */
    handleError(errorMessage = 'Bir hata oluştu.') {
        this.failedCheckCount = 0;
        this.setUserInputState(false, 'Mesajınızı yazın...');
        this.displayChatMessage('assistant', errorMessage);
        this.saveMessages();
    }

    /**
     * Mesajları localStorage'a kaydeder.
     */
    saveMessages() {
        const messages = this.chatMessagesElement.innerHTML;
        localStorage.setItem('chatMessages', messages);
    }

    /**
     * Mesajları localStorage'dan yükler.
     */
    loadMessages() {
        const messages = localStorage.getItem('chatMessages');
        if (messages) {
            this.chatMessagesElement.innerHTML = messages;
            this.chatMessagesElement.scrollTop = this.chatMessagesElement.scrollHeight;
        }
    }

    /**
     * Thread ID'yi localStorage'a kaydeder.
     */
    saveThreadId() {
        localStorage.setItem('threadId', this.threadId);
    }

    /**
     * Thread ID'yi localStorage'dan yükler.
     */
    loadThreadId() {
        const savedThreadId = localStorage.getItem('threadId');
        if (savedThreadId) {
            this.threadId = savedThreadId;
        }
    }

    saveMessageWaitingToBeSent(message){
        localStorage.setItem('messageWaitingToBeSent', message);
    }
    loadMessageWaitingToBeSent(){
        const savedMessageWaitingToBeSent = localStorage.getItem('messageWaitingToBeSent');
        if (savedMessageWaitingToBeSent && this.threadId!=null) {
            //delete
            localStorage.removeItem('messageWaitingToBeSent');
            console.log("Bekleyen mesaj gönderilecek");
            this.sendMessage(savedMessageWaitingToBeSent);
        }
    }

    /**
     * Sohbet kapatılır.
     */
    closeChat() {
        this.chatElement.style.display = 'none';
        localStorage.setItem('chatOpen', 'false'); // Sohbetin kapalı olduğunu kaydedin
        localStorage.setItem('chatMinimized', 'true');

        // Eğer mesaj kontrol zamanlayıcısı varsa temizleyin
        if (this.checkMessagesInterval) {
            clearInterval(this.checkMessagesInterval);
            this.checkMessagesInterval = null;
        }

        // Gerekli yerel verileri temizle
        this.clearChatData();
    }
}
//visitorUniqID ana js dosyasında const olarak tanımlanmıştır. Asistan işlemlerinde bu kodlar dahil edilir.
//languageCode ana js dosyasında const olarak tanımlanmıştır. Asistan işlemlerinde bu kodlar dahil edilir.
new AssistantChat(visitorUniqID,languageCode);
