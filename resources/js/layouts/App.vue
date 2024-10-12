<template>
    <div>
        <div  style="text-align: center">
            <router-link to="/" style="margin-right:20px;">Ana Sayfa</router-link>
            <router-link to="/sorular"  style="margin-top: 20px; margin-right:20px;" v-if="isAuth">SORULAR</router-link>
            <router-link to="/admin" style="margin-top: 20px;" v-if="!isAuth">Admin Girişi</router-link>
            <button v-else type="button" @click="logout" style="background-color: transparent; border: 0">Admin Çıkış</button>
        </div>
        <router-view ></router-view>
    </div>
</template>
<script>
export default {
    data() {
        return {
            isAuth: false
        }
    },
    methods: {
        logout() {
            localStorage.removeItem('authToken')
            window.location.href = '/admin'
        }
    },
    created() {
        this.isAuth = localStorage.getItem('authToken')
    }
}
</script>
