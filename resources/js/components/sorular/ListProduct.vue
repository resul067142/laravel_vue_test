<template>
    <div class="question-container">
        <h4 class="question-header">#{{ soru.id }} {{ soru.soru }}</h4>
        <div class="options-container">

            <p class="option">Seçenek 1: {{ soru.secenek_1 }}</p>
            <p class="option">Seçenek 2: {{ soru.secenek_2 }}</p>
            <p class="option">Seçenek 3: {{ soru.secenek_3 }}</p>
            <p class="option">Seçenek 4: {{ soru.secenek_4 }}</p>

            <p class="correct-answer">Doğru Cevap: {{ soru.dogru_cevap }}</p>
        </div>
        <div class="button-container">
            <router-link class="btn btn-edit" :to="'/sorular/' + soru.id">Düzenle</router-link>
            <button type="button" class="btn btn-delete" @click="deleteProduct(soru.id)">Sil</button>
        </div>

        <!-- Geri Sayım Süresi -->
<!--        <div class="timer">-->
<!--            <p>Kalan Süre: {{ remainingTime }} saniye</p>-->
<!--        </div>-->

    </div>
</template>

<script>
export default {
    props: {
        soru: {
            type: Object,
            required: true,
        },
        remainingTime: {
            type: Number,
            required: true,
        },
    },
    methods: {
        deleteProduct(productId) {
            this.$emit('deleteProduct', productId);
        },
    },
};
</script>

<style scoped>


.question-container {
    background-color: #f0f8ff; /* Light blue background */
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.question-header {
    background-color: #007acc; /* Darker blue */
    color: white;
    padding: 10px;
    border-radius: 5px;
    text-align: center;
}

.options-container {
    background-color: #e0f7fa; /* Light cyan */
    padding: 10px;
    border-radius: 5px;
    margin-top: 10px;
}

.option {
    margin: 8px 0;
    transition: background-color 0.7s ease;
    background-color: cadetblue;
}

.option:hover {
    background-color: #b2ebf2; /* Slightly darker cyan on hover */
    margin-left: 1px;
}

.correct-answer {
    font-weight: bold;
    color: #ff5722; /* Orange color for the correct answer */
    margin-top: 10px;
}

.button-container {
    margin-top: 10px;
    display: flex;
    justify-content: space-between; /* Space between buttons */
}

.btn {
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-edit {
    background-color: #4caf50; /* Green for edit button */
    color: white;
}

.btn-edit:hover {
    background-color: #45a049; /* Darker green on hover */
}

.btn-delete {
    background-color: #f44336; /* Red for delete button */
    color: white;
}

.btn-delete:hover {
    background-color: #d32f2f; /* Darker red on hover */
}
</style>
