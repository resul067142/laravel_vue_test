<template>
    <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <div class="card shadow-lg border-0 rounded-lg w-100">
                <div class="card-body">
                    <h1 class="text-center">Soru Düzenle</h1>
                    <div class="alert alert-success text-center" v-if="alertShow">
                        Soru başarıyla güncellendi
                    </div>
                    <form @submit.prevent="updateProduct">
                        <input type="text" class="form-control mb-3" v-model="soru" placeholder="SORU" required>
                        <input type="text" class="form-control mb-3" v-model="secenek_1" placeholder="Secenek 1" required>
                        <input type="text" class="form-control mb-3" v-model="secenek_2" placeholder="Secenek 2" required>
                        <input type="text" class="form-control mb-3" v-model="secenek_3" placeholder="Secenek 3" required>
                        <input type="text" class="form-control mb-3" v-model="secenek_4" placeholder="Secenek 4" required>

                        <div class="form-group mb-3">
                            <label for="dogru_cevap">Doğru Seçeneği Seçiniz</label>
                            <select id="dogru_cevap" class="form-control" v-model="dogru_cevap" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Güncelle</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';

export default {
    data() {
        return {
            soru: '',
            secenek_1: '',
            secenek_2: '',
            secenek_3: '',
            secenek_4: '',
            dogru_cevap: 0,
            alertShow: false,
        }
    },
    methods: {
        updateProduct() {
            const soru = this.soru;
            const secenek_1 = this.secenek_1;
            const secenek_2 = this.secenek_2;
            const secenek_3 = this.secenek_3;
            const secenek_4 = this.secenek_4;
            const dogru_cevap = this.dogru_cevap;

            const authToken = localStorage.getItem('authToken');

            const headers = {
                'Authorization': 'Bearer ' + authToken
            };

            axios.put('/api/sorular/' + this.$route.params.id,
                { soru, secenek_1, secenek_2, secenek_3, secenek_4, dogru_cevap }, { headers }).then(res => {
                console.log(res.data);
                this.alertShow = true;
                setTimeout(() => {
                    this.alertShow = false;
                }, 2000);
            });
        }
    },
    created() {
        const authToken = localStorage.getItem('authToken');

        const headers = {
            'Authorization': 'Bearer ' + authToken
        };

        axios.get('/api/sorular/' + this.$route.params.id, { headers }).then(response => {
            console.log(response.data);
            const soru = response.data;
            this.soru = soru.soru;
            this.secenek_1 = soru.secenek_1;
            this.secenek_2 = soru.secenek_2;
            this.secenek_3 = soru.secenek_3;
            this.secenek_4 = soru.secenek_4;
            this.dogru_cevap = soru.dogru_cevap;
        });
    }
}
</script>
