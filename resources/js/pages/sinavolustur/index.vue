<template>
    <div id="app">

        <div class="main-content" style="margin-top: 0;">
            <!-- Sınav oluşturma sayfası -->
            <div class="row justify-content-center align-items-start" style="margin-top: 0;">
                <div class="col-12 col-md-8 col-lg-6 col-xl-4">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-body text-center p-4">
                            <h3 class="mb-3 text-primary">Yeni Sınav Oluştur</h3>
                            <p class="text-muted mb-4">Sınav bilgilerini doldurunuz.</p>
                            <form @submit.prevent="createExam">
                                <div class="form-group mb-4">
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="exam.title"
                                        placeholder="Sınav Adı"
                                        required
                                    />
                                </div>

                                <p class="text-muted mb-4">Sınav Süresi Giriniz</p>
                                <div class="form-group mb-4">
                                    <input
                                        class="form-control"
                                        type="number"
                                        v-model="exam.timeLimit"
                                        placeholder="Zaman Sınırı (dakika)"
                                        required
                                    />
                                </div>

                                <div class="form-group mb-4">
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="exam.topic"
                                        placeholder="Sınav Konusu"
                                        required
                                    />
                                </div>
                                <div class="form-group mb-4">
                                    <h5 class="text-primary">Sınav Soruları</h5>
                                    <div v-for="(question, index) in exam.questions" :key="index" class="mb-3">
                                        <input
                                            class="form-control mb-2"
                                            type="text"
                                            v-model="question.text"
                                            placeholder="Soru Metni"
                                            required
                                        />
                                        <div v-for="(option, optIndex) in question.options" :key="optIndex" class="form-group mb-2">
                                            <input
                                                class="form-control"
                                                type="text"
                                                v-model="option.text"
                                                placeholder="Seçenek"
                                                required
                                            />
                                            <div class="form-check form-check-inline">
                                                <input
                                                    class="form-check-input"
                                                    type="radio"
                                                    v-model="question.correctOption"
                                                    :value="optIndex"
                                                />
                                                <label class="form-check-label">Doğru Seçenek</label>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary mt-3" @click="addQuestion">Yeni Soru Ekle</button>
                                </div>
                                <button class="btn btn-warning btn-block" style="font-size: 1.3rem; padding: 1rem 2rem;" @mouseover="hover = true" @mouseleave="hover = false" :style="hover ? 'background-color: red; border-color: red;' : 'background-color: #ffc107; border-color: #ffc107;'">Sınavı Oluştur</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Alt kısımda ikonlu menü -->

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
            exam: {
                title: '',
                description: '',
                timeLimit: 30,
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
        createExam() {
            // Sınav oluşturma işlemi
            if (this.exam.title && this.exam.description && this.exam.timeLimit > 0 && this.exam.level && this.exam.topic && this.exam.questions.length > 0) {
                // Sınav verileri JSON formatında
                const newExam = {
                    title: this.exam.title,
                    description: this.exam.description,
                    timeLimit: this.exam.timeLimit,
                    level: this.exam.level,
                    topic: this.exam.topic,
                    questions: this.exam.questions
                };
                console.log('Sınav oluşturuluyor...', newExam);
                // Örneğin, bir API isteği yaparak sınavı oluşturabilirsiniz
                // axios.post('/api/exams', newExam)
                //   .then(response => {
                //     // Başarılı bir şekilde sınav oluşturulduktan sonra yapılacak işlemler
                //     alert('Sınav başarıyla oluşturuldu!');
                //   })
                //   .catch(error => {
                //     console.error('Sınav oluşturma hatası:', error);
                //   });
            } else {
                alert('Lütfen tüm alanları doldurun!');
            }
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
    background-color: #fff;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

h3 {
    font-size: 1.8rem;
    color: #0056b3;
}

.form-control {
    font-size: 1.1rem;
    padding: 1rem;
    border-radius: 10px;
    border: 1px solid #ced4da;
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

/* Mobil görünüm için optimize edilmiş stil */
@media (max-width: 576px) {
    .card {
        margin: 0 10px;
    }

    h3 {
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
