<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="text-center">SORU EKLE</h1>
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
</template>

<script>
export default {
    data() {
        return {
            soru: '',
            secenek_1: '',
            secenek_2: '',
            secenek_3: '',
            secenek_4: '',
            dogru_cevap: 0,
            category: 2,
            alertShow: false,
        };
    },
    methods: {
        async Soruyu_Kayit_Et() {
            const { soru, secenek_1, secenek_2, secenek_3, secenek_4, dogru_cevap } = this;
            const authToken = localStorage.getItem('authToken');
            const headers = {
                Authorization: 'Bearer ' + authToken,
            };

            await axios.post('/api/sorular', { soru, secenek_1, secenek_2, secenek_3, secenek_4, dogru_cevap }, { headers }).then(res => {
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
    },
};
</script>

<style scoped>
.container {
    margin-top: 20px;
}

.card {
    background-color: #e3f2fd; /* Light blue background for the card */
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #1976d2; /* Medium blue for the title */
}

.alert {
    background-color: #c8e6c9; /* Light green for success alert */
    color: #2e7d32; /* Dark green text */
    margin-bottom: 15px;
}

.form-control {
    border: 1px solid #1976d2; /* Border color matching the theme */
    border-radius: 5px;
}

.form-control:focus {
    border-color: #0d47a1; /* Darker blue on focus */
}

.btn-primary {
    background-color: #1976d2; /* Primary button color */
    border: none;
}

.btn-primary:hover {
    background-color: #0d47a1; /* Darker blue on hover */
}

.mb-3 {
    margin-bottom: 1rem; /* Margin between inputs */
}
</style>
