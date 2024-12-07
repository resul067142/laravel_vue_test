<template>
    <div id="app" class="app-container"  >
        <header class="top-nav" >
            <div class="top-nav-inner">
                <div class="hamburger-menu" @click="toggleHamburgerMenu">
                    <i class="fas fa-bars"></i>
                </div>
                <nav class="nav-links"   v-if="!hamburgerMenuVisible">
                    <router-link to="/" class="nav-link"  @click="handleNavClick">Ana Sayfa</router-link>
                    <router-link v-if="!isAuth" to="/admin" class="nav-link" @click="handleAdminLoginClick">Yönetici Girişi</router-link>
                    <button v-if="isAuth" type="button" @click="çıkışYap" class="logout-button">Çıkış Yap</button>
                </nav>
            </div>
        </header>

        <transition name="fade">
            <div v-if="hamburgerMenuVisible" class="mobile-menu">
                <router-link to="/sorular" v-if="isAuth" class="mobile-menu-item" @click="handleNavClick">Sorular</router-link>
                <router-link to="/antreman" class="mobile-menu-item" @click="handleNavClick">Antreman</router-link>
                <router-link to="/scores" class="mobile-menu-item" @click="handleNavClick">Sıralama</router-link>
                <router-link to="/soytakip" class="mobile-menu-item" @click="handleNavClick">Soy Takip</router-link>
                <router-link to="/kisitespit" class="mobile-menu-item" @click="handleNavClick">Kişi Tespit</router-link>
                <router-link to="/video" class="mobile-menu-item" @click="handleNavClick">Video</router-link>
                <router-link to="/belge" class="mobile-menu-item" @click="handleNavClick">Belge</router-link>
                <router-link to="/dosya" class="mobile-menu-item" @click="handleNavClick">Dosyalar</router-link>
                <router-link to="/puanlama" class="mobile-menu-item" @click="handleNavClick">Puanlama</router-link>
                <router-link to="/sesmetin" class="mobile-menu-item" @click="handleNavClick">Ses Metin</router-link>
                <router-link to="/harita" class="mobile-menu-item" @click="handleNavClick">Harita</router-link>
            </div>
        </transition>

        <main class="main-content">
            <router-view></router-view>
            <div v-if="convertedAudioFile" class="audio-file-container">
                <h3>Ses Dönüştürülen Metin Dosyası</h3>
                <a :href="convertedAudioFile" download="converted_text.txt" class="download-button">Metin Dosyasını İndir</a>
            </div>
        </main>

        <footer class="bottom-menu">
            <router-link to="/profile" class="bottom-menu-item">
                <i class="fas fa-user"></i>
                <span>Kullanıcı Profili</span>
            </router-link>
            <router-link to="/badges" class="bottom-menu-item">
                <i class="fas fa-trophy"></i>
                <span>Rozet Sayfası</span>
            </router-link>
            <router-link to="/exam-create" class="bottom-menu-item special-item">
                <i class="fas fa-edit"></i>
                <span>Sınav Oluştur</span>
            </router-link>
            <router-link to="/categories" class="bottom-menu-item">
                <i class="fas fa-list"></i>
                <span>Kategoriler</span>
            </router-link>
            <router-link to="/statistics" class="bottom-menu-item">
                <i class="fas fa-chart-bar"></i>
                <span>İstatistikler</span>
            </router-link>
        </footer>
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

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}
.app-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
.top-nav {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #3498db;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
.top-nav-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.hamburger-menu {
    color: #fff;
    font-size: 20px;
    cursor: pointer;
    padding: 5px 10px;
}
.nav-links {
    display: flex;
    gap: 15px;
}
.nav-link {
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 5px;
}
.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.2);
}
.logout-button {
    background: #e74c3c;
    color: #fff;
    border: none;
    padding: 5px 15px;
    border-radius: 5px;
    cursor: pointer;
}
.logout-button:hover {
    background: #c0392b;
}
.mobile-menu {
    position: absolute;
    top: 60px;
    left: 0;
    right: 0;
    background: #34495e;
    padding: 15px 0;
    z-index: 20;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.mobile-menu-item {
    color: #ecf0f1;
    text-decoration: none;
    padding: 10px 20px;
    font-weight: 600;
}
.mobile-menu-item:hover {
    background: #2c3e50;
}
.main-content {
    flex: 1;
    padding: 15px;
}
.audio-file-container {
    margin-top: 20px;
    text-align: center;
}
.download-button {
    display: inline-block;
    background: #3498db;
    color: #fff;
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 5px;
    margin-top: 10px;
}
.download-button:hover {
    background: #2980b9;
}
.bottom-menu {
    position: sticky;
    bottom: 0;
    background: #2c3e50;
    display: flex;
    justify-content: space-around;
    padding: 10px;
    box-shadow: 0 -1px 5px rgba(0,0,0,0.2);
}
.bottom-menu-item {
    text-decoration: none;
    color: #fff;
    font-size: 0.85em;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    align-items: center;
    gap: 2px;
    transition: transform 0.2s;
}
.bottom-menu-item i {
    font-size: 20px;
}
.bottom-menu-item:hover {
    transform: scale(1.1);
}
.special-item {
    background: #e74c3c;
    padding: 5px 10px;
    border-radius: 5px;
}
.special-item:hover {
    background: #c0392b;
}
@media (max-width: 480px) {
    .nav-links {
        display: none;
    }
    .mobile-menu-item {
        font-size: 0.9em;
    }
    .bottom-menu-item {
        font-size: 0.75em;
    }
}
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter, .fade-leave-to {
    opacity: 0;
}
</style>
