<template>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <h1>SORU EKLE</h1>
                    <div class="alert alert-success" v-if="alertShow">
                        Soru başarıyla kaydedildi
                    </div>
                    <form @submit.prevent="Soruyu_Kayit_Et">
                        <input type="text" class="form-control" v-model="soru" placeholder="SORU" required> <br>
                        <input type="text" class="form-control" v-model="secenek_1" placeholder="secenek 1" required> <br>
                        <input type="text" class="form-control" v-model="secenek_2" placeholder="secenek 2" required> <br>
                        <input type="text" class="form-control" v-model="secenek_3" placeholder="secenek 3" required> <br>
                        <input type="text" class="form-control" v-model="secenek_4" placeholder="secenek 4" required> <br>

                        <div class="form-group">
                            <label>Doğru Cevap Seçiniz</label>
                            <select class="form-control" v-model="dogru_cevap" required>
                                <option value="1" selected>1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>


                        <br>
                        <button class="btn btn-primary" type="submit">Ekle</button>
                    </form>
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
        }
     },
     methods: {
        async Soruyu_Kayit_Et() {
            const soru = this.soru
            const secenek_1 = this.secenek_1
            const secenek_2 = this.secenek_2
            const secenek_3 = this.secenek_3
            const secenek_4 = this.secenek_4

            const dogru_cevap = this.dogru_cevap

            const authToken = localStorage.getItem('authToken')

            const headers = {
                'Authorization': 'Bearer ' + authToken
            }


            await axios.post('/api/sorular', {soru, secenek_1,secenek_2,secenek_3,secenek_4,dogru_cevap }, {headers}).then(res => {

                this.alertShow = true
                setTimeout(() => {
                    this.alertShow = false
                }, 2000)


                this.soru = null
                this.secenek_1 = null
                this.secenek_2 = null
                this.secenek_3 = null
                this.secenek_4 = null

                this.dogru_cevap = 0
            })

        }
     }
    }
</script>
