<template>
    <div id="app" class="app-container">
        <div class="nav-container" style="background-color: rgba(52, 58, 64, 0.9); padding: 5px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); display: flex; flex-wrap: nowrap; overflow-x: auto;">
            <div class="nav-links" style="display: flex; gap: 5px; flex-wrap: nowrap;">
                <router-link to="/" class="nav-link" @click="handleNavClick">ANA SAYFA</router-link>
                <router-link v-if="isAuth" to="/sorular" class="nav-link" @click="handleNavClick">SORULAR</router-link>
                <router-link v-if="!isAuth" to="/scores" class="nav-link">SIRALAMA</router-link>
                <router-link v-if="!isAuth" to="/admin" class="nav-link" @click="handleAdminLoginClick">ADMİN GİRİŞ</router-link>
                <div class="nav-link dropdown" @click="toggleDropdown">
                    EĞİTİM
                    <div v-if="dropdownVisible" class="dropdown-content" :style="dropdownPosition" @mouseleave="closeDropdown">
                        <router-link to="/video" class="dropdown-item" @click="handleNavClick">Video</router-link>
                        <router-link to="/belge" class="dropdown-item" @click="handleNavClick">Belge</router-link>
                        <router-link to="/Antireman" class="dropdown-item" @click="handleNavClick">Antireman</router-link>
                        <router-link to="/menu2" class="dropdown-item" @click="handleNavClick">Menu2</router-link>
                    </div>
                </div>
                <router-link to="/harita" class="nav-link" @click="handleNavClick">HARİTA</router-link>
                <button v-if="isAuth" type="button" @click="logout" class="logout-button">ADMİN ÇIKIŞ</button>
            </div>
        </div>

        <!-- Profil sayfası ve diğer içerikler menünün hemen altında yer alacak -->
        <div class="main-content" style="padding: 2px;">

            <router-view></router-view>

        </div>

        <!-- Sınav uygulaması onay penceresi -->
        <div v-if="showModal" class="modal">
            <div class="modal-content" style="background-color: rgba(255, 255, 255, 0.9);">
                <h2>Sınav Uygulaması</h2>
                <p>"Hoş geldiniz! Bu Sayfa, Yazılım Dersi Sınav Uygulaması'dır.
                    Kayıt olmadan sınava katılmak için yalnızca bir fotoğraf yükleyip adınızı girmeniz yeterlidir. <hr>
                    Sınav sonuçlarınızı aldıktan sonra, genel sıralama listesinde nerede yer aldığınızı görebilirsiniz.
                    Başarılar dileriz!"</p>
                <button @click="closeModal" class="modal-button">Tamam</button>
            </div>
        </div>

        <!-- Alt kısımda ikonlu menü -->
        <div class="bottom-menu">
            <router-link to="/profile" class="bottom-menu-item">
                <i class="fas fa-user"></i>
                <span>KULLANICI PROFİLİ</span>
            </router-link>
            <router-link to="/badges" class="bottom-menu-item">
                <i class="fas fa-trophy"></i>
                <span>ROZET SAYFASI</span>
            </router-link>
            <router-link to="/exam-create" class="bottom-menu-item exam-create">
                <i class="fas fa-edit"></i>
                <span style="margin-bottom: 12px;">SINAV OLUŞTUR</span>
            </router-link>
            <router-link to="/categories" class="bottom-menu-item">
                <i class="fas fa-list"></i>
                <span>SINAV KATEGORİ SEÇİMİ</span>
            </router-link>
            <router-link to="/statistics" class="bottom-menu-item">
                <i class="fas fa-chart-bar"></i>
                <span>SINAV İSTATİSTİKLERİ</span>
            </router-link>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isAuth: false,
            showModal: true, // Modal pencere başlangıçta görünür
            dropdownVisible: false, // Eğitim menüsü için dropdown başlangıçta kapalı
            dropdownPosition: {}, // Dropdown menünün pozisyon bilgisi
        }
    },
    methods: {
        logout() {
            localStorage.removeItem('authToken');
            window.location.href = '/admin';
        },
        closeModal() {
            this.showModal = false; // Modal pencereyi kapat
        },
        handleNavClick() {
            this.closeRanking(); // Navigasyona tıklanıldığında sıralama menüsünü kapat
            this.dropdownVisible = false; // Dropdown menüyü kapat
        },
        handleAdminLoginClick() {
            this.closeRanking(); // Admin girişine tıklanıldığında sıralama menüsünü kapat
        },
        toggleDropdown() {
            this.dropdownVisible = !this.dropdownVisible; // Dropdown menüsünü aç/kapat
            if (this.dropdownVisible) {
                const dropdown = this.$el.querySelector('.dropdown');
                const rect = dropdown.getBoundingClientRect();
                this.dropdownPosition = {
                    position: 'fixed',
                    top: `${rect.bottom}px`,
                    left: `${rect.left}px`,
                    backgroundColor: '#f8f9fa',
                    boxShadow: '0 2px 5px rgba(0, 0, 0, 0.2)',
                    borderRadius: '5px',
                    zIndex: 1000,
                    padding: '10px',
                    minWidth: '150px',
                };
            }
        },
        closeDropdown() {
            this.dropdownVisible = false; // Dropdown menüyü kapat
        },
    },
    created() {
        // Kullanıcının giriş durumunu kontrol et
        this.isAuth = !!localStorage.getItem('authToken');
    },
    beforeRouteLeave(to, from, next) {
        // Sıralama menüsünü gizle
        this.closeRanking();
        next(); // Route değişikliğine devam et
    },
}
</script>

<style scoped>
/* Nav Container Stilleri */
.nav-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0;
    background-color: #f8f9fa;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    overflow-x: auto;
}

.nav-links {
    display: flex;
    gap: 5px;
    flex-wrap: nowrap;
}

.nav-link {
    color: white;
    text-decoration: none;
    font-weight: bold;
    padding: 8px 10px;
    border-radius: 12px;
    transition: background-color 0.3s ease;
    background-color: #007bff;
    white-space: nowrap;
}

.nav-link:hover {
    background-color: #ef4444;
}

/* Dropdown menü stilleri */
.dropdown {
    position: relative;
    cursor: pointer;
}

.dropdown-content {
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 100%;
    left: 0;
    background-color: #f8f9fa;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    border-radius: 5px;
    z-index: 1000;
    padding: 10px;
    min-width: 150px;
}

.dropdown-item {
    padding: 8px;
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.dropdown-item:hover {
    background-color: #ff0000;
}

/* Sıralama butonu stili */
.ranking-button {
    color: #007bff;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 1em;
    background-color: #f8f9fa;
}

.ranking-button:hover {
    background-color: #e2e6ea;
}

/* Logout butonu stili */
.logout-button {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 1em;
}

.logout-button:hover {
    background-color: #c82333;
}

/* Modal stilleri */
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
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
    max-width: 90%;
}

.modal-button {
    background-color: #007bff;
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

/* Alt menü stil */
.bottom-menu {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: #343a40;
    display: flex;
    justify-content: space-around;
    padding: 10px;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
    margin-bottom: 4px;
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

/* SINAV OLUŞTUR butonu animasyonu */
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
    }
    .nav-links {
        padding: 2px 14px;
        font-size: 0.8em;
    }

    .score-link {
        width: 100%;
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
}
</style>
