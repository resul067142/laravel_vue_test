<template>
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div v-if="!yarismaBasladi" class="row">
                        <div class="col-12 text-center">
                            <form @submit.prevent="start">
                                <div class="form-group">
                                    <label class="m-2" for="">Fotoğraf Ekle</label>
                                    <input class="m-2" @change="onFileChange" type="file" required>
                                </div>
                                <input type="text" v-model="name" class="form-control" placeholder="Ad" required>
                                <button type="submit" class="btn btn-primary mt-1">Yarışmaya Başla</button>
                            </form>
                        </div>
                    </div>
                    <div v-if="yarismaBasladi" class="row">
                        <div class="col-12 m-2 text-center">
                            Hoşgeldin <strong>{{name}}</strong>
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

                            <div @click="sec(1)"
                                 :class="'secenek' +  (secilenSoru.secilen_cevap && secilenSoru.secilen_cevap === 1 ? ' secilen' : '')">
                                1 - {{secilenSoru.secenek_1}}
                            </div>
                            <div @click="sec(2)"
                                 :class="'secenek' +  (secilenSoru.secilen_cevap && secilenSoru.secilen_cevap === 2 ? ' secilen' : '')">
                                2 - {{secilenSoru.secenek_2}}
                            </div>
                            <div @click="sec(3)"
                                 :class="'secenek' +  (secilenSoru.secilen_cevap && secilenSoru.secilen_cevap === 3 ? ' secilen' : '')">
                                3 - {{secilenSoru.secenek_3}}
                            </div>
                            <div @click="sec(4)"
                                 :class="'secenek' +  (secilenSoru.secilen_cevap && secilenSoru.secilen_cevap === 4 ? ' secilen' : '')">
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
                            <th>Fotoğraf</th>
                            <th>Ad</th>
                            <th>Puan</th>
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
            });
        }
    },
    mounted() {
        axios.get('/api/scores').then(res => {
            this.scores = res.data;
        });
    }
};
</script>
