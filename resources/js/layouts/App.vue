<template>
    <div id="app" class="app-container">
        <!-- Hamburger Menu and Main Navigation -->
        <div class="nav-container" style="padding: 10px 20px; justify-content: center;">
            <div class="hamburger-menu" @click="toggleHamburgerMenu" style="padding: 20px; border-radius: 12px;">
                <i class="fas fa-stream fa-3x"></i>
            </div>
            <div class="nav-links" style="flex: 1; justify-content: space-evenly; /* adjusted for even spacing */ /* adjusted for better alignment */ align-items: center;">
                <router-link to="/" class="nav-link" @click="handleNavClick">Ana Sayfa</router-link>
                <router-link v-if="!isAuth" to="/scores" class="nav-link">Sıralama</router-link>
                <router-link v-if="!isAuth" to="/admin" class="nav-link" @click="handleAdminLoginClick">Yönetici Girişi</router-link>
                <button v-if="isAuth" type="button" @click="çıkışYap" class="çıkışYap-button">Yönetici Çıkışı</button>
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
            <router-link to="/soytakip" class="hamburger-menu-item" @click="handleNavClick">Soy Takip</router-link>
            <router-link to="/sesmetin" class="hamburger-menu-item" @click="handleNavClick">Ses Metin</router-link>
            <router-link to="/harita" class="hamburger-menu-item" @click="handleNavClick">Harita</router-link>
            <router-link to="/aile" class="hamburger-menu-item" @click="handleNavClick">aile</router-link>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <router-view></router-view>

            <!-- Ses Metin Dosyası -->
            <div v-if="convertedAudioFile" class="audio-file-container">
                <h3>Ses Dönüştürülen Metin Dosyası</h3>
                <a :href="convertedAudioFile" download="converted_text.txt" class="download-button">Metin Dosyasını İndir</a>
            </div>
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
</template>

<script>
export default {
    data() {
        return {
            convertedAudioFile: null,
            isAuth: false,
            showModal: true,
            dropdownVisible: false,
            dropdownPosition: {},
            hamburgerMenuVisible: false,
        }
    },
    methods: {
        çıkışYap() {
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
    }
}
</script>

<style scoped>
/* Navigation Container Styles */
.nav-container {
    display: flex;
    justify-content: space-evenly; /* adjusted for even spacing */ /* adjusted for better alignment */
    align-items: center;
    padding: 8px; /* slightly reduced padding for compactness */
    background-color: #1e90ff; /* updated to make it more visually appealing */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4); /* enhanced shadow for a more defined appearance */
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    border-radius: 15px; /* smoother rounded corners for a modern look */
    margin: 0;
    color: white;
    font-weight: bold;
}

.hamburger-menu {
    padding: 8px; /* slightly reduced padding for compactness */ /* increased padding for better visual balance */
    background-color: #ff4444;
    border-radius: 50%;
    transition: transform 0.3s ease;
    margin-right: 5px; /* further reduced margin for closer alignment */ /* reduced margin for closer positioning */
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
    border-radius: 15px; /* smoother rounded corners for a modern look */
    z-index: 2000;
    padding: 15px;
    min-width: 200px;
    display: flex;
    flex-direction: column;
    gap: 2px; /* reduced gap for tighter alignment */ /* reduced gap for closer alignment */
}

.hamburger-menu-item {
    color: #f0f0f3;
    text-decoration: none;
    font-weight: bold;
    padding: 8px; /* slightly reduced padding for compactness */
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.hamburger-menu-item:hover {
    background-color: #ff6347;
}

.nav-links {
    display: flex;
    gap: 2px; /* reduced gap for tighter alignment */ /* reduced gap for closer alignment */
    flex: 1;
    justify-content: space-around;
    align-items: center;
}

.nav-link {
    color: white;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 15px; /* smoother rounded corners for a modern look */
    transition: background-color 0.3s ease;
    background-color: #2c2c2e; /* darker shade for better contrast */
    white-space: nowrap;
    font-size: 1em; /* increased font size for readability */
}

.nav-link:hover {
    background-color: #1e90ff;
}

/* Logout Button Styles */
.çıkışYap-button {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 15px; /* smoother rounded corners for a modern look */
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 1em; /* increased font size for readability */
}

.çıkışYap-button:hover {
    background-color: #c82333;
}

/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 3000;
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 15px; /* smoother rounded corners for a modern look */
    text-align: center;
    max-width: 90%;
}

.modal-button {
    background-color: #1e90ff; /* updated to make it more visually appealing */
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 1em;
}

.modal-button:hover {
    background-color: #0056b3;
}

/* Bottom Menu Styles */
.bottom-menu {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: #2c2c2e; /* darker shade for better contrast */
    display: flex;
    justify-content: space-around;
    padding: 8px; /* slightly reduced padding for compactness */
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
    font-size: 1em; /* increased font size for readability */
    color: white;
    padding: 8px; /* slightly reduced padding for compactness */ /* increased padding for better visual balance */
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

/* Exam Create Button Animation */
.exam-create {
    background-color: #e74c3c;
    transition: background-color 6s ease-in-out;
}

.exam-create:hover {
    background-color: #0056b3;
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
        font-size: 1em; /* increased font size for readability */
    }
    100% {
        font-size: 1em;
    }
}

/* Responsive Styles */
@media (max-width: 768px) {
    .nav-container {
        padding: 8px; /* slightly reduced padding for compactness */
        justify-content: space-evenly; /* adjusted for even spacing */ /* adjusted for better alignment */
        flex-wrap: nowrap;
    }
    .hamburger-menu {
        display: block;
    }
    .nav-links {
        display: flex;
        width: 100%;
        justify-content: space-evenly; /* adjusted for even spacing */ /* adjusted for better alignment */
        padding: 0;
    }
    .nav-link, .çıkışYap-button {
        flex: 1;
        text-align: center;
        margin: 0 5px;
    }
    .bottom-menu {
        flex-direction: row;
        gap: 2px; /* reduced gap for tighter alignment */
        padding: 15px;
        background-color: rgba(52, 58, 64, 0.9);
    }
    .bottom-menu-item {
        font-size: 0.7em;
    }
    .main-content {
        padding: 8px; /* slightly reduced padding for compactness */
        margin-top: 100px;
    }
}
</style>
