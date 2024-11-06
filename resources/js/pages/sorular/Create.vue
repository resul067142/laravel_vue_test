<template>
    <div id="app">
        <div class="main-content" style="margin-top: 0;">
            <!-- Soru ekleme sayfası -->
            <div class="row justify-content-center align-items-start" style="margin-top: 0;">
                <div class="col-12 col-md-8 col-lg-6 col-xl-4">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-body text-center p-4">
                            <h1 class="text-primary">SORU EKLE</h1>
                            <div class="alert alert-success" v-if="alertShow">
                                Soru başarıyla kaydedildi
                            </div>
                            <form @submit.prevent="Soruyu_Kayit_Et">
                                <input
                                    type="text"
                                    class="form-control mb-3"
                                    v-model="soru"
                                    placeholder="SORU"
                                    required
                                />
                                <input
                                    type="text"
                                    class="form-control mb-3"
                                    v-model="secenek_1"
                                    placeholder="Seçenek 1"
                                    required
                                />
                                <input
                                    type="text"
                                    class="form-control mb-3"
                                    v-model="secenek_2"
                                    placeholder="Seçenek 2"
                                    required
                                />
                                <input
                                    type="text"
                                    class="form-control mb-3"
                                    v-model="secenek_3"
                                    placeholder="Seçenek 3"
                                    required
                                />
                                <input
                                    type="text"
                                    class="form-control mb-3"
                                    v-model="secenek_4"
                                    placeholder="Seçenek 4"
                                    required
                                />
                                <div class="form-group mb-3">
                                    <label>Doğru Cevap Seçiniz</label>
                                    <select class="form-control" v-model="dogru_cevap" required>
                                        <option value="1" selected>1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                                <button class="btn btn-primary w-100" type="submit">Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            hover: false,
            isAuth: false,
            showModal: true, // Modal pencere başlangıçta görünür
            scores: [], // Skorları tutacak dizi
            isRankingVisible: false, // Sıralama menüsünün görünürlüğü
            soru: '',
            secenek_1: '',
            secenek_2: '',
            secenek_3: '',
            secenek_4: '',
            dogru_cevap: 0,
            alertShow: false,
            exam: {
                title: '',
                description: '',
                timeLimit: 0,
                level: '',
                topic: '',
                questions: []
            }
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
            this.$router.push('/admin');
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
        },
        Soruyu_Kayit_Et() {
            const { soru, secenek_1, secenek_2, secenek_3, secenek_4, dogru_cevap } = this;
            const authToken = localStorage.getItem('authToken');
            const headers = {
                Authorization: 'Bearer ' + authToken,
            };

            axios.post('/api/sorular', { soru, secenek_1, secenek_2, secenek_3, secenek_4, dogru_cevap }, { headers }).then(res => {
                this.alertShow = true;
                setTimeout(() => {
                    this.alertShow = false;
                }, 2000);

                this.soru = '';
                this.secenek_1 = '';
                this.secenek_2 = '';
                this.secenek_3 = '';
                this.secenek_4 = '';
                this.dogru_cevap = 0;
            });
        },
        addCategory() {
            alert('Yeni kategori ekleme özelliği henüz aktif değil.');
        },
        addQuestion() {
            this.exam.questions.push({ text: '', options: [{ text: '' }, { text: '' }, { text: '' }, { text: '' }], correctOption: null });
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
.btn-warning:hover {
    background-color: red !important;
    border-color: red !important;
}
/* Sınav oluşturma sayfası için şık ve mobil uyumlu stil */
.card {
    background-color: #e3f2fd; /* Light blue background for the card */
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

h1 {
    font-size: 1.8rem;
    color: #1976d2;
}

.form-control {
    font-size: 1.1rem;
    padding: 1rem;
    border-radius: 10px;
    border: 1px solid #1976d2;
    box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
}

.btn {
    font-size: 1.1rem;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
}

.form-label {
    font-weight: bold;
    color: #333;
}

.card-footer {
    background-color: #0056b3;
    color: #ffffff;
    border-top: none;
    border-radius: 0 0 15px 15px;
    position: sticky;
    bottom: 0;
    width: 100%;
}

.alert {
    background-color: #c8e6c9; /* Light green for success alert */
    color: #2e7d32; /* Dark green text */
    margin-bottom: 15px;
}

.btn-primary {
    background-color: #1976d2;
    border: none;
}

.btn-primary:hover {
    background-color: #0d47a1;
}

/* Mobil görünüm için optimize edilmiş stil */
@media (max-width: 576px) {
    .card {
        margin: 0 10px;
    }

    h1 {
        font-size: 1.5rem;
    }

    .form-control {
        font-size: 1rem;
        padding: 0.75rem;
    }

    .btn {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }
}
</style>
