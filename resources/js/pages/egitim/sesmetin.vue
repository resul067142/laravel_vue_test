<template>
    <div :class="['app-container', theme]">
        <!-- Tema Seçimi -->
        <div class="theme-toggle">
            <button @click="toggleTheme" aria-label="Temayı Değiştir">
                {{ theme === 'light' ? '🌙 Gölge Modu' : '☀️ Açık Mod' }}
            </button>
        </div>

        <!-- Kullanıcı Kimlik Doğrulama -->
        <div class="auth-section">
            <div v-if="!isAuthenticated">
                <h2>Giriş Yap</h2>
                <form @submit.prevent="login">
                    <input type="text" v-model="loginForm.username" placeholder="Kullanıcı Adı" required />
                    <input type="password" v-model="loginForm.password" placeholder="Şifre" required />
                    <button type="submit">Giriş Yap</button>
                </form>
            </div>
            <div v-else>
                <h2>Hoş Geldiniz, {{ user.username }}</h2>
                <button @click="logout">Çıkış Yap</button>
            </div>
        </div>

        <!-- Sürükle ve Bırak Yükleme Alanı -->
        <div
            class="dropzone"
            @dragover.prevent
            @drop.prevent="handleDrop"
            :class="{ 'is-dragover': isDragOver }"
            @dragenter="isDragOver = true"
            @dragleave="isDragOver = false"
        >
            <p>Sürükleyip bırakın veya tıklayın</p>
            <input
                type="file"
                @change="handleAudioUpload"
                accept="audio/*"
                multiple
                hidden
                ref="fileInput"
            />
            <button @click="triggerFileSelect" class="select-button">Dosya Seç</button>
        </div>

        <!-- Ses Dosyası Yükleme Bölümü -->
        <div class="upload-container">
            <h1>SES DOSYASINI METİN DOSYASINA DÖNÜŞTÜRME</h1>
            <hr>
            <div class="upload-section">
                <button @click="triggerFileSelect" class="select-button">Dosya Seç</button>
                <select v-model="selectedLanguage" aria-label="Transkripsiyon Dili Seçin">
                    <option value="tr-TR">Türkçe</option>
                    <option value="ku-TR">Kürtçe</option>
                    <option value="zaz-TR">Zazaca</option>
                    <option value="ar-TR">Arapça</option>
                    <option value="ce-TR">Çerkesçe</option>
                    <option value="el-TR">Yunanca</option>
                    <option value="hy-TR">Ermenice</option>
                    <option value="ka-TR">Gürcüce</option>
                    <option value="lzz-TR">Lazca</option>
                    <!-- Diğer dilleri buraya ekleyebilirsiniz -->
                </select>
                <button
                    @click="transcribeAudio"
                    class="transcribe-button"
                    :disabled="!audioFiles.length || isLoading || !isAuthenticated"
                    aria-label="Ses dosyasını metine dönüştür"
                >
                    {{ isLoading ? 'Dönüştürülüyor...' : 'Ses Dosyasını Metine Dönüştür' }}
                </button>
            </div>
            <div v-if="errorMessage" class="error-message">
                {{ errorMessage }}
            </div>
        </div>

        <!-- İlerleme Çubuğu -->
        <div v-if="isLoading" class="progress-bar">
            <div class="progress" :style="{ width: progress + '%' }"></div>
        </div>

        <!-- Transkripsiyon Sonucu -->
        <div v-if="transcription" class="transcription-result">
            <h3>Transkripsiyon:</h3>
            <textarea v-model="transcription" class="transcription-textarea" aria-label="Transkripsiyon Metni"></textarea>
            <div class="actions">
                <button @click="copyToClipboard" aria-label="Metni Panoya Kopyala">📋 Kopyala</button>
                <button @click="downloadTranscription" aria-label="Metni İndir">⬇️ İndir</button>
                <button @click="summarizeTranscription" aria-label="Metni Özetle">📝 Özeti Al</button>
                <button @click="shareTranscription" aria-label="Metni Paylaş">📤 Paylaş</button>
                <button @click="exportAsPDF" aria-label="PDF Olarak İndir">📄 PDF Olarak İndir</button>
            </div>
            <audio v-if="audioUrl" :src="audioUrl" controls aria-label="Yüklenen Ses Dosyası"></audio>
        </div>

        <!-- Metin Düzenleme Araçları -->
        <div v-if="transcription" class="editor-tools">
            <button @click="boldText" aria-label="Kalın Metin">B</button>
            <button @click="italicText" aria-label="İtalik Metin">I</button>
            <button @click="underlineText" aria-label="Altı Çizili Metin">U</button>
        </div>

        <!-- Transkripsiyon Geçmişi -->
        <div v-if="transcriptionHistory.length" class="history-container">
            <h2>Transkripsiyon Geçmişi</h2>
            <ul>
                <li v-for="(item, index) in transcriptionHistory" :key="index">
                    <p><strong>Dosya:</strong> {{ item.fileName }}</p>
                    <p><strong>Dil:</strong> {{ getLanguageName(item.language) }}</p>
                    <p><strong>Transkripsiyon:</strong> {{ item.transcription }}</p>
                    <audio v-if="item.audioUrl" :src="item.audioUrl" controls aria-label="Geçmiş Ses Dosyası"></audio>
                    <button @click="deleteHistory(index)" aria-label="Geçmişi Sil">🗑️ Sil</button>
                    <hr>
                </li>
            </ul>
        </div>

        <!-- Bildirimler -->
        <div v-if="notification.message" :class="['notification', notification.type]" role="alert" aria-live="assertive">
            {{ notification.message }}
        </div>

        <!-- Transkripsiyon İstatistikleri -->
        <div v-if="transcriptionHistory.length" class="statistics-container">
            <h2>İstatistikler</h2>
            <p>Toplam Transkripsiyon: {{ transcriptionHistory.length }}</p>
            <p>Başarılı Transkripsiyonlar: {{ successfulTranscriptions }}</p>
            <p>Başarısız Transkripsiyonlar: {{ failedTranscriptions }}</p>
            <!-- Daha fazla istatistik ekleyebilirsiniz -->
        </div>

        <!-- Kullanıcı Profili -->
        <div v-if="isAuthenticated" class="profile-container">
            <h2>Kullanıcı Profili</h2>
            <p><strong>Kullanıcı Adı:</strong> {{ user.username }}</p>
            <p><strong>Email:</strong> {{ user.email }}</p>
            <button @click="updateProfile" aria-label="Profili Güncelle">Profili Güncelle</button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import jsPDF from 'jspdf'; // PDF oluşturmak için jsPDF kütüphanesini ekleyin
import html2canvas from 'html2canvas'; // HTML içeriğini canvas'a dönüştürmek için

export default {
    data() {
        return {
            audioFiles: [],             // Yüklenen ses dosyaları
            transcription: '',          // Dönüştürülen metin
            isLoading: false,           // Yükleme durumu
            progress: 0,                // İlerleme çubuğu
            errorMessage: '',           // Hata mesajı
            selectedLanguage: 'tr-TR',  // Seçilen transkripsiyon dili
            transcriptionHistory: [],   // Transkripsiyon geçmişi
            isDragOver: false,          // Sürükle ve bırak durumu
            theme: 'light',             // Tema durumu
            audioUrl: '',               // Yüklenen ses dosyasının URL'si
            notification: {             // Bildirim durumu
                message: '',
                type: '',                 // 'success' veya 'error'
            },
            isAuthenticated: false,     // Kullanıcının kimlik doğrulaması durumu
            user: {                     // Kullanıcı bilgileri
                username: '',
                email: '',
            },
            loginForm: {                // Giriş formu
                username: '',
                password: '',
            },
            statistics: {               // İstatistikler
                successfulTranscriptions: 0,
                failedTranscriptions: 0,
            },
        };
    },
    methods: {
        /**
         * Dosya seçme butonunu tetikler.
         */
        triggerFileSelect() {
            this.$refs.fileInput.click();
        },

        /**
         * Dosya sürükleyip bırakıldığında çalışır.
         * @param {DragEvent} event - Drop olayı
         */
        handleDrop(event) {
            const files = Array.from(event.dataTransfer.files);
            this.handleFiles(files);
            this.isDragOver = false;
        },

        /**
         * Dosya seçildiğinde veya sürükle bırak ile yüklendiğinde çalışır.
         * @param {Event} event - Dosya yükleme olayı
         */
        handleAudioUpload(event) {
            const files = Array.from(event.target.files);
            this.handleFiles(files);
        },

        /**
         * Dosyaları kontrol eder ve geçerli olanları ekler.
         * @param {Array} files - Yüklenen dosyaların listesi
         */
        handleFiles(files) {
            const validTypes = ['audio/mpeg', 'audio/wav', 'audio/mp3'];
            const maxSize = 10 * 1024 * 1024; // 10MB

            files.forEach(file => {
                if (!validTypes.includes(file.type)) {
                    this.showNotification(`Geçersiz dosya türü: ${file.name}`, 'error');
                    return;
                }
                if (file.size > maxSize) {
                    this.showNotification(`Dosya boyutu 10MB'ı aşamaz: ${file.name}`, 'error');
                    return;
                }
                this.audioFiles.push(file);
                this.showNotification(`Dosya yüklendi: ${file.name}`, 'success');
            });
        },

        /**
         * Ses dosyasını transkribe eder.
         */
        async transcribeAudio() {
            if (!this.audioFiles.length) {
                this.showNotification("Lütfen en az bir ses dosyası seçin!", 'error');
                return;
            }

            this.isLoading = true;
            this.progress = 0;
            this.errorMessage = '';
            this.transcription = '';

            const formData = new FormData();
            this.audioFiles.forEach(file => {
                formData.append('audio', file);
            });
            formData.append('language', this.selectedLanguage);

            try {
                const response = await axios.post('/api/upload', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json',
                    },
                    onUploadProgress: progressEvent => {
                        this.progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    },
                });

                if (response.data && response.data.transcription) {
                    this.transcription = response.data.transcription;
                    this.audioUrl = URL.createObjectURL(this.audioFiles[0]); // İlk dosyanın URL'sini al
                    this.addToHistory(this.audioFiles[0].name, this.selectedLanguage, this.transcription, this.audioUrl);
                    this.statistics.successfulTranscriptions += 1;
                    this.showNotification('Ses dosyası başarıyla metne dönüştürüldü.', 'success');
                    console.log('Ses dosyası başarıyla metne dönüştürüldü:', this.transcription);
                } else {
                    this.statistics.failedTranscriptions += 1;
                    this.showNotification('Transkripsiyon sonucu alınamadı.', 'error');
                }
            } catch (error) {
                console.error("Ses dosyası dönüştürülürken hata oluştu:", error);
                this.statistics.failedTranscriptions += 1;
                if (error.response && error.response.data && error.response.data.message) {
                    this.showNotification(error.response.data.message, 'error');
                } else {
                    this.showNotification("Ses dosyası dönüştürülürken bir hata oluştu. Lütfen tekrar deneyin.", 'error');
                }
            } finally {
                this.isLoading = false;
                this.progress = 0;
                this.audioFiles = [];
            }
        },

        /**
         * Transkripsiyonu panoya kopyalar.
         */
        copyToClipboard() {
            navigator.clipboard.writeText(this.transcription)
                .then(() => {
                    this.showNotification("Transkripsiyon panoya kopyalandı!", 'success');
                })
                .catch(err => {
                    console.error("Panoya kopyalama hatası:", err);
                    this.showNotification("Transkripsiyonu panoya kopyalama başarısız.", 'error');
                });
        },

        /**
         * Transkripsiyonu metin dosyası olarak indirir.
         */
        downloadTranscription() {
            const blob = new Blob([this.transcription], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'transkripsiyon.txt';
            link.click();
            URL.revokeObjectURL(url);
            this.showNotification("Transkripsiyon indirildi.", 'success');
        },

        /**
         * Transkripsiyonu PDF olarak indirir.
         */
        exportAsPDF() {
            const doc = new jsPDF();
            doc.text(this.transcription, 10, 10);
            doc.save('transkripsiyon.pdf');
            this.showNotification("Transkripsiyon PDF olarak indirildi.", 'success');
        },

        /**
         * Transkripsiyonu özetler.
         */
        async summarizeTranscription() {
            if (!this.transcription) {
                this.showNotification("Özetlenecek bir transkripsiyon yok!", 'error');
                return;
            }

            this.isLoading = true;
            this.errorMessage = '';

            try {
                const response = await axios.post('/api/summarize', {
                    text: this.transcription,
                    language: this.selectedLanguage,
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                });

                if (response.data && response.data.summary) {
                    this.transcription = response.data.summary;
                    this.showNotification('Transkripsiyon başarıyla özetlendi.', 'success');
                } else {
                    this.showNotification('Özetleme sonucu alınamadı.', 'error');
                }
            } catch (error) {
                console.error("Özetleme sırasında hata oluştu:", error);
                this.showNotification("Transkripsiyon özetlenirken bir hata oluştu. Lütfen tekrar deneyin.", 'error');
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Transkripsiyonu paylaşır.
         */
        shareTranscription() {
            const shareData = {
                title: 'Transkripsiyon',
                text: this.transcription,
            };
            if (navigator.share) {
                navigator.share(shareData)
                    .then(() => {
                        this.showNotification("Transkripsiyon başarıyla paylaşıldı.", 'success');
                    })
                    .catch((err) => {
                        console.error("Paylaşma hatası:", err);
                        this.showNotification("Transkripsiyon paylaşılırken bir hata oluştu.", 'error');
                    });
            } else {
                this.showNotification("Bu özellik tarayıcınız tarafından desteklenmiyor.", 'error');
            }
        },

        /**
         * Metni kalın yapar.
         */
        boldText() {
            this.transcription = `<strong>${this.transcription}</strong>`;
            this.showNotification("Metin kalınlaştırıldı.", 'success');
        },

        /**
         * Metni italik yapar.
         */
        italicText() {
            this.transcription = `<em>${this.transcription}</em>`;
            this.showNotification("Metin italikleştirildi.", 'success');
        },

        /**
         * Metni altını çizer.
         */
        underlineText() {
            this.transcription = `<u>${this.transcription}</u>`;
            this.showNotification("Metnin altı çizildi.", 'success');
        },

        /**
         * Tema modunu değiştirir.
         */
        toggleTheme() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            this.showNotification(`Tema ${this.theme === 'light' ? 'Açık' : 'Gölge'} moda geçti.`, 'success');
        },

        /**
         * Transkripsiyonu geçmişe ekler.
         * @param {string} fileName - Dosya adı
         * @param {string} language - Seçilen dil
         * @param {string} transcription - Transkripsiyon metni
         * @param {string} audioUrl - Ses dosyasının URL'si
         */
        addToHistory(fileName, language, transcription, audioUrl) {
            this.transcriptionHistory.unshift({
                fileName,
                language,
                transcription,
                audioUrl,
            });
        },

        /**
         * Bildirim gösterir.
         * @param {string} message - Bildirim mesajı
         * @param {string} type - 'success' veya 'error'
         */
        showNotification(message, type) {
            this.notification.message = message;
            this.notification.type = type;
            setTimeout(() => {
                this.notification.message = '';
                this.notification.type = '';
            }, 3000);
        },

        /**
         * Kullanıcı giriş yapar.
         */
        async login() {
            try {
                const response = await axios.post('/api/login', this.loginForm);
                if (response.data && response.data.token) {
                    localStorage.setItem('authToken', response.data.token);
                    this.isAuthenticated = true;
                    this.user = response.data.user;
                    this.showNotification('Başarıyla giriş yapıldı.', 'success');
                }
            } catch (error) {
                console.error("Giriş hatası:", error);
                this.showNotification("Giriş başarısız. Lütfen bilgilerinizi kontrol edin.", 'error');
            }
        },

        /**
         * Kullanıcı çıkış yapar.
         */
        logout() {
            localStorage.removeItem('authToken');
            this.isAuthenticated = false;
            this.user = { username: '', email: '' };
            this.showNotification("Başarıyla çıkış yapıldı.", 'success');
        },

        /**
         * Kullanıcı profilini günceller.
         */
        async updateProfile() {
            try {
                const response = await axios.put('/api/profile', this.user, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
                    },
                });
                if (response.data && response.data.user) {
                    this.user = response.data.user;
                    this.showNotification("Profil başarıyla güncellendi.", 'success');
                }
            } catch (error) {
                console.error("Profil güncelleme hatası:", error);
                this.showNotification("Profil güncellenirken bir hata oluştu.", 'error');
            }
        },

        /**
         * Geçmiş transkripsiyonu siler.
         * @param {number} index - Silinecek transkripsiyonun indeksi
         */
        deleteHistory(index) {
            this.transcriptionHistory.splice(index, 1);
            this.showNotification("Transkripsiyon geçmişinden silindi.", 'success');
        },

        /**
         * Dil kodunu dil adına çevirir.
         * @param {string} code - Dil kodu
         * @returns {string} - Dil adı
         */
        getLanguageName(code) {
            const languages = {
                'tr-TR': 'Türkçe',
                'ku-TR': 'Kürtçe',
                'zaz-TR': 'Zazaca',
                'ar-TR': 'Arapça',
                'ce-TR': 'Çerkesçe',
                'el-TR': 'Yunanca',
                'hy-TR': 'Ermenice',
                'ka-TR': 'Gürcüce',
                'lzz-TR': 'Lazca',
            };
            return languages[code] || code;
        },
    },
    created() {
        // Kullanıcı kimlik doğrulama durumu kontrolü
        const token = localStorage.getItem('authToken');
        if (token) {
            this.isAuthenticated = true;
            // Token'ı kullanarak kullanıcı bilgilerini almak için bir API çağrısı yapılabilir
            // Örneğin:
            axios.get('/api/profile', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
            })
                .then(response => {
                    if (response.data && response.data.user) {
                        this.user = response.data.user;
                    }
                })
                .catch(error => {
                    console.error("Profil yükleme hatası:", error);
                    this.logout();
                });
        }
    },
};
</script>

<style scoped>
.app-container {
    text-align: center;
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    transition: background-color 0.3s ease, color 0.3s ease;
    display: grid;
    grid-template-areas:
    "theme auth"
    "dropzone dropzone"
    "upload upload"
    "progress progress"
    "result result"
    "editor editor"
    "history history"
    "statistics statistics"
    "profile profile";
    grid-gap: 20px;
}

.app-container.light {
    background: linear-gradient(to right, #ece9e6, #ffffff);
    color: #000;
}

.app-container.dark {
    background: linear-gradient(to right, #2c3e50, #4ca1af);
    color: #fff;
}

.theme-toggle {
    grid-area: theme;
    text-align: right;
}

.theme-toggle button {
    background: none;
    border: none;
    font-size: 1.2em;
    cursor: pointer;
    color: inherit;
}

.auth-section {
    grid-area: auth;
    text-align: left;
}

.auth-section h2 {
    margin-bottom: 10px;
}

.auth-section form {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
}

.auth-section input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.auth-section button {
    padding: 10px;
    border: none;
    border-radius: 5px;
    background-color: #1e90ff;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.auth-section button:hover {
    background-color: #0b78e3;
}

.dropzone {
    grid-area: dropzone;
    border: 2px dashed #ccc;
    padding: 20px;
    border-radius: 10px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
    position: relative;
}

.dropzone.is-dragover {
    background-color: #f0f8ff;
    border-color: #1e90ff;
}

.dropzone p {
    margin: 0;
    font-size: 1.1em;
}

.select-button {
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    border: none;
    background-color: #1e90ff;
    color: white;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.select-button:hover {
    background-color: #0b78e3;
    transform: translateY(-2px);
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
}

.upload-container {
    grid-area: upload;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: rgba(255, 255, 255, 0.8);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.upload-section {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    align-items: center;
    justify-items: center;
}

.upload-section select {
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    cursor: pointer;
}

.transcribe-button {
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    border: none;
    background-color: #4caf50;
    color: white;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.transcribe-button:disabled {
    background-color: #a5d6a7;
    cursor: not-allowed;
}

.transcribe-button:hover:not(:disabled) {
    background-color: #388e3c;
    transform: translateY(-2px);
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
}

.progress-bar {
    grid-area: progress;
    width: 100%;
    background-color: #f3f3f3;
    border-radius: 5px;
    overflow: hidden;
}

.progress {
    height: 10px;
    background-color: #4caf50;
    width: 0%;
    transition: width 0.3s ease;
}

.transcription-result {
    grid-area: result;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: rgba(255, 255, 255, 0.9);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.transcription-textarea {
    width: 100%;
    height: 150px;
    resize: vertical;
    padding: 10px;
    font-size: 1em;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.actions {
    margin-top: 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
}

.actions button {
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    background-color: #1e90ff;
    color: white;
    transition: background-color 0.3s ease;
}

.actions button:hover {
    background-color: #0b78e3;
}

.editor-tools {
    grid-area: editor;
    display: flex;
    gap: 10px;
    justify-content: center;
}

.editor-tools button {
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    background-color: #ff9800;
    color: white;
    transition: background-color 0.3s ease;
}

.editor-tools button:hover {
    background-color: #e68900;
}

.history-container {
    grid-area: history;
    padding: 20px;
    border-top: 2px solid #ccc;
}

.history-container h2 {
    margin-bottom: 10px;
}

.history-container ul {
    list-style: none;
    padding: 0;
}

.history-container li {
    margin-bottom: 20px;
}

.history-container hr {
    border: none;
    border-top: 1px solid #ccc;
    margin: 10px 0;
}

.statistics-container {
    grid-area: statistics;
    padding: 20px;
    border-top: 2px solid #ccc;
}

.statistics-container h2 {
    margin-bottom: 10px;
}

.error-message {
    margin-top: 10px;
    color: #dc3545;
    font-weight: bold;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 5px;
    color: white;
    z-index: 1000;
    opacity: 0.9;
}

.notification.success {
    background-color: #28a745;
}

.notification.error {
    background-color: #dc3545;
}

.profile-container {
    grid-area: profile;
    padding: 20px;
    border-top: 2px solid #ccc;
}

.profile-container h2 {
    margin-bottom: 10px;
}

.profile-container p {
    margin: 5px 0;
}

.profile-container button {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background-color: #17a2b8;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.profile-container button:hover {
    background-color: #138496;
}

/* Responsive Tasarım */
@media (max-width: 768px) {
    .app-container {
        grid-template-areas:
      "theme"
      "auth"
      "dropzone"
      "upload"
      "progress"
      "result"
      "editor"
      "history"
      "statistics"
      "profile";
    }

    .actions {
        flex-direction: column;
    }

    .actions button {
        width: 100%;
    }
}
</style>
