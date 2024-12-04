<template>
    <div class="container">
        <h2>Fotoğraf ve Video Yükle</h2>

        <!-- İstatistiksel Veriler -->
        <div v-if="faces.length" class="stats-section">
            <p>Tespit edilen yüz sayısı: {{ faces.length }}</p>
        </div>

        <div
            class="upload-section"
            @dragover.prevent
            @dragenter.prevent
            @drop.prevent="onDrop"
        >
            <label class="file-upload">
                <i class="fas fa-upload"></i>
                <span>Dosya Seç veya Sürükle ve Bırak</span>
                <input
                    type="file"
                    @change="onFileChange"
                    multiple
                    accept="image/*,video/*"
                />
            </label>

            <div v-if="selectedFiles.length" class="preview-section">
                <h3>Seçilen Dosyalar:</h3>
                <div class="preview-grid">
                    <div
                        v-for="(file, index) in selectedFiles"
                        :key="index"
                        class="preview-card"
                    >
                        <p>{{ file.name }}</p>
                        <img
                            v-if="file.type.startsWith('image/')"
                            :src="file.preview"
                            alt="Önizleme"
                        />
                        <video
                            v-else-if="file.type.startsWith('video/')"
                            :src="file.preview"
                            controls
                        ></video>
                        <button
                            @click="removeFile(index)"
                            class="remove-button"
                            aria-label="Dosyayı Kaldır"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button
                @click="uploadFiles"
                :disabled="loading || !selectedFiles.length"
                class="upload-button"
            >
                <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                <span v-else>Yükle ve Analiz Et</span>
            </button>
        </div>

        <div v-if="progress > 0 && progress < 100" class="progress-bar">
            <div class="progress" :style="{ width: progress + '%' }"></div>
        </div>

        <div v-if="faces.length" class="faces-section">
            <h3>Tespit Edilen Yüzler:</h3>
            <div class="faces-grid">
                <div v-for="(face, index) in faces" :key="index" class="face-card">
                    <p>Şahıs {{ index + 1 }}</p>
                    <img :src="face.image" alt="Yüz resmi" @click="openModal(face.image)" />
                </div>
            </div>
        </div>

        <div v-else-if="loading" class="loading-section">
            <p>Yükleniyor, lütfen bekleyin...</p>
        </div>

        <div v-else class="no-analysis">
            <p>Henüz bir analiz yapılmadı.</p>
        </div>

        <!-- Büyütülmüş Görsel Modalı -->
        <div v-if="showModal" class="modal" @click.self="closeModal">
            <div class="modal-content">
                <img :src="modalImage" alt="Büyük Yüz Resmi" />
                <button class="close-modal" @click="closeModal">Kapat</button>
            </div>
        </div>

        <!-- Yeni Özellikler -->
        <!-- Özellik 1: Dosya Türüne Göre Filtreleme -->
        <div class="filter-section">
            <label>
                Dosya Türü Filtrele:
                <select v-model="fileTypeFilter" @change="filterFiles">
                    <option value="all">Tümü</option>
                    <option value="image">Resimler</option>
                    <option value="video">Videolar</option>
                </select>
            </label>
        </div>

        <!-- Özellik 2: Seçilen Dosyaları Sıralama -->
        <div class="sort-section">
            <label>
                Dosyaları Sırala:
                <select v-model="sortOrder" @change="sortFiles">
                    <option value="name">İsme Göre</option>
                    <option value="size">Boyuta Göre</option>
                </select>
            </label>
        </div>

        <!-- Özellik 3: Yükleme Durumu Geri Bildirimi -->
        <div v-if="loadingMessage" class="loading-message">
            <p>{{ loadingMessage }}</p>
        </div>

        <!-- Özellik 4: Yüzlerin Tam Ekran Görüntülenmesi -->
        <div v-if="showFullScreen" class="fullscreen-modal" @click.self="closeFullScreen">
            <img :src="fullscreenImage" alt="Tam Ekran Görüntü" />
            <button @click="closeFullScreen" class="close-fullscreen">Kapat</button>
        </div>

        <!-- Özellik 5: Dosyaları Toplu Silme -->
        <button @click="removeAllFiles" class="remove-all-button" :disabled="!selectedFiles.length">
            Tüm Dosyaları Kaldır
        </button>

        <!-- Özellik 6: Dosyaların Toplam Boyutu Gösterimi -->
        <div v-if="totalFileSize > 0" class="total-size">
            <p>Toplam Dosya Boyutu: {{ formatSize(totalFileSize) }}</p>
        </div>

        <!-- Özellik 7: Tekrar Yükleme Seçeneği -->
        <div v-if="uploadError" class="retry-section">
            <p>Yükleme sırasında bir hata oluştu. Lütfen tekrar deneyin.</p>
            <button @click="retryUpload" class="retry-button">Tekrar Dene</button>
        </div>

        <!-- Özellik 8: Yüzlerin Ayrıntılı Bilgisi -->
        <div v-if="faces.length" class="faces-details">
            <div v-for="(face, index) in faces" :key="index" class="face-detail">
                <p>Şahıs {{ index + 1 }} Ayrıntılar: {{ face.details }}</p>
            </div>
        </div>

        <!-- Özellik 9: Yükleme Tamamlandığında Bildirim Gösterimi -->
        <div v-if="uploadCompleted" class="upload-complete">
            <p>Yükleme tamamlandı!</p>
        </div>

        <!-- Özellik 10: Yüzlere Etiket Ekleme -->
        <div v-if="faces.length" class="faces-labels">
            <div v-for="(face, index) in faces" :key="index" class="face-label">
                <label>
                    Şahıs {{ index + 1 }} Etiket Ekle:
                    <input type="text" v-model="face.label" @change="updateFaceLabel(index)" />
                </label>
            </div>
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
            progress: 0, // Yükleme ilerleme durumu
            showModal: false, // Modal gösterilsin mi
            modalImage: null, // Modalda gösterilecek resim
            fileTypeFilter: 'all', // Dosya türü filtresi
            sortOrder: 'name', // Sıralama düzeni
            loadingMessage: '', // Yükleme durumu mesajı
            showFullScreen: false, // Tam ekran gösterimi
            fullscreenImage: null, // Tam ekran resim
            totalFileSize: 0, // Dosyaların toplam boyutu
            uploadError: false, // Yükleme hatası durumu
            uploadCompleted: false, // Yüklemenin tamamlanma durumu
        };
    },
    methods: {
        onFileChange(event) {
            const files = Array.from(event.target.files);
            this.handleFiles(files);
        },
        onDrop(event) {
            const files = Array.from(event.dataTransfer.files);
            this.handleFiles(files);
        },
        handleFiles(files) {
            files.forEach((file) => {
                if (this.validateFile(file)) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        file.preview = e.target.result;
                        this.selectedFiles.push(file);
                        this.calculateTotalSize();
                    };
                    if (file.type.startsWith("image/")) {
                        reader.readAsDataURL(file);
                    } else if (file.type.startsWith("video/")) {
                        reader.readAsDataURL(file);
                    }
                }
            });
        },
        validateFile(file) {
            const maxSize = 50 * 1024 * 1024; // 50 MB
            if (file.size > maxSize) {
                alert(`'${file.name}' dosyası çok büyük (50 MB sınırı).`);
                return false;
            }
            const validTypes = ["image/", "video/"];
            if (!validTypes.some((type) => file.type.startsWith(type))) {
                alert(`'${file.name}' dosya tipi desteklenmiyor.`);
                return false;
            }
            return true;
        },
        removeFile(index) {
            this.selectedFiles.splice(index, 1);
            this.calculateTotalSize();
        },
        removeAllFiles() {
            this.selectedFiles = [];
            this.calculateTotalSize();
        },
        async uploadFiles() {
            if (!this.selectedFiles.length) {
                alert("Lütfen önce bir dosya seçin.");
                return;
            }

            this.loading = true; // Yükleme durumunu başlat
            this.loadingMessage = 'Yükleme işlemi devam ediyor...';
            this.progress = 0;
            const formData = new FormData();
            this.selectedFiles.forEach((file, index) => {
                formData.append(`files[${index}]`, file);
            });

            try {
                const response = await axios.post("/api/upload", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    onUploadProgress: (progressEvent) => {
                        this.progress = parseInt(
                            Math.round((progressEvent.loaded * 100) / progressEvent.total)
                        );
                    },
                });

                console.log("Sunucudan gelen yanıt:", response.data);

                if (response.data.faces && response.data.faces.length) {
                    this.faces = response.data.faces; // Yüz bilgilerini ata
                } else {
                    alert("Yüz bilgisi bulunamadı.");
                    this.faces = [];
                }
                this.uploadCompleted = true;
            } catch (error) {
                console.error("Yükleme hatası:", error);
                if (error.response && error.response.data) {
                    console.error("Sunucudan dönen hata:", error.response.data);
                }
                alert("Yükleme sırasında bir hata oluştu. Lütfen tekrar deneyin.");
                this.faces = []; // Hata durumunda yüz listesini temizle
                this.uploadError = true;
            } finally {
                this.loading = false; // Yükleme durumu sona erdi
                this.loadingMessage = '';
                this.progress = 0;
            }
        },
        retryUpload() {
            this.uploadError = false;
            this.uploadFiles();
        },
        openModal(imageSrc) {
            this.modalImage = imageSrc;
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.modalImage = null;
        },
        openFullScreen(imageSrc) {
            this.fullscreenImage = imageSrc;
            this.showFullScreen = true;
        },
        closeFullScreen() {
            this.showFullScreen = false;
            this.fullscreenImage = null;
        },
        calculateTotalSize() {
            this.totalFileSize = this.selectedFiles.reduce((total, file) => total + file.size, 0);
        },
        formatSize(size) {
            if (size < 1024) return size + ' B';
            else if (size < 1024 * 1024) return (size / 1024).toFixed(2) + ' KB';
            else return (size / (1024 * 1024)).toFixed(2) + ' MB';
        },
        filterFiles() {
            // Filtreleme mantığı burada uygulanacak
            console.log("Dosyalar filtrelendi: " + this.fileTypeFilter);
        },
        sortFiles() {
            if (this.sortOrder === 'name') {
                this.selectedFiles.sort((a, b) => a.name.localeCompare(b.name));
            } else if (this.sortOrder === 'size') {
                this.selectedFiles.sort((a, b) => a.size - b.size);
            }
        },
        updateFaceLabel(index) {
            console.log(`Şahıs ${index + 1} etiketi güncellendi: ${this.faces[index].label}`);
        },
    },
};
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

.container {
    max-width: 800px;
    margin: 20px auto;
    padding: 15px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 10px;
    color: #333;
}

.stats-section {
    text-align: center;
    margin-bottom: 10px;
    color: #555;
}

.upload-section {
    border: 2px dashed #4CAF50;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    transition: background-color 0.3s, border-color 0.3s;
    position: relative;
    background-color: #e8f5e9;
}

.upload-section:hover {
    background-color: #c8e6c9;
    border-color: #388e3c;
}

.file-upload {
    display: inline-block;
    cursor: pointer;
    font-size: 16px;
    color: #388e3c;
}

.file-upload input {
    display: none;
}

.preview-section {
    margin-top: 20px;
}

.preview-section h3 {
    margin-bottom: 15px;
    color: #555;
}

.preview-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
}

.preview-card {
    position: relative;
    background-color: #fafafa;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    width: 150px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.preview-card p {
    margin-bottom: 10px;
    font-weight: bold;
    color: #555;
    font-size: 14px;
}

.preview-card img,
.preview-card video {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
}

.remove-button {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: transparent;
    border: none;
    color: #888;
    cursor: pointer;
    font-size: 16px;
}

.remove-button:hover {
    color: #f44336;
}

.upload-button {
    margin-top: 20px;
    background-color: #4CAF50;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.3s;
}

.upload-button:disabled {
    background-color: #9E9E9E;
    cursor: not-allowed;
}

.upload-button:hover:not(:disabled) {
    background-color: #45a049;
}

.progress-bar {
    width: 100%;
    background-color: #f1f1f1;
    border-radius: 8px;
    overflow: hidden;
    margin-top: 20px;
}

.progress {
    height: 10px;
    background-color: #4CAF50;
    width: 0%;
    transition: width 0.4s ease;
}

.faces-section {
    margin-top: 30px;
}

.faces-section h3 {
    margin-bottom: 15px;
    color: #555;
}

.faces-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 15px;
}

.face-card {
    background-color: #fafafa;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.face-card p {
    margin-bottom: 10px;
    font-weight: bold;
    color: #555;
}

.face-card img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
    cursor: pointer;
}

.loading-section,
.no-analysis {
    text-align: center;
    color: #777;
    margin-top: 20px;
}

/* Modal Stilleri */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    position: relative;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
}

.modal-content img {
    max-width: 100%;
    height: auto;
}

.close-modal {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #f44336;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
}

.close-modal:hover {
    background-color: #e53935;
}

/* Yeni Özellikler İçin Stil */
.filter-section, .sort-section, .total-size, .retry-section, .faces-labels {
    margin-top: 20px;
    text-align: center;
}

.fullscreen-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.fullscreen-modal img {
    max-width: 90%;
    height: auto;
}

.close-fullscreen {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #f44336;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
}

.close-fullscreen:hover {
    background-color: #e53935;
}

.remove-all-button {
    margin-top: 10px;
    background-color: #f44336;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.remove-all-button:disabled {
    background-color: #9E9E9E;
    cursor: not-allowed;
}

.remove-all-button:hover:not(:disabled) {
    background-color: #e53935;
}

.upload-complete {
    margin-top: 20px;
    text-align: center;
    color: #4CAF50;
    font-weight: bold;
}

.faces-label input {
    margin-top: 5px;
    padding: 5px;
    border-radius: 4px;
    border: 1px solid #ddd;
    width: 80%;
}

/* Responsive Tasarım */
@media (max-width: 600px) {
    .upload-section {
        padding: 10px;
    }

    .preview-card {
        width: 120px;
    }

    .upload-button {
        width: 100%;
        padding: 12px;
        justify-content: center;
    }

    .faces-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }

    /* Yatay menüyle çakışmayı önlemek için üst boşluk ekleyin */
    .faces-section {
        margin-top: 80px;
    }
}
</style>
