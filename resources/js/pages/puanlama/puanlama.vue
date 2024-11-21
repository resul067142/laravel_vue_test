<template>
    <div class="min-h-screen bg-gray-100 flex flex-col items-center p-4">
        <!-- Fotoğraf Yükleme Formu -->
        <div class="w-full max-w-md bg-white rounded-lg shadow-md overflow-hidden mt-8 p-4">
            <h3 class="text-xl font-semibold text-center mb-4">Fotoğraf Ekle</h3>
            <form @submit.prevent="fotografYukle" class="space-y-4">
                <div>
                    <label for="ad" class="text-gray-700">Ad</label>
                    <input
                        type="text"
                        id="ad"
                        v-model="yeniFotograf.ad"
                        class="w-full p-2 border rounded mt-1"
                        required
                    />
                </div>
                <div>
                    <label for="dosya" class="text-gray-700">Fotoğraf Dosyası</label>
                    <input
                        type="file"
                        id="dosya"
                        @change="dosyaSecildi"
                        class="w-full p-2 border rounded mt-1"
                        required
                    />
                </div>
                <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 transition">
                    Fotoğraf Yükle
                </button>
            </form>
        </div>

        <!-- Fotoğraf Slider -->
        <div class="w-full max-w-md bg-white rounded-lg shadow-md overflow-hidden mt-8">
            <div class="relative">
                <img :src="guncelFotograf.dosya_yolu || 'https://via.placeholder.com/150'" alt="Fotoğraf" class="w-full h-64 object-cover" />
                <button @click="oncekiFotograf" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2">
                    ←
                </button>
                <button @click="sonrakiFotograf" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2">
                    →
                </button>
            </div>
            <div class="p-4">
                <h3 class="text-xl font-semibold text-center mb-4">{{ guncelFotograf.ad }}</h3>
                <div class="space-y-4">
                    <div v-for="(kategori, index) in kategoriler" :key="index" class="flex items-center justify-between">
                        <label :for="kategori" class="text-gray-700">{{ kategori }}</label>
                        <input
                            type="number"
                            :id="kategori"
                            v-model.number="puanlar[kategori]"
                            min="0"
                            max="10"
                            class="w-16 p-1 border rounded"
                        />
                    </div>
                    <button @click="puanVer" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition">
                        Puan Ver
                    </button>
                </div>
                <div class="mt-4 text-center">
                    <h4 class="text-lg font-bold">Ortalama Puan: {{ ortalamaPuan }}</h4>
                </div>
            </div>
        </div>

        <!-- Fotoğraf Sıralaması -->
        <div class="w-full max-w-4xl mt-8">
            <h4 class="text-2xl font-semibold mb-4 text-center">Fotoğraf Sıralaması</h4>
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Ad</th>
                    <th class="py-3 px-6 text-center">Güzellik</th>
                    <th class="py-3 px-6 text-center">Yetenek</th>
                    <th class="py-3 px-6 text-center">Kişisel</th>
                    <th class="py-3 px-6 text-center">Ortalama</th>
                </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                <tr v-for="foto in fotograflar" :key="foto.id" class="border-b hover:bg-gray-50">
                    <td class="py-3 px-6 text-left">{{ foto.ad }}</td>
                    <td class="py-3 px-6 text-center">{{ foto.guzellik }}</td>
                    <td class="py-3 px-6 text-center">{{ foto.yetenek }}</td>
                    <td class="py-3 px-6 text-center">{{ foto.kisisel }}</td>
                    <td class="py-3 px-6 text-center font-semibold">{{ foto.ortalama_puan }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            fotograflar: [],
            guncelFotografIndex: 0,
            kategoriler: ['Güzellik', 'Yetenek', 'Kişisel'],
            puanlar: { Güzellik: 0, Yetenek: 0, Kişisel: 0 },
            yeniFotograf: {
                ad: '',
                dosya: null,
            },
        };
    },
    computed: {
        guncelFotograf() {
            return this.fotograflar[this.guncelFotografIndex] || {};
        },
        ortalamaPuan() {
            const toplam = Object.values(this.puanlar).reduce((a, b) => a + b, 0);
            return (toplam / 3).toFixed(2);
        },
    },
    methods: {
        sonrakiFotograf() {
            if (this.fotograflar.length > 0) {
                this.guncelFotografIndex = (this.guncelFotografIndex + 1) % this.fotograflar.length;
                this.puanlariSifirla();
            }
        },
        oncekiFotograf() {
            if (this.fotograflar.length > 0) {
                this.guncelFotografIndex =
                    (this.guncelFotografIndex - 1 + this.fotograflar.length) % this.fotograflar.length;
                this.puanlariSifirla();
            }
        },
        async puanVer() {
            const veri = {
                ad: this.guncelFotograf.ad,
                dosya_yolu: this.guncelFotograf.dosya_yolu,
                guzellik: this.puanlar.Güzellik,
                yetenek: this.puanlar.Yetenek,
                kisisel: this.puanlar.Kişisel,
            };

            try {
                await axios.post('/api/puanlafoto', veri);
                this.fotograflariGetir();
                this.puanlariSifirla();
            } catch (error) {
                console.error('Puanlama sırasında hata oluştu:', error);
            }
        },
        async fotograflariGetir() {
            try {
                const yanit = await axios.get('/api/puanlafoto');
                this.fotograflar = yanit.data;
            } catch (error) {
                console.error('Fotoğraflar getirilirken hata oluştu:', error);
            }
        },
        puanlariSifirla() {
            this.puanlar = { Güzellik: 0, Yetenek: 0, Kişisel: 0 };
        },
        dosyaSecildi(event) {
            this.yeniFotograf.dosya = event.target.files[0];
        },
        async fotografYukle() {
            const formData = new FormData();
            formData.append('ad', this.yeniFotograf.ad);
            formData.append('dosya', this.yeniFotograf.dosya);

            try {
                await axios.post('/api/puanlafoto/yukle', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });

                this.yeniFotograf = { ad: '', dosya: null };
                this.fotograflariGetir();
            } catch (error) {
                console.error('Fotoğraf yüklenirken hata oluştu:', error);
            }
        },
    },
    created() {
        this.fotograflariGetir();
    },
};
</script>

<style>
@import 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css';

img {
    object-fit: cover;
}

button:hover {
    filter: brightness(90%);
}

@media (max-width: 768px) {
    .min-h-screen {
        padding: 1rem;
    }

    table {
        font-size: 0.9rem;
    }
}
</style>
