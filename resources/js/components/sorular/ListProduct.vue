<template>
    <div class="row">
        <div class="col-12">
            <h4 style="background-color: darkcyan; color: white">#{{soru.id}} {{soru.soru}}</h4>
            <div style="background-color: palegoldenrod;">
                <p class="m-0">
                    Seçenek 1: {{soru.secenek_1}}
                </p>
                <p class="m-0">
                    Seçenek 2: {{soru.secenek_2}}
                </p>
                <p class="m-0">
                    Seçenek 3: {{soru.secenek_3}}
                </p>
                <p class="m-0">
                    Seçenek 4: {{soru.secenek_4}}
                </p>
                <p class="m-0">
                    Doğru Cevap: {{soru.dogru_cevap}}
                </p>
            </div>
            <p class="mt-2" style="background-color: #79b8f6">
                <router-link class="btn btn-warning btn-sm m-1" :to="'/sorular/' + soru.id">Düzenle</router-link>
                <button type="button" class="btn btn-danger btn-sm m-1" @click="deleteProduct(soru.id)">Sil</button>
            </p>
            <hr>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            soru: {
                type: Object,
                required: true,
            },

        },
        methods: {
            deleteProduct(productId) {
                this.$emit('deleteProduct', productId)
            },
        }
    }
</script>
