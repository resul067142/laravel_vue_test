<template>
    <div id="app">
        <div class="upload-container">
            <h2>Dosya Kütüphanesi</h2>
            <div class="upload-section">
                <input type="file" @change="handleFileUpload" multiple placeholder="Dosya seçin..." />
                <input type="file" @change="handleImageUpload" accept="image/*" placeholder="Tanıtım görseli seçin..." />
                <select v-model="selectedCategory" class="category-select" placeholder="Kategori Seçin...">
                    <option disabled value="">Kategori Seçin</option>
                    <option value="excel">Excel</option>
                    <option value="word">Word</option>
                    <option value="powerpoint">PowerPoint</option>
                </select>
                <button @click="uploadFiles" class="upload-button">Dosya Yükle</button>
            </div>
        </div>

        <div v-if="!serverFiles.length" class="no-files-message">
            Henüz yüklenmiş bir dosya yok.
        </div>
        <div v-else class="file-library">
            <h3>Yüklü Dosyalar:</h3>
            <div class="filter-section split-filters">
                <div class="filter-half">
                    <label for="category-filter">Kategori Filtrele:</label>
                    <select v-model="filterCategory" id="category-filter" class="filter-select">
                        <option value="">Tümü</option>
                        <option v-for="category in categories" :key="category" :value="category">{{ category.toUpperCase() }}</option>
                    </select>
                </div>
                <div class="filter-half">
                    <label for="column-filter">Sütun Seç:</label>
                    <select id="column-filter" class="filter-select">
                        <option disabled selected>Kategorileri Seç</option>
                        <option v-for="column in columns" :key="column.key" :value="column.key">{{ column.label }}</option>
                    </select>

                </div>
            </div>
            <div class="table-responsive">
                <table class="file-table">
                    <thead>
                    <tr>
                        <th v-if="selectedColumns.includes('name')" @click="sortColumn('name', 'Dosya adı sıralandı')">Dosya Adı</th>
                        <th v-if="selectedColumns.includes('category')" @click="sortColumn('category', 'Kategori sıralandı')">Kategori</th>
                        <th v-if="selectedColumns.includes('uploader')" class="optional-column" @click="sortColumn('uploader', 'Yükleyici adı sıralandı')">Yükleyen</th>
                        <th v-if="selectedColumns.includes('uploaded_at')" class="optional-column" @click="sortColumn('uploaded_at', 'Yükleme tarihi sıralandı')">Yükleme Tarihi</th>
                        <th>Görsel</th>
                        <th>İşlemler</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(file, index) in sortedFiles" :key="index" class="file-row">
                        <td v-if="selectedColumns.includes('name')">{{ file.name }}</td>
                        <td v-if="selectedColumns.includes('category')">{{ file.category.toUpperCase() }}</td>
                        <td v-if="selectedColumns.includes('uploader')" class="optional-column">{{ file.uploader }}</td>
                        <td v-if="selectedColumns.includes('uploaded_at')" class="optional-column">{{ formatDate(file.uploaded_at) }}</td>
                        <td v-if="file.image_path">
                            <img :src="`/storage/${file.image_path}`" alt="Görsel Önizlemesi" class="file-preview" />
                        </td>
                        <td v-else>Görsel yok</td>
                        <td><button @click="downloadFile(file)" class="download-button">İndir</button></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="mobile-navigation" v-if="isMobile">
                <button @click="showPreviousColumns" :disabled="currentColumnIndex === 0">Önceki</button>
                <button @click="showNextColumns" :disabled="currentColumnIndex >= maxColumnIndex">Sonraki</button>
            </div>
        </div>

        <transition name="fade">
            <div v-if="showNotification" class="notification-box center">
                {{ notificationMessage }}
            </div>
        </transition>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            filesToUpload: [],  // Yüklenecek dosyalar
            serverFiles: [],    // Sunucudan gelen dosyalar
            selectedCategory: '',  // Seçilen kategori
            categories: ['excel', 'word', 'powerpoint'],  // Kategoriler listesi
            uploaderName: 'Kullanıcı Adı', // Yükleyen kişinin adı
            selectedImage: null, // Seçilen görsel
            filterCategory: '', // Filtrelenen kategori
            sortKey: '', // Sıralama anahtarı
            sortAsc: true, // Sıralama yönü (artan/azalan)
            showNotification: false, // Bildirim kutusu görünürlüğü
            notificationMessage: '', // Bildirim mesajı
            currentColumnIndex: 0, // Mobilde görüntülenen sütunların başlangıç indeksi
            columnsPerPage: 2, // Mobilde aynı anda gösterilecek sütun sayısı
            selectedColumns: ['name', 'category'], // Görüntülenecek sütunlar, varsayılan olarak en az iki sütun seçili
        };
    },
    computed: {
        filteredFiles() {
            if (!this.filterCategory) {
                return this.serverFiles;
            }
            return this.serverFiles.filter(file => file.category === this.filterCategory);
        },
        sortedFiles() {
            return this.filteredFiles.sort((a, b) => {
                let result = 0;
                if (a[this.sortKey] > b[this.sortKey]) {
                    result = 1;
                } else if (a[this.sortKey] < b[this.sortKey]) {
                    result = -1;
                }
                return this.sortAsc ? result : -result;
            });
        },
        isMobile() {
            return window.innerWidth <= 768;
        },
        maxColumnIndex() {
            return Math.max(0, Math.ceil((this.columns.length - this.columnsPerPage) / this.columnsPerPage));
        },
        columns() {
            return [
                { key: 'name', label: 'Dosya Adı' },
                { key: 'category', label: 'Kategori' },
                { key: 'uploader', label: 'Yükleyen' },
                { key: 'uploaded_at', label: 'Yükleme Tarihi' },
            ];
        },
        visibleColumns() {
            return this.columns.slice(this.currentColumnIndex, this.currentColumnIndex + this.columnsPerPage);
        }
    },
    created() {
        // Uygulama yüklendiğinde mevcut dosyaları al
        this.fetchFiles();
    },
    methods: {
        handleFileUpload(event) {
            const uploadedFiles = event.target.files;

            // Eğer dosya yoksa uyarı veriyoruz
            if (uploadedFiles.length === 0) {
                alert("Lütfen yüklemek için bir dosya seçin!");
                return;
            }

            // Eğer kategori seçilmediyse uyarı veriyoruz
            if (!this.selectedCategory) {
                alert("Lütfen bir kategori seçin!");
                return;
            }

            // Dosyaları bileşen veri yapısına ekleme
            this.filesToUpload = []; // Eski dosyaları temizliyoruz
            for (let i = 0; i < uploadedFiles.length; i++) {
                console.log("Yüklenmiş Dosya:", uploadedFiles[i]);
                this.filesToUpload.push({
                    name: uploadedFiles[i].name,
                    file: uploadedFiles[i], // Dosyanın kendisi
                    category: this.selectedCategory,
                    uploader: this.uploaderName, // Yükleyici adı
                    uploaded_at: new Date().toISOString(), // Yükleme tarihini ISO formatında ekliyoruz
                    imagePreview: this.selectedImage // Görsel önizlemesi
                });
            }

            console.log("Seçilen dosyalar:", this.filesToUpload);
        },

        handleImageUpload(event) {
            const imageFile = event.target.files[0];
            if (imageFile) {
                console.log("Yüklen en Görsel:", imageFile);
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.selectedImage = e.target.result; // Görsel önizlemesi (base64)
                };
                reader.readAsDataURL(imageFile);
            }
        },

        // Dosyaları sunucuya yükleme işlemi
        async uploadFiles() {
            if (this.filesToUpload.length === 0) {
                alert("Yüklenecek dosya bulunamadı. Lütfen dosya seçin!");
                return;
            }

            try {
                for (let file of this.filesToUpload) {
                    const formData = new FormData();
                    formData.append('file', file.file);
                    formData.append('name', file.name);
                    formData.append('category', file.category);
                    formData.append('uploader', file.uploader);
                    formData.append('uploaded_at', file.uploaded_at); // 'uploaded_at' alanını ekliyoruz

                    if (file.imagePreview) {
                        // Görseli Blob olarak ekleme işlemi
                        const blob = this.dataURLtoBlob(file.imagePreview);
                        formData.append('image', blob, 'preview.png');
                    }

                    console.log("Gönderilen dosya:", formData.get('file'));
                    console.log("Gönderilen görsel:", formData.get('image'));

                    try {
                        const response = await axios.post('/api/files', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            },
                        });

                        console.log('Dosya başarıyla yüklendi:', response.data);
                    } catch (error) {
                        console.error("Dosya yüklenirken hata oluştu:", error);
                        if (error.response) {
                            console.error("Hata ayrıntıları:", error.response.data);
                        }
                        throw new Error("Dosya yüklenirken bir hata oluştu.");
                    }
                }

                alert("Dosyalar başarıyla yüklendi!");
                this.filesToUpload = [];
                this.fetchFiles(); // Dosyaları yeniden yükle
            } catch (error) {
                alert(error.message);
            }
        },

        // DataURL'yi Blob'a dönüştürme
        dataURLtoBlob(dataURL) {
            const byteString = atob(dataURL.split(',')[1]);
            const mimeString = dataURL.split(',')[0].split(':')[1].split(';')[0];
            const ab = new ArrayBuffer(byteString.length);
            const ia = new Uint8Array(ab);
            for (let i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }
            return new Blob([ab], { type: mimeString });
        },

        // Mevcut dosyaları sunucudan almak için
        async fetchFiles() {
            try {
                const response = await axios.get('/api/files');
                this.serverFiles = response.data;
                console.log('Alınan dosyalar:', this.serverFiles);
            } catch (error) {
                console.error('Dosyalar alınırken hata oluştu:', error);
                alert('Dosyalar alınırken bir hata oluştu. Lütfen tekrar deneyin.');
            }
        },

        // Dosya indirme fonksiyonu
        async downloadFile(file) {
            try {
                const response = await axios.get(`/api/files/${file.id}/download`, {
                    responseType: 'blob'
                });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(response.data);
                link.download = file.name || 'downloaded-file';
                link.click();
            } catch (error) {
                console.error('Dosya indirilirken hata oluştu:', error);
                alert('Dosya indirilirken bir hata oluştu. Lütfen tekrar deneyin.');
            }
        },

        // Tarih formatlama fonksiyonu
        formatDate(dateString) {
            if (!dateString) return '';
            const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            return new Date(dateString).toLocaleDateString('tr-TR', options);
        },

        // Sütun sıralama fonksiyonu
        sortColumn(key, message) {
            if (this.sortKey === key) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortKey = key;
                this.sortAsc = true;
            }
            this.showNotificationMessage(message);
        },

        // Bildirim mesajı gösterme fonksiyonu
        showNotificationMessage(message) {
            this.notificationMessage = message;
            this.showNotification = true;
            setTimeout(() => {
                this.showNotification = false;
            }, 3000);
        },

        // Mobilde önceki sütunları gösterme
        showPreviousColumns() {
            if (this.currentColumnIndex > 0) {
                this.currentColumnIndex -= this.columnsPerPage;
            }
        },

        // Mobilde sonraki sütunları gösterme
        showNextColumns() {
            if (this.currentColumnIndex < this.maxColumnIndex) {
                this.currentColumnIndex += this.columnsPerPage;
            }
        },

        // En az iki sütun seçili olmasını sağlama
        isColumnDisabled(key) {
            return this.selectedColumns.length <= 2 && this.selectedColumns.includes(key);
        }
    }
};
</script>

<style scoped>
#app {
    text-align: center;
    max-width: 100%;
    margin: 0 auto;
    padding: 20px;
    background: linear-gradient(to right, #ece9e6, #ffffff);
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.upload-container {
    margin-bottom: 20px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #f9f9f9;
    transition: all 0.3s ease;
}

.upload-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.upload-section {
    display: flex;
    gap: 10px;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
}

.upload-section input[type="file"], .category-select {
    width: 100%;
    max-width: 300px;
}

.category-select, .upload-button {
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
    transition: all 0.3s ease;
}

.upload-button {
    background-color: #4caf50;
    color: white;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.upload-button:hover {
    background-color: #388e3c;
    transform: translateY(-2px);
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
}

.no-files-message {
    margin-top: 20px;
    font-size: 18px;
    color: #777;
    font-style: italic;
}

.file-library {
    margin-top: 20px;
    animation: fadeIn 1s ease-in-out;
}

.filter-section {
    margin-bottom: 10px;
    display: flex;
    justify-content: center;
    gap: 10px;
    align-items: center;
}

.split-filters {
    display: flex;
    gap: 20px;
}

.filter-half {
    flex: 1;
}

.filter-select {
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
    transition: all 0.3s ease;
}

.table-responsive {
    overflow-x: auto;
}

.file-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    animation: slideIn 1s ease-in-out;
}

.file-table th, .file-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
    transition: background-color 0.3s ease;
    cursor: pointer;
}

.file-table th {
    background-color: #f2f2f2;
    color: #333;
}

.file-row:hover td {
    background-color: #f9f9f9;
}

.download-button {
    background-color: #0288d1;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.3s;
}

.download-button:hover {
    background-color: #0277bd;
    transform: translateY(-2px);
}

.file-preview {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.file-preview:hover {
    transform: scale(1.1);
}

.notification-box {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: rgba(51, 51, 51, 0.9);
    color: white;
    padding: 20px 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    font-size: 18px;
}

.optional-column {
    display: none;
}

@media (min-width: 768px) {
    .optional-column {
        display: table-cell;
    }
}

@keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

@keyframes slideIn {
    0% { transform: translateY(-20px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}

@media (max-width: 768px) {
    .upload-section {
        flex-direction: column;
    }
    .file-table th, .file-table td {
        font-size: 14px;
        padding: 5px;
    }
    .file-preview {
        width: 60px;
        height: 60px;
    }
    .upload-button, .download-button {
        padding: 6px 10px;
        font-size: 14px;
    }
    .notification-box {
        font-size: 16px;
        padding: 15px 20px;
    }
    .mobile-navigation {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }
    .mobile-navigation button {
        padding: 10px;
        font-size: 16px;
        border-radius: 5px;
        border: 1px solid #ccc;
        background-color: #4caf50;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .mobile-navigation button:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }
}
</style>
