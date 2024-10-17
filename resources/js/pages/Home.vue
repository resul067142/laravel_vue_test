<template>
    <div class="row">
        <div class="col-12 text-center mb-3">
            <div class="weather-container d-flex justify-content-center align-items-center">
                <div id="date" class="date me-3"></div>
                <div id="clock" class="clock me-3"></div>
                <div id="weather" class="weather"></div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div v-if="!yarismaBasladi" class="row">
                        <div class="col-12 text-center">
                            <form @submit.prevent="start">
                                <div class="form-group">
                                    <label class="m-2" for="photoUpload"><b>FOTOGRAF EKLE</b></label>
                                    <div class="custom-file">
                                        <input class="custom-file-input" id="photoUpload" @change="onFileChange" type="file" required>
                                        <label class="custom-file-label" for="photoUpload">Dosya Seçin</label>

                                        <small style="margin-left: 20px" class="form-text text-muted">JPEG, PNG formatında dosya yükleyebilirsiniz.</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" v-model="name" class="form-control" placeholder="Ad" required>
                                </div>
                                <button type="submit" class="btn btn-primary mt-1">Yarışmaya Başla</button>
                            </form>
                        </div>
                    </div>
                    <div v-if="yarismaBasladi" class="row">
                        <div class="col-12 m-1 text-center">
                            <b>Hoşgeldin <strong>{{name}}</strong> Başarılar Dileriz.</b>
                        </div>
                    </div>
                    <div v-if="yarismaBasladi && sorular && sorular.length && secilenSoru" class="row">
                        <div class="col-12 m-2">
                            <div class="text-center" style="font-size: 24px; margin-bottom: 10px">
                                {{secilenSoruSirasi + 1}} / {{sorular.length}}
                            </div>
                            <div class="p-2" style="background-color: darkcyan; color: white; min-height: 50px;">
                                {{secilenSoru.soru}}
                            </div>

                            <div @click="sec(1)" :class="'secenek' +  (secilenSoru.secilen_cevap && secilenSoru.secilen_cevap === 1 ? ' secilen' : '')">
                                1 - {{secilenSoru.secenek_1}}
                            </div>
                            <div @click="sec(2)" :class="'secenek' +  (secilenSoru.secilen_cevap && secilenSoru.secilen_cevap === 2 ? ' secilen' : '')">
                                2 - {{secilenSoru.secenek_2}}
                            </div>
                            <div @click="sec(3)" :class="'secenek' +  (secilenSoru.secilen_cevap && secilenSoru.secilen_cevap === 3 ? ' secilen' : '')">
                                3 - {{secilenSoru.secenek_3}}
                            </div>
                            <div @click="sec(4)" :class="'secenek' +  (secilenSoru.secilen_cevap && secilenSoru.secilen_cevap === 4 ? ' secilen' : '')">
                                4 - {{secilenSoru.secenek_4}}
                            </div>
                            <div v-if="skorKaydedildi">
                                <div v-if="secilenSoru.dogru_cevap === secilenSoru.secilen_cevap" class="alert alert-success mt-2 text-center">
                                    DOĞRU
                                </div>
                                <div v-else class="alert alert-danger mt-2 text-center">
                                    YANLIŞ
                                </div>
                            </div>

                            <div class="text-center mt-2 mb-2">
                                <button class="btn btn-info m-1" v-if="secilenSoruSirasi != 0" type="button" @click="oncekiSoru"> {{ '<' }} </button>
                                <button class="btn btn-info m-1" v-if="secilenSoruSirasi < sorular.length - 1" type="button" @click="sonrakiSoru"> {{ '>' }} </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 m-2 text-center">
                            <button class="btn btn-success" style="width: 100%;" @click="bitir" :disabled="bittiMi" v-if="yarismaBasladi">Bitir</button>
                            <div v-if="skorKaydedildi" class="mt-2 mb-2" style="font-size: 28px;">
                                {{yuzdeDogruCevap}}%
                            </div>
                            <button @click="yenidenOyna" class="btn btn-primary" v-if="skorKaydedildi">Yeniden Oyna</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-3 mt-md-0">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>FOTOĞRAF</th>
                            <th>İSİM</th>
                            <th>PUAN</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="score in scores">
                            <td><img class="rounded-circle img-fluid" :src="'uploads/' + score.img_url" :style="imgStyle" alt=""/></td>
                            <td>{{ score.name }}</td>
                            <td>{{ score.score }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            name: null,
            photo: null,
            sorular: null,
            secilenSoru: {},
            secilenSoruSirasi: 0,
            bitirKapali: true,
            yuzdeDogruCevap: null,
            skorKaydedildi: false,
            yarismaBasladi: false,
            scores: [],
        }
    },
    computed: {
        bittiMi() {
            return this.sorular && !this.sorular.every(soru => soru.hasOwnProperty('secilen_cevap'));
        },
        imgStyle() {
            return {
                width: '100px',
                height: '100px',
                objectFit: 'cover'
            };
        }
    },
    methods: {
        onFileChange(event) {
            this.photo = event.target.files[0];
        },
        yenidenOyna() {
            this.yarismaBasladi = false;
            this.skorKaydedildi = false;
            this.name = null;
        },
        start() {
            this.secilenSoruSirasi = 0;
            axios.get('/api/sorular').then(res => {
                this.sorular = res.data;
                this.secilenSoru = this.sorular[0];
                this.yarismaBasladi = true;
                window.addEventListener('beforeunload', this.handleBeforeUnload);
            });
        },
        oncekiSoru() {
            this.secilenSoruSirasi--;
            this.secilenSoru = this.sorular[this.secilenSoruSirasi];
        },
        sonrakiSoru() {
            this.secilenSoruSirasi++;
            this.secilenSoru = this.sorular[this.secilenSoruSirasi];
        },
        sec(secenek){
            if (!this.skorKaydedildi) {
                this.sorular[this.secilenSoruSirasi].secilen_cevap = secenek;
            }
        },
        bitir() {
            const soruSayisi = this.sorular.length;
            const dogruCevapSayisi = this.sorular.filter(soru => soru.dogru_cevap === soru.secilen_cevap).length;
            this.yuzdeDogruCevap = Math.ceil(dogruCevapSayisi / soruSayisi * 100);

            const formData = new FormData();
            formData.append('photo', this.photo);
            formData.append('name', this.name);
            formData.append('score', this.yuzdeDogruCevap);

            axios.post('/api/scores', formData).then(() => {
                this.skorKaydedildi = true;
                axios.get('/api/scores').then(res => {
                    this.scores = res.data;
                });
                window.removeEventListener('beforeunload', this.handleBeforeUnload);
            });
        },
        updateClock() {
            const clockElement = document.getElementById("clock");
            setInterval(() => {
                const now = new Date();
                const timeString = now.toLocaleTimeString('tr-TR', { hour12: false });
                clockElement.innerText = `Saat: ${timeString}`;
            }, 1000);
        },
        updateWeather() {
            const weatherElement = document.getElementById("weather");
            // Example API call for weather with city information (dummy data)
            const city = "Ankara";
            axios.get('https://api.open-meteo.com/v1/forecast?latitude=39.9334&longitude=32.8597&hourly=temperature_2m').then(res => {
                const temperature = res.data.hourly.temperature_2m[0];
                weatherElement.innerHTML = `<span class='city'>${city}</span> - <span class='temperature'>${temperature}°C</span>`;
            }).catch(() => {
                weatherElement.innerText = "Hava Durumu: Bilgi alınamadı";
            });
        },
        updateDate() {
            const dateElement = document.getElementById("date");
            const now = new Date();
            const dateString = now.toLocaleDateString('tr-TR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            dateElement.innerHTML = `<span class='date-text'>${dateString}</span>`;
        },
        handleBeforeUnload(event) {
            event.preventDefault();
            event.returnValue = '';
        },
        confirmNavigation() {
            window.addEventListener('beforeunload', (event) => {
                event.preventDefault();
                event.returnValue = '';
            });
        }
    },
    mounted() {
        axios.get('/api/scores').then(res => {
            this.scores = res.data;
        });
        this.updateClock();
        this.updateWeather();
        this.updateDate();
        window.addEventListener('beforeunload', this.handleBeforeUnload);
    },
    beforeDestroy() {
        window.removeEventListener('beforeunload', this.handleBeforeUnload);
    }
};
</script>

<style>
.custom-file {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.custom-file-input {
    position: absolute;
    z-index: 1;
    opacity: 0;
    height: 100%;
    width: 100%;
}

.custom-file-label {
    display: block;
    padding: 0.5rem 1rem;
    border: 2px dashed #007bff;
    border-radius: 0.25rem;
    background-color: #f8f9fa;
    transition: border-color 0.2s ease-in-out, background-color 0.2s ease-in-out;
    font-weight: 500;
    color: #007bff;
    text-align: center;
}

.custom-file-input:focus + .custom-file-label {
    border-color: #0056b3;
}

.custom-file-input:valid + .custom-file-label {
    color: #495057;
}

.custom-file-input:invalid + .custom-file-label {
    color: #dc3545;
}

.weather-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    color: #007bff;
    font-weight: bold;
    font-size: 1rem; /* Genel font boyutu */
}

.date, .clock, .weather {
    background-color: #f0f8ff;
    padding: 5px;
    border-radius: 5px;
    color: #007bff;
    font-size: 0.8rem; /* Tarih, saat ve derece bilgileri */
    text-align: center;
}

@media (max-width: 576px) {
    .weather-container {
        flex-direction: row;
        font-size: 0.6rem; /* Mobil cihazlarda daha küçük boyut */
    }
    .date, .clock, .weather {
        margin-bottom: 0;
        flex: 1;
        text-align: center;
    }
}
</style>
