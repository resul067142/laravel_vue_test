<script setup>
import {onMounted,ref} from "vue";

const isRankingVisible = ref(true);
const scores = ref([]);

const fetchScores = (range) => {
    let url = "/api/scores";
    if (range !== "all") {
        url = `/api/scores/${range}`;
    }
    axios.get(url)
        .then(response => {
            scores.value = response.data;
        })
        .catch(error => {
            scores.value = [];
            console.error("Veri alınırken bir hata oluştu:", error);
        });
}

onMounted(() => {
    axios.get("/api/scores")
        .then(response => {
            scores.value = response.data;
        })
        .catch(error => {
            scores.value = [];
            console.error("Veri alınırken bir hata oluştu:", error);
        });
})

</script>

<template>
    <div class="score-box">
        <div v-if="isRankingVisible" class="score-menu">
            <button class="score-link" @click.prevent="fetchScores('0-25')">0-25</button>
            <button class="score-link" @click.prevent="fetchScores('26-50')">26-50</button>
            <button class="score-link" @click.prevent="fetchScores('51-75')">51-75</button>
            <button class="score-link" @click.prevent="fetchScores('76-100')">76-100</button>
            <button class="score-link" @click.prevent="fetchScores('all')">Tümü</button>
        </div>

        <table class="table profile-table">
            <thead>
            <tr>
                <th>FOTOĞRAF</th>
                <th>İSİM</th>
                <th>PUAN</th>
            </tr>
            </thead>
            <tbody>
            <template  v-if="scores.length > 0">

                <tr v-for="score in scores">
                    <td><img class="rounded-circle img-fluid score-list-image" :src="'/uploads/' + score.img_url" :alt="score.name"/></td>
                    <td>{{ score.name }}</td>
                    <td>{{ score.score }}</td>
                </tr>
            </template>
            <template v-else>
                <tr>
                    <td colspan="3">İçerik bulunamadı.</td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>

</template>

<style scoped>
/* Puan Menüsü stilleri */
.score-menu {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    padding: 2px 20px 20px;
    gap: 10px;
    position: sticky;
    top: -2px;
    background-color: var(--bs-white);
}

.score-link {
    padding: 10px;
    background-color: #28a745;
    color: white;
    text-decoration: none;
    text-align: center;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.2s ease;
    min-width: 100px;
}

.score-link:hover {
    background-color: #218838;
    transform: scale(1.05);
}

.score-list-image {
    width: 100px;
    height: 100px;
    object-fit: cover;
}

.score-box {
    height: calc(100vh - 160px);
    overflow: auto;
}

.profile-table {
    overflow: auto;
    border-collapse: collapse;
}



</style>
