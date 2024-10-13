<template>
    <div>
        <div class="nav-container">
            <router-link to="/" class="nav-link">Ana Sayfa</router-link>
            <router-link v-if="isAuth" to="/sorular" class="nav-link">SORULAR</router-link>
            <router-link v-if="!isAuth" to="/admin" class="nav-link">ADMİN GİRİŞ</router-link>
            <button v-else type="button" @click="logout" class="logout-button">Admin Çıkış</button>
        </div>
        <router-view></router-view>

        <!-- Sınav uygulaması onay penceresi -->
        <div v-if="showModal" class="modal">
            <div class="modal-content">
                <h2>Sınav Uygulaması</h2>
                <p> "Hoş geldiniz! Bu Sayfa, Yazılım Dersi Sınav Uygulaması'dır.
                    Kayıt olmadan sınava katılmak için yalnızca bir fotoğraf yükleyip adınızı girmeniz yeterlidir. <hr>
                    Sınav sonuçlarınızı aldıktan sonra, genel sıralama listesinde nerede yer aldığınızı görebilirsiniz.
                    Başarılar dileriz!"</p>
                <button @click="closeModal">Tamam</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isAuth: false,
            showModal: true // Modal pencere başlangıçta görünür
        }
    },
    methods: {
        logout() {
            localStorage.removeItem('authToken');
            window.location.href = '/admin';
        },
        closeModal() {
            this.showModal = false; // Modal pencereyi kapat
        }
    },
    created() {
        this.isAuth = localStorage.getItem('authToken');
    }
}
</script>

<style scoped>
/* Navigation container styles */
.nav-container {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    padding: 20px;
    background-color: #2c3e50;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    font-family: 'Poppins', sans-serif;
}

/* Link styles */
.nav-link {
    margin: 0 15px;
    font-size: 18px;
    color: #ecf0f1;
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: color 0.3s ease, transform 0.3s ease;
    position: relative;
}

/* Hover effect for links: color transition and subtle scaling */
.nav-link:hover {
    color: #f39c12;
    transform: scale(1.1);
}

/* Logout button */
.logout-button {
    margin: 0 15px;
    font-size: 18px;
    color: #e74c3c;
    background-color: transparent;
    border: none;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: color 0.3s ease, transform 0.3s ease;
}

.logout-button:hover {
    color: #c0392b;
    transform: scale(1.1);
}

/* Responsive styles for mobile */
@media (max-width: 600px) {
    .nav-container {
        flex-direction: column;
        padding: 10px;
    }

    .nav-link,
    .logout-button {
        margin: 10px 0;
        font-size: 16px;
    }
}

/* Modal styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000; /* Modal ön planda olsun */
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
}

.modal-content button {
    margin-top: 10px;
    padding: 10px 20px;
    font-size: 16px;
}
</style>
