<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h1>SINAV SORULARI
                        <router-link class="btn btn-sm btn-primary" to="/sorular/ekle"> Soru Ekle</router-link>
                    </h1>
                    <hr>

                    <ListProduct v-for="soru in sorular" :key="soru.id"
                                 :soru="soru"
                                 :titleColor="selectedTitleColor"
                                 @deleteProduct="deleteProduct">
                    </ListProduct>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios'
import ListProduct from '../../components/sorular/ListProduct.vue'
import ColorChanger from '../../components/general/ColorChanger.vue'
export default {
    components: {
        ListProduct,
        ColorChanger,
    },
    data(){
        return {
            sorular: [],
            selectedColor: 'red',
            selectedTitleColor: 'red',
        }
    },
    methods: {
        indexFunction(param) {
            console.log('indexFunction', param);
        },
        deleteProduct(productId) {
            const authToken = localStorage.getItem('authToken')

            const headers = {
                'Authorization': 'Bearer ' + authToken
            }

            axios.delete('/api/sorular/' + productId, {headers}).then(res => {
                console.log(res);

                let productIndex = this.sorular.findIndex(product => product.id == productId)
                this.sorular.splice(productIndex, 1)
            })
        }
    },
    created() {
        const authToken = localStorage.getItem('authToken')

        const headers = {
            'Authorization': 'Bearer ' + authToken
        }

        axios.get('/api/sorular', {headers}).then(res => {
            console.log('res => ',res);
            this.sorular = res.data
        })
        console.log('Products component created.')
    }
}
</script>
<style>
.red{
    color: red;
}
.blue{
    color: blue;
}
</style>
