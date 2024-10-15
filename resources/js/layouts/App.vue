<template>
    <div id="app">
        <div class="nav-container">
            <div  class="nav-links">
                <router-link to="/" class="nav-link" @click="handleNavClick">ANA SAYFA</router-link>
                <router-link v-if="isAuth" to="/sorular" class="nav-link" @click="handleNavClick">SORULAR</router-link>
                <button v-if="!isAuth" type="button" @click="openRanking" class="nav-link"> PUAN SIRALAMA</button>
                <router-link v-if="!isAuth" to="/admin" class="nav-link" @click="handleAdminLoginClick">ADMİN GİRİŞ</router-link>
                <button v-if="isAuth" type="button" @click="logout" class="logout-button">ADMİN ÇIKIŞ</button>
            </div>
        </div>
        <!-- Sıralama menüsü görünürlüğü -->
        <div v-if="isRankingVisible" class="score-menu">
            <router-link to="/scores/0-25" class="score-link"  @click.prevent="fetchScores('0-25')">0-25</router-link>
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
            this.isRankingVisible = true; // Sıralama menüsünü göster
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
    }
}
</script>


<style scoped>
/* Nav Container Stilleri */
.nav-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 10px;
    background-color: #f8f9fa; /* Arka plan rengi */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Gölge efekti */
}

.nav-links {
    display: flex;
    gap: 5px; /* Bağlantılar arasındaki mesafeyi azaltmak için */
}

.nav-link {
    color: white;
    text-decoration: none;
    font-weight: bold;
    padding: 10px 15px; /* Buton görünümünü artırmak için padding */
    border-radius: 5px; /* Yuvarlatılmış köşeler */
    transition: background-color 0.3s ease;
    background-color: #0056b3;
}

.nav-link:hover {
    background-color: skyblue; /* Açık gri hover efekti */
    transition: background-color .2s ease;
}

/* Sıralama butonu stili */
.ranking-button {
    //background-color: #3498db; /* Mavi arka plan */
    color: #0056b3;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 150px; /* Sabit genişlik ayarlama */
}

.ranking-button:hover {
    background-color: #2980b9; /* Koyu mavi hover efekti */
}

/* Logout butonu stili */
.logout-button {
    background-color: #3498db; /* Mavi arka plan */
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 150px; /* Sabit genişlik ayarlama */
}

.logout-button:hover {
    background-color: #2980b9; /* Koyu mavi hover efekti */
}

/* Puan Menüsü stilleri */
.score-menu {
    display: flex; /* Yatay hizalama */
    justify-content: space-between; /* İlk butonu sola, son butonu sağa yaslama */
    flex-wrap: nowrap; /* Sadece tek satırda kalmasını sağla */
    margin: 20px 10px; /* Dışarıdan alan */
    padding: 10px; /* İç boşluk */
}

.score-link {
    padding: 10px;
    background-color: #3498db; /* Mavi arka plan */
    color: white;
    text-decoration: none;
    text-align: center;
    border-radius: 5px; /* Yuvarlatılmış köşeler */
    transition: background-color 0.3s ease, transform 0.2s ease; /* Geçiş efektleri */
    width: 150px; /* Sabit genişlik ayarlama */
}

/* Mobil görünüm ayarları */
@media (max-width: 768px) {
    .score-menu {
        justify-content: space-between; /* Elemanlar arasındaki boşluk */
    }
    .score-link {
        width: 150px; /* Sabit genişlik ayarlama */
        margin-right:2px;
    }
}

.score-link:hover {
    background-color: green; /* Koyu mavi hover efekti */
    transform: scale(1.05); /* Hoverda biraz büyütme */

}

.score-item {
    text-align: center;
    margin: 10px 0;
}

.score-photo {
    width: 100px;
    height: 100px;
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
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.modal-button {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.modal-button:hover {
    background-color: #2980b9; /* Koyu mavi hover efekti */
}
</style>




