<template>
    <h1 style="text-align: center">YOUTUBE APİ DENEME</h1>
    <div class="row justify-content-center align-items-start" style="margin-top: 0;">
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body text-center p-4">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Aramak istediğiniz videoyu yazın..." v-model="searchQuery">
                        <div class="input-group-append">
                            <button class="btn btn-primary" @click="fetchYoutubeVideos">Ara</button>
                        </div>
                    </div>
                    <div>
                        <div v-for="video in youtubeVideos" :key="video.id" class="mb-3">
                            <h6>{{ video.title }}</h6>
                            <iframe v-if="video.id" :src="`https://www.youtube.com/embed/${video.id}`" frameborder="0" allowfullscreen class="w-100" style="height: 200px;"></iframe>
                        </div>
                    </div>
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
            searchQuery: '',
            youtubeVideos: []
        }
    },
    methods: {
        fetchYoutubeVideos(initial = false) {
            // YouTube API'den veri çekme
            const API_KEY = 'AIzaSyCHw4XdL7tgPN2LVm2XTLZJUprNmvczPtI';
            const maxResults = 5;
            const query = initial ? 'SQL İLE SORGU EĞİTİMİ' : this.searchQuery;
            const url = `https://www.googleapis.com/youtube/v3/search?key=${API_KEY}&q=${query}&part=snippet&type=video&maxResults=${maxResults}&relevanceLanguage=tr&regionCode=TR`;

            axios.get(url)
                .then(response => {
                    this.youtubeVideos = response.data.items
                        .filter(item => item.id.kind === 'youtube#video')
                        .map(item => ({
                            id: item.id.videoId,
                            title: item.snippet.title.replace(/\&\#39;/g, "'").replace(/\&quot;/g, '"').replace(/\&amp;/g, '&')
                        }));
                })
                .catch(error => {
                    console.error('YouTube videoları yüklenirken bir hata oluştu:', error);
                });
        }
    },
    mounted() {
        this.fetchYoutubeVideos(true);
    }
}
</script>

<style scoped>
/* Sayfa için şık ve mobil uyumlu stil */
.card {
    background-color: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 0;
}

.btn {
    font-size: 1rem;
    padding: 0.75rem 1rem;
}

/* Mobil görünüm için optimize edilmiş stil */
@media (max-width: 576px) {
    .card {
        margin: 0 10px;
    }

    .btn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
}
</style>
