<template>
    <div id="app">
        <div class="nav-container">
            <div class="nav-links">
                <router-link to="/" class="nav-link" @click="handleNavClick">ANA SAYFA</router-link>
                <router-link v-if="isAuth" to="/sorular" class="nav-link" @click="handleNavClick">SORULAR</router-link>
                <button v-if="!isAuth" type="button" @click="openRanking" class="nav-link">PUAN SIRALAMA</button>
                <router-link v-if="!isAuth" to="/admin" class="nav-link" @click="handleAdminLoginClick">ADMİN GİRİŞ</router-link>
                <button v-if="isAuth" type="button" @click="logout" class="logout-button">ADMİN ÇIKIŞ</button>
            </div>
        </div>
        <!-- Sıralama menüsü görünürlüğü -->
        <div v-if="isRankingVisible" class="score-menu">
            <router-link to="/scores/0-25" class="score-link" @click.prevent="fetchScores('0-25')">0-25</router-link>
            <router-link to="/scores/26-50" class="score-link" @click.prevent="fetchScores('26-50')">26-50</router-link>
            <router-link to="/scores/51-75" class="score-link" @click.prevent="fetchScores('51-75')">51-75</router-link>
            <router-link to="/scores/76-100" class="score-link" @click.prevent="fetchScores('76-100')">76-100</router-link>
        </div>

        <div v-if="scores.length > 0">
            <div v-for="score in scores" :key="score.id" class="score-item">
                <img v-if="score.img_url" :src="'/uploads/' + score.img_url" alt="User Photo" class="score-photo">
                <p>{{ score.name }}</p>
                <p>{{ score.score }} Puan</p>
            </div>
        </div>

        <router-view></router-view>

        <!-- Sınav uygulaması onay penceresi -->
        <div v-if="showModal" class="modal">
            <div class="modal-content">
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
                <span>PROFİL</span>
            </router-link>
            <router-link to="/badges" class="bottom-menu-item">
                <i class="fas fa-trophy"></i>
                <span>ROZETLER</span>
            </router-link>
            <router-link to="/exam-create" class="bottom-menu-item exam-create">
                <i class="fas fa-edit"></i>
                <span style="margin-bottom: 12px;">SINAV OLUŞTUR</span>
            </router-link>
            <router-link to="/categories" class="bottom-menu-item">
                <i class="fas fa-list"></i>
                <span>KATEGORİ</span>
            </router-link>
            <router-link to="/statistics" class="bottom-menu-item">
                <i class="fas fa-chart-bar"></i>
                <span>İSTATİSTİK</span>
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
            scores: [], // Skorları tutacak dizi
            isRankingVisible: false // Sıralama menüsünün görünürlüğü
        }
    },
    methods: {
        openRanking() {
            this.isRankingVisible = !this.isRankingVisible; // Sıralama menüsünü göster
        },
        closeRanking() {
            this.isRankingVisible = false; // Sıralama menüsünü gizle
            this.scores = []; // Skorları sıfırla
        },
        logout() {
            localStorage.removeItem('authToken');
            window.location.href = '/admin';
        },
        closeModal() {
            this.showModal = false; // Modal pencereyi kapat
        },
        fetchScores(range) {
            axios.get(`/api/scores/${range}`)
                .then(response => {
                    this.scores = response.data;
                })
                .catch(error => {
                    console.error("Veri alınırken bir hata oluştu:", error);
                });
        },
        handleNavClick() {
            this.closeRanking(); // Navigasyona tıklanıldığında sıralama menüsünü kapat
        },
        handleAdminLoginClick() {
            this.closeRanking(); // Admin girişine tıklanıldığında sıralama menüsünü kapat
            this.scores = []; // Skorları sıfırla
        }
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
    padding: 10px;
    background-color: #f8f9fa;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.nav-links {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.nav-link {
    color: white;
    text-decoration: none;
    font-weight: bold;
    padding: 12px 12px;
    border-radius: 12px;
    transition: background-color 0.3s ease;
    background-color: #007bff;
}

.nav-link:hover {
    background-color: #ef4444;
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

/* Puan Menüsü stilleri */
.score-menu {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    margin: 20px;
    gap: 10px;
}

.score-link {
    padding: 10px;
    background-color: #28a745;
    color: white;
    text-decoration: none;
    text-align: center;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.2s ease;
    min-width: 100px;
}

.score-link:hover {
    background-color: #218838;
    transform: scale(1.05);
}

.score-item {
    text-align: center;
    margin: 10px 0;
}

.score-photo {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 10px;
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
