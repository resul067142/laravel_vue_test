<template>
    <div class="container-fluid home-container">
        <div class="row">
            <div class="col-12 text-center mb-3">
                <div class="weather-container d-flex justify-content-center align-items-center">
                    <div id="date" class="date me-3"></div>
                    <div id="clock" class="clock me-3"></div>
                    <div id="weather" class="weather"></div>
                </div>
            </div>
            <hr>
            <form class="auth-form p-3 mb-3">
                <div class="mb-3 text-center">
                    <label for="exampleInputEmail1" class="form-label fw-bold text-info fs-6">Kayıtlı E-posta adresi</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="E-postanızı girin" v-model="email" required>
                </div>
                <div class="mb-3 text-center">
                    <label for="exampleInputPassword1" class="form-label fw-bold text-warning fs-6">Uygulama Giriş Şifresi</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Şifrenizi girin" v-model="password" required>
                </div>
                <div class="mb-3 form-check d-flex justify-content-center align-items-center">
                    <input type="checkbox" class="form-check-input me-2" id="exampleCheck1" v-model="remember">
                    <label class="form-check-label fw-bold text-success fs-6" for="exampleCheck1">Beni hatırla</label>
                </div>
                <div class="d-flex justify-content-between">
                    <button @click.prevent="login" class="btn btn-primary w-45 fw-bold fs-6">Uygulamaya Giriş Yap</button>
                    <router-link to="/kayitol" class="btn btn-secondary w-45 fw-bold fs-6">Kayıt Ol</router-link>
                </div>
            </form>
            <hr>
            <div class="col-12 col-md-6 mb-4">
                <div class="card quiz-card">
                    <div class="card-body">
                        <div v-if="!yarismaBasladi" class="text-center">
                            <form @submit.prevent="start" class="quiz-start-form">
                                <div class="mb-3">
                                    <label for="photoUpload" class="m-2 fw-bold">FOTOGRAF EKLE</label>
                                    <div class="custom-file position-relative d-flex align-items-center mb-3">
                                        <input class="custom-file-input form-control" id="photoUpload" @change="onFileChange" type="file" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <input type="text" v-model="name" class="form-control" placeholder="Ad" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Yarışmaya Başla</button>
                            </form>
                        </div>
                        <div v-if="yarismaBasladi" class="row">
                            <div class="col-12 m-1 text-center">
                                <b>Hoşgeldin <strong>{{ name }}</strong> Başarılar Dileriz.</b>
                            </div>
                        </div>
                        <div v-if="yarismaBasladi && sorular && sorular.length && secilenSoru" class="row">
                            <div class="col-12 m-2">
                                <div class="text-center fs-4 mb-2">{{ secilenSoruSirasi + 1 }} / {{ sorular.length }}</div>
                                <div class="p-2 text-white mb-2" style="background-color: darkcyan; min-height: 50px;">{{ secilenSoru.soru }}</div>
                                <div @click="sec(1)" :class="['secenek', secilenSoru.secilen_cevap === 1 ? 'secilen' : '']">1 - {{ secilenSoru.secenek_1 }}</div>
                                <div @click="sec(2)" :class="['secenek', secilenSoru.secilen_cevap === 2 ? 'secilen' : '']">2 - {{ secilenSoru.secenek_2 }}</div>
                                <div @click="sec(3)" :class="['secenek', secilenSoru.secilen_cevap === 3 ? 'secilen' : '']">3 - {{ secilenSoru.secenek_3 }}</div>
                                <div @click="sec(4)" :class="['secenek', secilenSoru.secilen_cevap === 4 ? 'secilen' : '']">4 - {{ secilenSoru.secenek_4 }}</div>
                                <div v-if="skorKaydedildi">
                                    <div v-if="secilenSoru.dogru_cevap === secilenSoru.secilen_cevap" class="alert alert-success mt-2 text-center">DOĞRU</div>
                                    <div v-else class="alert alert-danger mt-2 text-center">YANLIŞ</div>
                                </div>
                                <div class="text-center mt-2 mb-2">
                                    <button class="btn btn-info m-1" v-if="secilenSoruSirasi != 0" type="button" @click="oncekiSoru"><</button>
                                    <button class="btn btn-info m-1" v-if="secilenSoruSirasi < sorular.length - 1" type="button" @click="sonrakiSoru">></button>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-success w-100 mt-2" @click="bitir" :disabled="bittiMi" v-if="yarismaBasladi">Bitir</button>
                            <div v-if="skorKaydedildi" class="mt-2 mb-2 fs-2">{{ yuzdeDogruCevap }}%</div>
                            <button @click="yenidenOyna" class="btn btn-primary" v-if="skorKaydedildi">Yeniden Oyna</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 mt-3 mt-md-0">
                <div class="card scoreboard-card">
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                            <tr>
                                <th>FOTOĞRAF</th>
                                <th>İSİM</th>
                                <th>PUAN</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="score in scores" :key="score.id">
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
    </div>
</template>

<script>
import { jsPDF } from 'jspdf';
import html2canvas from 'html2canvas';
import axios from 'axios';

export default {
    data() {
        return {
            email: '',
            password: '',
            remember: false,
            name: null,
            photo: null,
            sorular: null,
            secilenSoru: {},
            secilenSoruSirasi: 0,
            bitirKapali: true,
            yuzdeDogruCevap: null,
            skorKaydedildi: false,
            yarismaBasladi: false,
            scores: []
        }
    },
    computed: {
        bittiMi() {
            return this.sorular && !this.sorular.every(soru => soru.hasOwnProperty('secilen_cevap'));
        },
        imgStyle() {
            return {
                width: '80px',
                height: '80px',
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
            this.secilenSoru = {};
            this.secilenSoruSirasi = 0;
            this.yuzdeDogruCevap = null;
            this.sorular = null;
        },
        login() {
            axios.post('/api/login', {
                email: this.email,
                password: this.password,
                remember: this.remember
            }).then(res => {
                // Giriş başarılı ise yarışma sayfasına yönlendirin veya uygun aksiyonu alın
                this.showNotification("Başarıyla giriş yapıldı.", 'success');
                // Örneğin, kullanıcıyı dashboard'a yönlendirebilirsiniz:
                // this.$router.push('/dashboard');
            }).catch(error => {
                console.error("Giriş yapılırken hata oluştu:", error);
                this.showNotification("Giriş yapılırken bir hata oluştu.", 'error');
            });
        },
        start() {
            this.secilenSoruSirasi = 0;
            axios.get('/api/sorular').then(res => {
                this.sorular = res.data;
                console.log(this.sorular)
                this.secilenSoru = this.sorular[0];
                this.yarismaBasladi = true;
                window.addEventListener('beforeunload', this.handleBeforeUnload);
            }).catch(error => {
                console.error("Sorular alınırken hata oluştu:", error);
                this.showNotification("Sorular alınırken bir hata oluştu.", 'error');
            });
        },
        oncekiSoru() {
            if (this.secilenSoruSirasi > 0) {
                this.secilenSoruSirasi--;
                this.secilenSoru = this.sorular[this.secilenSoruSirasi];
                this.skorKaydedildi = false;
            }
        },
        sonrakiSoru() {
            if (this.secilenSoruSirasi < this.sorular.length - 1) {
                this.secilenSoruSirasi++;
                this.secilenSoru = this.sorular[this.secilenSoruSirasi];
                this.skorKaydedildi = false;
            }
        },
        sec(secenek) {
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
                }).catch(error => {
                    console.error("Skorlar alınırken hata oluştu:", error);
                    this.showNotification("Skorlar alınırken bir hata oluştu.", 'error');
                });
                window.removeEventListener('beforeunload', this.handleBeforeUnload);
            }).catch(error => {
                console.error("Skor kaydedilirken hata oluştu:", error);
                this.showNotification("Skor kaydedilirken bir hata oluştu.", 'error');
            });
        },
        updateClock() {
            const clockElement = document.getElementById("clock");
            setInterval(() => {
                const now = new Date();
                const timeString = now.toLocaleTimeString('tr-TR', {hour12: false});
                clockElement.innerText = `Saat: ${timeString}`;
            }, 1000);
        },
        updateWeather() {
            const weatherElement = document.getElementById("weather");
            const city = "Ankara";
            axios.get('https://api.open-meteo.com/v1/forecast?latitude=39.9334&longitude=32.8597&hourly=temperature_2m')
                .then(res => {
                    const temperature = res.data.hourly.temperature_2m[0];
                    weatherElement.innerHTML = `<span class='city'>${city}</span> - <span class='temperature'>${temperature}°C</span>`;
                })
                .catch(() => {
                    weatherElement.innerText = "Hava Durumu: Bilgi alınamadı";
                });
        },
        updateDate() {
            const dateElement = document.getElementById("date");
            const now = new Date();
            const dateString = now.toLocaleDateString('tr-TR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            dateElement.innerHTML = `<span class='date-text'>${dateString}</span>`;
        },
        handleBeforeUnload(event) {
            event.preventDefault();
            event.returnValue = '';
        },
        showNotification(message, type) {
            // Bildirim gösterme metodunuz
            // Örneğin, Vue Toastification veya başka bir bildirim kütüphanesi kullanabilirsiniz
            console.log(message); // Örneğin, konsola loglama
        }
    },
    mounted() {
        axios.get('/api/scores').then(res => {
            this.scores = res.data;
        }).catch(error => {
            console.error("Skorlar alınırken hata oluştu:", error);
            this.showNotification("Skorlar alınırken bir hata oluştu.", 'error');
        });
        this.updateClock();
        this.updateWeather();
        this.updateDate();
        window.addEventListener('beforeunload', this.handleBeforeUnload);
    },
    beforeUnmount() { // Vue 3 için beforeUnmount kullanılır
        window.removeEventListener('beforeunload', this.handleBeforeUnload);
    }
};
</script>

<style scoped>
.home-container {
    padding: 15px;
}
.weather-container {
    gap: 15px;
    color: #007bff;
    font-weight: bold;
    font-size: 1rem;
}
.date, .clock, .weather {
    background-color: #f0f8ff;
    padding: 5px;
    border-radius: 5px;
    color: #007bff;
    font-size: 0.8rem;
    text-align: center;
}
.auth-form {
    background: #41464b;
    border-radius: 8px;
}
.auth-form .form-control {
    max-width: 300px;
    margin: 0 auto;
    text-align: center;
}
.quiz-card, .scoreboard-card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.secenek {
    background: #f2f2f2;
    border-radius: 5px;
    padding: 10px;
    margin-top: 5px;
    cursor: pointer;
    transition: background 0.2s;
}
.secenek:hover {
    background: #e0e0e0;
}
.secenek.secilen {
    background: #d1ecf1;
    border: 1px solid #bee5eb;
}
@media (max-width: 576px) {
    .weather-container {
        font-size: 0.7rem;
        flex-direction: column;
    }
    .auth-form .form-control {
        width: 100%;
    }
}
.w-45 {
    width: 45%!important;
}
</style>
