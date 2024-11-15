<template>
    <div id="app">
        <div class="app-container">
            <!-- Hamburger Menu and Main Navigation -->
            <div class="nav-container">
                <div class="hamburger-menu" @click="toggleHamburgerMenu">
                    <i class="fas fa-th fa-3x"></i>
                </div>
                <div class="nav-links">
                    <router-link to="/" class="nav-link" @click="handleNavClick">Ana Sayfa</router-link>
                    <router-link v-if="!isAuth" to="/scores" class="nav-link">Sıralama</router-link>
                    <router-link v-if="!isAuth" to="/admin" class="nav-link" @click="handleAdminLoginClick">Admin Giriş</router-link>
                    <button v-if="isAuth" type="button" @click="logout" class="logout-button">Admin Çıkış</button>
                </div>
            </div>

            <!-- Hamburger Menu Content -->
            <div v-if="hamburgerMenuVisible" class="hamburger-menu-content">
                <router-link to="/sorular" v-if="isAuth" class="hamburger-menu-item" @click="handleNavClick">Sorular</router-link>
                <router-link to="/video" class="hamburger-menu-item" @click="handleNavClick">Video</router-link>
                <router-link to="/belge" class="hamburger-menu-item" @click="handleNavClick">Belge</router-link>
                <router-link to="/antreman" class="hamburger-menu-item" @click="handleNavClick">Antreman</router-link>
                <router-link to="/dosya" class="hamburger-menu-item" @click="handleNavClick">Dosyalar</router-link>
                <router-link to="/puanlama" class="hamburger-menu-item" @click="handleNavClick">Puanlama</router-link>
                <router-link to="/soytakip" class="hamburger-menu-item" @click="handleNavClick">Takip</router-link>
                <router-link to="/sesmetin" class="hamburger-menu-item" @click="handleNavClick">Ses Metin</router-link>
                <router-link to="/harita" class="hamburger-menu-item" @click="handleNavClick">Harita</router-link>
            </div>

            <!-- Ses Dosyası Yükleme Bölümü -->
            <div class="upload-container">
                <h2>Ses Dosyası Kütüphanesi</h2>
                <div class="upload-section">
                    <input type="file" @change="handleAudioUpload" accept="audio/*" placeholder="Ses dosyası seçin..." />
                    <button @click="transcribeAudio" class="transcribe-button">Ses Dosyasını Metine Dönüştür</button>
                </div>
            </div>

            <div v-if="transcription" class="transcription-result">
                <h3>Transkripsiyon:</h3>
                <p>{{ transcription }}</p>
            </div>

            <!-- Exam Confirmation Modal -->
            <div v-if="showModal" class="modal">
                <div class="modal-content">
                    <h2>Sınav Uygulaması</h2>
                    <p>Hoş geldiniz! Bu sayfa Yazılım Dersi Sınav Uygulamasıdır. Kayıt olmadan sınava katılmak için sadece bir fotoğraf yükleyip adınızı girmeniz yeterlidir. <hr> Sınav sonuçlarınızı aldıktan sonra genel sıralama listesindeki yerinizi görebilirsiniz. Başarılar dileriz!</p>
                    <button @click="closeModal" class="modal-button">Tamam</button>
                </div>
            </div>

            <!-- Bottom Icon Menu -->
            <div class="bottom-menu">
                <router-link to="/profile" class="bottom-menu-item">
                    <i class="fas fa-user"></i>
                    <span>Kullanıcı Profili</span>
                </router-link>
                <router-link to="/badges" class="bottom-menu-item">
                    <i class="fas fa-trophy"></i>
                    <span>Rozet Sayfası</span>
                </router-link>
                <router-link to="/exam-create" class="bottom-menu-item exam-create">
                    <i class="fas fa-edit"></i>
                    <span>Sınav Oluştur</span>
                </router-link>
                <router-link to="/categories" class="bottom-menu-item">
                    <i class="fas fa-list"></i>
                    <span>Sınav Kategorileri</span>
                </router-link>
                <router-link to="/statistics" class="bottom-menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Sınav İstatistikleri</span>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            audioFile: null, // Seçilen ses dosyası
            transcription: '', // Dönüştürülen metin
            isAuth: false,
            showModal: true,
            dropdownVisible: false,
            dropdownPosition: {},
            hamburgerMenuVisible: false,
        };
    },
    methods: {
        handleAudioUpload(event) {
            const uploadedAudio = event.target.files[0];
            if (uploadedAudio) {
                console.log("Yüklenmiş Ses Dosyası:", uploadedAudio);
                this.audioFile = uploadedAudio;
            }
        },
        async transcribeAudio() {
            if (!this.audioFile) {
                alert("Lütfen bir ses dosyası seçin!");
                return;
            }

            const formData = new FormData();
            formData.append('audio', this.audioFile);

            try {
                const response = await axios.post('/api/upload', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json',  // Bunu ekleyin
                    },
                });




                this.transcription = response.data.transcription;
                console.log('Ses dosyası başarıyla metne dönüştürüldü:', this.transcription);
            } catch (error) {
                console.error("Ses dosyası dönüştürülürken hata oluştu:", error);
                alert("Ses dosyası dönüştürülürken bir hata oluştu. Lütfen tekrar deneyin.");
            }
        },
        logout() {
            localStorage.removeItem('authToken');
            window.location.href = '/admin';
        },
        closeModal() {
            this.showModal = false;
        },
        handleNavClick() {
            this.hamburgerMenuVisible = false;
            this.dropdownVisible = false;
        },
        handleAdminLoginClick() {
            this.hamburgerMenuVisible = false;
        },
        toggleDropdown() {
            this.dropdownVisible = !this.dropdownVisible;
        },
        toggleHamburgerMenu() {
            this.hamburgerMenuVisible = !this.hamburgerMenuVisible;
        },
    },
    created() {
        this.isAuth = !!localStorage.getItem('authToken');
    },
};
</script>

<style scoped>
#app {
    text-align: center;
    max-width: 100%;
    margin: 0 auto;
    padding: 20px;
    background: linear-gradient(to right, #ece9e6, #ffffff);
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.upload-container {
    margin-bottom: 20px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #f9f9f9;
    transition: all 0.3s ease;
}

.upload-section {
    display: flex;
    gap: 10px;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
}

.transcribe-button {
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: none;
    background-color: #4caf50;
    color: white;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.transcribe-button:hover {
    background-color: #388e3c;
    transform: translateY(-2px);
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
}

.transcription-result {
    margin-top: 20px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #fff;
}

.nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background-color: #007bff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    border-radius: 8px;
    margin: 0;
    color: white;
    font-weight: bold;
}

.hamburger-menu {
    padding: 5px;
    background-color: #ff4444;
    border-radius: 50%;
    transition: transform 0.3s ease;
    margin-right: 15px;
}

.hamburger-menu:hover {
    transform: scale(1.1);
}

.hamburger-menu-content {
    position: fixed;
    top: 60px;
    left: 10px;
    background-color: #2c2c2e;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
    border-radius: 8px;
    z-index: 2000;
    padding: 15px;
    min-width: 200px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.hamburger-menu-item {
    color: #f0f0f3;
    text-decoration: none;
    font-weight: bold;
    padding: 10px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.hamburger-menu-item:hover {
    background-color: #ff6347;
}

.nav-links {
    display: flex;
    gap: 10px;
    flex: 1;
    justify-content: space-around;
    align-items: center;
}

.nav-link {
    color: white;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 8px;
    transition: background-color 0.3s ease;
    background-color: #1c1c1e;
    white-space: nowrap;
    font-size: 0.9em;
}

.nav-link:hover {
    background-color: #1e90ff;
}

.logout-button {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 0.9em;
}

.logout-button:hover {
    background-color: #c82333;
}

.bottom-menu {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: #1c1c1e;
    display: flex;
    justify-content: space-around;
    padding: 10px;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.3);
    margin-bottom: 4px;
    z-index: 1000;
}

.bottom-menu-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    font-weight: bold;
    font-size: 0.9em;
    color: white;
    padding: 5px;
    border-radius: 5px;
    transition: transform 0.3s ease;
    text-align: center;
}

.bottom-menu-item i {
    font-size: 20px;
    margin-bottom: 5px;
    transition: transform 0.3s ease;
}

.bottom-menu-item:hover {
    transform: scale(1.1);
}

.exam-create {
    animation: pulse-red 6s infinite, grow-shrink 3s infinite;
}

@keyframes pulse-red {
    0%, 100% {
        background-color: #e74c3c;
    }
    50% {
        background-color: #0056b3;
    }
}

@keyframes grow-shrink {
    0%, 100% {
        font-size: 0.9em;
    }
    100% {
        font-size: 0.9em;
    }
}

@media (max-width: 768px) {
    .nav-container {
        padding: 10px;
        justify-content: space-between;
        flex-wrap: nowrap;
    }
    .hamburger-menu {
        display: block;
    }
    .nav-links {
        display: flex;
        width: 100%;
        justify-content: space-between;
        padding: 0;
    }
    .nav-link, .logout-button {
        flex: 1;
        text-align: center;
        margin: 0 5px;
    }
    .bottom-menu {
        flex-direction: row;
        gap: 5px;
        padding: 15px;
        background-color: rgba(52, 58, 64, 0.9);
    }
    .bottom-menu-item {
        font-size: 0.7em;
    }
    .main-content {
        padding: 10px;
        margin-top: 100px;
    }
}
</style>
