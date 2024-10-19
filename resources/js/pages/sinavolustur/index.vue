<template>
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body text-center p-4">
                    <h3 class="mb-3 text-primary">Yeni Sınav Oluştur</h3>
                    <p class="text-muted mb-4">Sınav bilgilerini doldurarak yeni bir sınav oluşturabilirsiniz. Sınav seviyesini ve diğer detayları seçerek öğrenciler için uygun sınavlar hazırlayın.</p>
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
                        <div class="form-group mb-4">
                            <textarea
                                class="form-control"
                                v-model="exam.description"
                                placeholder="Sınav Açıklaması"
                                rows="3"
                                required
                            ></textarea>
                        </div>
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
                            <label class="form-label d-block mb-2">Sınav Seviyesi</label>
                            <div class="d-flex justify-content-around">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="exam.level" value="kolay" />
                                    <label class="form-check-label">Kolay</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="exam.level" value="orta" />
                                    <label class="form-check-label">Orta</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" v-model="exam.level" value="zor" />
                                    <label class="form-check-label">Zor</label>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block">Sınavı Oluştur</button>
                    </form>
                </div>
                <div class="card-footer text-center bg-primary text-white py-4">
                    <p class="mb-2">Yeni kategoriler ekleyerek çeşitliliği artırabilirsiniz!</p>
                    <button class="btn btn-light" @click="addCategory">Yeni Kategori Ekle</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            exam: {
                title: '',
                description: '',
                timeLimit: 0,
                level: ''
            }
        };
    },
    methods: {
        createExam() {
            // Sınav oluşturma işlemi
            if (this.exam.title && this.exam.description && this.exam.timeLimit > 0 && this.exam.level) {
                // Sınav verileri JSON formatında
                const newExam = {
                    title: this.exam.title,
                    description: this.exam.description,
                    timeLimit: this.exam.timeLimit,
                    level: this.exam.level
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
        }
    }
};
</script>

<style scoped>
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
