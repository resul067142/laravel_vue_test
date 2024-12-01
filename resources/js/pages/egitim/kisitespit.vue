<template>
    <div>
        <h2>Fotoğraf ve Video Yükle</h2>
        <input
            type="file"
            @change="onFileChange"
            multiple
            accept="image/*,video/*"
        />
        <button @click="uploadFiles" :disabled="loading">
            {{ loading ? "Yükleniyor..." : "Yükle ve Analiz Et" }}
        </button>

        <div v-if="faces.length">
            <h3>Tespit Edilen Yüzler:</h3>
            <ul>
                <li v-for="(face, index) in faces" :key="index">
                    Şahıs {{ index + 1 }}
                    <img :src="face.image" alt="Yüz resmi" />
                </li>
            </ul>
        </div>
        <div v-else-if="loading">
            <p>Yükleniyor, lütfen bekleyin...</p>
        </div>
        <div v-else>
            <p>Henüz bir analiz yapılmadı.</p>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            selectedFiles: [], // Yüklemek için seçilen dosyalar
            faces: [], // Tespit edilen yüzlerin bilgileri
            loading: false, // Yükleme durumu
        };
    },
    methods: {
        onFileChange(event) {
            // Dosyaları seç ve kaydet
            this.selectedFiles = Array.from(event.target.files);
            if (!this.selectedFiles.length) {
                alert("Lütfen dosya seçin.");
            }
        },
        async uploadFiles() {
            if (!this.selectedFiles.length) {
                alert("Lütfen önce bir dosya seçin.");
                return;
            }

            this.loading = true; // Yükleme durumunu başlat
            const formData = new FormData();
            this.selectedFiles.forEach((file, index) => {
                formData.append(`files[${index}]`, file);
            });

            try {
                const response = await axios.post("/api/upload", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });

                console.log("Sunucudan gelen yanıt:", response.data);

                if (response.data.faces && response.data.faces.length) {
                    this.faces = response.data.faces; // Yüz bilgilerini ata
                } else {
                    alert("Yüz bilgisi bulunamadı.");
                    this.faces = [];
                }
            } catch (error) {
                console.error("Yükleme hatası:", error);
                if (error.response && error.response.data) {
                    console.error("Sunucudan dönen hata:", error.response.data);
                }
                alert("Yükleme sırasında bir hata oluştu. Lütfen tekrar deneyin.");
                this.faces = []; // Hata durumunda yüz listesini temizle
            } finally {
                this.loading = false; // Yükleme durumu sona erdi
            }
        },
    },
};
</script>
