<template>
    <div class="row">
        <div class="col-12 text-center mb-3"> <!-- Hava durumu, tarih ve saat bilgilerini içeren satır: Bu satırda hava durumu, tarih ve saati gösterecek bir kapsayıcı var. Kullanıcıya bu bilgileri toplu halde sunmak amaçlanmıştır. -->
            <div class="weather-container d-flex justify-content-center align-items-center">
                <div id="date" class="date me-3"></div> <!-- Tarih bilgisi gösterimi: Günün tarihini gösteren alan. -->
                <div id="clock" class="clock me-3"></div> <!-- Saat bilgisi gösterimi: Anlık saati göstermek için kullanılır. -->
                <div id="weather" class="weather"></div> <!-- Hava durumu bilgisi gösterimi: Kullanıcının bulunduğu bölgedeki hava durumunu gösterir. -->
            </div>
        </div>

        <hr> <form style="background:#41464b"> <!-- E-posta ve şifre girişi için form: Kullanıcının e-posta ve şifre bilgilerini girmesi gereken form. -->
            <div class="mb-3 text-center">
                <label for="exampleInputEmail1" class="form-label fw-bold text-info fs-6">Kayıtlı E-posta adresi</label>
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="E-postanızı girin" required> <!-- E-posta giriş alanı: Kullanıcının kayıtlı e-posta adresini girmesi gereken alan. -->
            </div>
            <div class="mb-3 text-center">
                <label for="exampleInputPassword1" class="form-label fw-bold text-warning fs-6">Uygulama Giriş Şifresi</label>
                <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Şifrenizi girin" required> <!-- Şifre giriş alanı: Kullanıcının uygulamaya giriş yapabilmesi için gerekli olan şifreyi girdiği alan. -->
            </div>
            <div class="mb-3 form-check d-flex justify-content-center align-items-center">
                <input type="checkbox" class="form-check-input me-2" id="exampleCheck1">
                <label class="form-check-label fw-bold text-success fs-6" for="exampleCheck1">Beni hatırla</label> <!-- Beni hatırla kutucuğu: Kullanıcının giriş bilgilerini hatırlamak için kullanılan seçenek. -->
            </div>
            <div class="d-flex justify-content-between">
                <router-link to="/giris" class="btn btn-primary w-44 fw-bold fs-6">UYGULAMAYA GİRİŞ YAP</router-link> <!-- Giriş sayfasına yönlendirme bağlantısı: Kullanıcının uygulamaya giriş yapmasını sağlayacak bağlantı. -->
                <router-link to="/kayitol" class="btn btn-primary btn-block btn-lg">Kayıt Ol</router-link> <!-- Kayıt ol sayfasına yönlendirme bağlantısı: Kullanıcının kayıt olmasını sağlayacak bağlantı. -->
            </div>
        </form>

        <hr><div class="col-12 col-md-6"> <!-- Yarışma formu ve sorular için kart bileşeni: Yarışmanın başlatılması ve soruların gösterimi için kullanılan kart. -->
            <div class="card">
                <div class="card-body">
                    <div v-if="!yarismaBasladi" class="row"> <!-- Yarışma başlamadıysa yarışmayı başlatmak için form: Yarışma başlamamışsa kullanıcının yarışmaya katılmasını sağlayacak form. -->
                        <div class="col-12 text-center">
                            <form @submit.prevent="start">
                                <div class="form-group">
                                    <label class="m-2" for="photoUpload"><b>FOTOGRAF EKLE</b></label>
                                    <div class="custom-file">
                                        <input class="custom-file-input" id="photoUpload" @change="onFileChange" type="file" required>
                                        <label class="custom-file-label" for="photoUpload">Dosya Seçin</label>
                                        <small style="margin-left: 20px" class="form-text text-muted">JPEG, PNG formatında dosya yükleyebilirsiniz.</small> <!-- Kabul edilen dosya formatları hakkında bilgi: Kullanıcının hangi formatlarda dosya yükleyebileceğini belirtir. -->
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" v-model="name" class="form-control" placeholder="Ad" required> <!-- İsim giriş alanı: Kullanıcının yarışmaya katılmak için adını girdiği alan. -->
                                </div>
                                <button type="submit" class="btn btn-primary mt-1">Yarışmaya Başla</button> <!-- Yarışmayı başlat butonu: Kullanıcının yarışmayı başlatmasını sağlayan buton. -->
                            </form>
                        </div>
                    </div>
                    <div v-if="yarismaBasladi" class="row"> <!-- Yarışma başladıktan sonra hoş geldin mesajı: Kullanıcı yarışmayı başlattıktan sonra ekranda hoş geldin mesajı gösterilir. -->
                        <div class="col-12 m-1 text-center">
                            <b>Hoşgeldin <strong>{{ name }}</strong> Başarılar Dileriz.</b>
                        </div>
                    </div>
                    <div v-if="yarismaBasladi && sorular && sorular.length && secilenSoru" class="row"> <!-- Yarışma başladıysa soruların gösterimi: Yarışma başladıktan sonra kullanıcının karşısına sorular gelir. -->
                        <div class="col-12 m-2">
                            <div class="text-center" style="font-size: 24px; margin-bottom: 10px"> {{ secilenSoruSirasi + 1 }} / {{ sorular.length }} </div> <!-- Soru numarası ve toplam soru sayısı gösterimi: Kullanıcıya kaçıncı soruda olduğu ve toplam soru sayısı gösterilir. -->
                            <div class="p-2" style="background-color: darkcyan; color: white; min-height: 50px;"> {{ secilenSoru.soru }} </div> <!-- Soru metninin gösterimi: Kullanıcıya yöneltilen sorunun metni bu alanda gösterilir. -->
                            <div @click="sec(1)" :class="'secenek' +  (secilenSoru.secilen_cevap && secilenSoru.secilen_cevap === 1 ? ' secilen' : '')"> 1 - {{ secilenSoru.secenek_1 }} </div> <!-- Soru seçeneği 1: Kullanıcının seçebileceği ilk seçenek. -->
                            <div @click="sec(2)" :class="'secenek' +  (secilenSoru.secilen_cevap && secilenSoru.secilen_cevap === 2 ? ' secilen' : '')"> 2 - {{ secilenSoru.secenek_2 }} </div> <!-- Soru seçeneği 2: Kullanıcının seçebileceği ikinci seçenek. -->
                            <div @click="sec(3)" :class="'secenek' +  (secilenSoru.secilen_cevap && secilenSoru.secilen_cevap === 3 ? ' secilen' : '')"> 3 - {{ secilenSoru.secenek_3 }} </div> <!-- Soru seçeneği 3: Kullanıcının seçebileceği üçüncü seçenek. -->
                            <div @click="sec(4)" :class="'secenek' +  (secilenSoru.secilen_cevap && secilenSoru.secilen_cevap === 4 ? ' secilen' : '')"> 4 - {{ secilenSoru.secenek_4 }} </div> <!-- Soru seçeneği 4: Kullanıcının seçebileceği dördüncü seçenek. -->
                            <div v-if="skorKaydedildi">
                                <div v-if="secilenSoru.dogru_cevap === secilenSoru.secilen_cevap" class="alert alert-success mt-2 text-center"> DOĞRU </div> <!-- Cevabın doğru olduğunu gösteren alan: Kullanıcı doğru cevap verdiğinde bu mesaj gösterilir. -->
                                <div v-else class="alert alert-danger mt-2 text-center"> YANLIŞ </div> <!-- Cevabın yanlış olduğunu gösteren alan: Kullanıcı yanlış cevap verdiğinde bu mesaj gösterilir. -->
                            </div>
                            <div class="text-center mt-2 mb-2">
                                <button class="btn btn-info m-1" v-if="secilenSoruSirasi != 0" type="button" @click="oncekiSoru"> {{ '<' }} </button> <!-- Önceki soruya geçiş butonu: Kullanıcı bir önceki soruya geçiş yapabilir. -->
                                <button class="btn btn-info m-1" v-if="secilenSoruSirasi < sorular.length - 1" type="button" @click="sonrakiSoru"> {{ '>' }} </button> <!-- Sonraki soruya geçiş butonu: Kullanıcı bir sonraki soruya geçiş yapabilir. -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 m-2 text-center">
                            <button class="btn btn-success" style="width: 100%;" @click="bitir" :disabled="bittiMi" v-if="yarismaBasladi">Bitir</button> <!-- Yarışmayı bitir butonu: Yarışma sona erdiğinde kullanıcının bitirmesi için buton. -->
                            <div v-if="skorKaydedildi" class="mt-2 mb-2" style="font-size: 28px;"> {{ yuzdeDogruCevap }}% </div> <!-- Doğru cevap yüzdesini gösteren alan: Kullanıcının verdiği doğru cevapların yüzdesi burada gösterilir. -->
                            <button @click="yenidenOyna" class="btn btn-primary" v-if="skorKaydedildi">Yeniden Oyna</button> <!-- Yeniden oyna butonu: Kullanıcı yarışmayı tekrar başlatabilir. -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-3 mt-md-0"> <!-- Yarışma sıralamasını gösteren kart bileşeni: Yarışma sonuçlarını ve kullanıcıların sıralamasını gösterir. -->
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
                            <td><img class="rounded-circle img-fluid" :src="'uploads/' + score.img_url" :style="imgStyle" alt=""/></td> <!-- Fotoğraf sütunu: Yarışmacının fotoğrafını gösterir. -->
                            <td>{{ score.name }}</td> <!-- İsim sütunu: Yarışmacının ismini gösterir. -->
                            <td>{{ score.score }}</td> <!-- Puan sütunu: Yarışmacının kazandığı puanı gösterir. -->
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
            name: null, // Kullanıcının ismini tutar (yarışma katılımı için gerekli olan isim bilgisi)
            photo: null, // Kullanıcının fotoğrafını tutar (profil resmi olarak kullanılabilir)
            sorular: null, // Yarışma için alınan tüm soruları depolar
            secilenSoru: {}, // Kullanıcının şu anda cevaplamakta olduğu soruyu tutar
            secilenSoruSirasi: 0, // Şu an cevaplanmakta olan sorunun sırasını belirtir
            bitirKapali: true, // Yarışmanın bitip bitmediğini kontrol eder, yarışma bitince bu değer false olur
            yuzdeDogruCevap: null, // Yarışmadaki doğru cevap yüzdesini tutar (kullanıcının performansını gösterir)
            skorKaydedildi: false, // Skorun kaydedilip kaydedilmediği bilgisi (tekrar kaydedilmesini önlemek için kullanılır)
            yarismaBasladi: false, // Yarışmanın başlayıp başlamadığını belirtir (başla butonu ile tetiklenir)
            scores: [], // Daha önceki kullanıcı skorlarını veya diğer yarışmacıların skorlarını tutar
        }
    },
    computed: {
        bittiMi() { // Yarışmanın bitip bitmediğini kontrol eder (tüm soruların cevaplanıp cevaplanmadığına bakar)
            return this.sorular && !this.sorular.every(soru => soru.hasOwnProperty('secilen_cevap')); // Eğer her soru cevaplandıysa, yarışma bitmiş demektir
        },
        imgStyle() { // Fotoğraf için stil ayarlarını belirler (görünümde kullanılacak stil)
            return {
                width: '100px', // Fotoğraf genişliği 100 piksel olarak ayarlanır
                height: '100px', // Fotoğraf yüksekliği 100 piksel olarak ayarlanır
                objectFit: 'cover' // Fotoğrafın belirli bir alana sığdırılması sağlanır, taşan kısımlar kırpılır
            };
        }
    },
    methods: {
        onFileChange(event) { // Fotoğraf dosyası seçildiğinde tetiklenir (kullanıcının seçtiği dosyayı alır)
            this.photo = event.target.files[0]; // Dosya inputundan seçilen ilk dosyayı alır ve photo değişkenine atar
        },
        yenidenOyna() { // Yarışmayı yeniden başlatmak için gerekli değişkenleri sıfırlar
            this.yarismaBasladi = false; // Yarışmanın başlamadığını belirtir
            this.skorKaydedildi = false; // Skor kaydedilmemiş olarak ayarlanır
            this.name = null; // Kullanıcının adı sıfırlanır (yeniden girilmesi gerekebilir)
        },
        start() { // Yarışmayı başlatır ve gerekli başlangıç ayarlarını yapar
            this.secilenSoruSirasi = 0; // İlk sorudan başlamak üzere sıfırlanır
            axios.get('/api/sorular').then(res => { // Soruları API'den alır ve yanıt başarılı olduğunda işlemleri gerçekleştirir
                this.sorular = res.data; // API'den alınan sorular sorular değişkenine atanır
                this.secilenSoru = this.sorular[0]; // İlk soru seçili olarak ayarlanır
                this.yarismaBasladi = true; // Yarışmanın başladığını belirtir
                window.addEventListener('beforeunload', this.handleBeforeUnload); // Sayfa kapanmadan önce kullanıcıya uyarı verir
            });
        },
        oncekiSoru() { // Önceki soruya geçmek için kullanılır
            this.secilenSoruSirasi--; // Soru sırası bir azaltılır
            this.secilenSoru = this.sorular[this.secilenSoruSirasi]; // Yeni seçilen soru secilenSoru değişkenine atanır
        },
        sonrakiSoru() { // Sonraki soruya geçmek için kullanılır
            this.secilenSoruSirasi++; // Soru sırası bir artırılır
            this.secilenSoru = this.sorular[this.secilenSoruSirasi]; // Yeni seçilen soru secilenSoru değişkenine atanır
        },
        sec(secenek) { // Kullanıcının bir cevabı seçmesini sağlar
            if (!this.skorKaydedildi) { // Eğer skor kaydedilmediyse (yarışma hala devam ediyorsa)
                this.sorular[this.secilenSoruSirasi].secilen_cevap = secenek; // Şu anki sorunun seçilen cevabını belirler
            }
        },
        bitir() { // Yarışmayı bitirir ve kullanıcının skorunu kaydeder
            const soruSayisi = this.sorular.length; // Toplam soru sayısını alır
            const dogruCevapSayisi = this.sorular.filter(soru => soru.dogru_cevap === soru.secilen_cevap).length; // Doğru cevapların sayısını hesaplar
            this.yuzdeDogruCevap = Math.ceil(dogruCevapSayisi / soruSayisi * 100); // Doğru cevap yüzdesini hesaplar ve en yakın tam sayıya yuvarlar

            const formData = new FormData(); // Form verilerini göndermek için yeni bir FormData nesnesi oluşturur
            formData.append('photo', this.photo); // Fotoğrafı form verilerine ekler
            formData.append('name', this.name); // Kullanıcının ismini form verilerine ekler
            formData.append('score', this.yuzdeDogruCevap); // Kullanıcının skorunu form verilerine ekler

            axios.post('/api/scores', formData).then(() => { // Skoru API'ye gönderir ve işlem başarılı olduğunda diğer işlemleri yapar
                this.skorKaydedildi = true; // Skorun kaydedildiğini belirtir
                axios.get('/api/scores').then(res => { // Güncel skorları API'den tekrar alır
                    this.scores = res.data; // Alınan skorlar scores değişkenine atanır
                });
                window.removeEventListener('beforeunload', this.handleBeforeUnload); // Sayfa kapanmadan önceki uyarıyı kaldırır
            });
        },
        updateClock() { // Saat bilgisini günceller
            const clockElement = document.getElementById("clock"); // Saat bilgisinin gösterileceği DOM elemanını alır
            setInterval(() => { // Her saniye bu fonksiyonu tekrar eder
                const now = new Date(); // Şu anki tarihi ve saati alır
                const timeString = now.toLocaleTimeString('tr-TR', {hour12: false}); // Saat bilgisini Türkiye formatında ve 24 saatlik dilimde alır
                clockElement.innerText = `Saat: ${timeString}`; // Saat bilgisini DOM elemanına yazar
            }, 1000); // 1000 milisaniye (1 saniye) aralıklarla çalışır
        },
        updateWeather() { // Hava durumu bilgisini günceller
            const weatherElement = document.getElementById("weather"); // Hava durumu bilgisinin gösterileceği DOM elemanını alır
            const city = "Ankara"; // Şehir bilgisi, hava durumu sorgusu için kullanılır
            axios.get('https://api.open-meteo.com/v1/forecast?latitude=39.9334&longitude=32.8597&hourly=temperature_2m').then(res => { // Hava durumu API çağrısı yapar
                const temperature = res.data.hourly.temperature_2m[0]; // Alınan veriden sıcaklık bilgisini alır (ilk saatin sıcaklığı)
                weatherElement.innerHTML = `<span class='city'>${city}</span> - <span class='temperature'>${temperature}°C</span>`; // Şehir ve sıcaklık bilgilerini DOM elemanına ekler
            }).catch(() => {
                weatherElement.innerText = "Hava Durumu: Bilgi alınamadı"; // Hata durumunda kullanıcıya gösterilecek mesaj
            });
        },
        updateDate() { // Tarih bilgisini günceller
            const dateElement = document.getElementById("date"); // Tarih bilgisinin gösterileceği DOM elemanını alır
            const now = new Date(); // Şu anki tarihi alır
            const dateString = now.toLocaleDateString('tr-TR', { // Tarih bilgisini Türkiye formatında alır
                weekday: 'long', // Haftanın günü (ör. Pazartesi)
                year: 'numeric', // Yıl bilgisi (ör. 2024)
                month: 'long', // Ay bilgisi (ör. Ekim)
                day: 'numeric' // Gün bilgisi (ör. 15)
            });
            dateElement.innerHTML = `<span class='date-text'>${dateString}</span>`; // Alınan tarih bilgisini DOM elemanına ekler
        },
        handleBeforeUnload(event) { // Sayfa kapanmadan önce uyarı gösterir
            event.preventDefault(); // Tarayıcıların sayfa kapanış uyarısı vermesini sağlar
            event.returnValue = ''; // Uyarı mesajını belirler (boş bırakılabilir, tarayıcı varsayılan mesajını kullanır)
        },
        confirmNavigation() { // Sayfa kapanışında kullanıcıdan onay alır
            window.addEventListener('beforeunload', (event) => { // Sayfa kapanmadan önce 'beforeunload' olayını dinler
                event.preventDefault(); // Tarayıcıların sayfa kapanış uyarısı vermesini sağlar
                event.returnValue = ''; // Uyarı mesajını belirler (boş bırakılabilir)
            });
        }
    },
    mounted() { // Bileşen yüklendiğinde çalışır
        axios.get('/api/scores').then(res => { // Skorları API'den alır ve başarılı bir yanıt döndüğünde işlemleri yapar
            this.scores = res.data; // API'den alınan skorları scores değişkenine atar
        });
        this.updateClock(); // Saati günceller (ekranda güncel saat gösterilir)
        this.updateWeather(); // Hava durumunu günceller (ekranda güncel hava durumu gösterilir)
        this.updateDate(); // Tarih bilgisini günceller (ekranda güncel tarih gösterilir)
        window.addEventListener('beforeunload', this.handleBeforeUnload); // Sayfa kapanmadan önce uyarı ekler (kullanıcıya sayfadan ayrılmadan önce onay alır)
    },
    beforeDestroy() { // Bileşen yok edilmeden önce çalışır
        window.removeEventListener('beforeunload', this.handleBeforeUnload); // Sayfa kapanmadan önceki uyarıyı kaldırır (bileşen yok edilirken gereksiz dinleyiciler temizlenir)
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
