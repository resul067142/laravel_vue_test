<template>
    <div class="row">
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="col-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="mb-3">Admin Girişi</h3>
                        <form @click.prevent="login">
                            <input class="form-control mb-2" placeholder="Email" type="email" v-model="email" required>
                            <input class="form-control mb-2" placeholder="Parola" type="password" v-model="password" required>
                            <button class="btn btn-primary">Giriş</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            email: null,
            password: null
        }
    },
    methods: {
        login() {
            axios.post('api/login', {email: this.email, password: this.password}).then(res => {
                localStorage.setItem('authToken', res.data.token)
                setTimeout(() => {

                    window.location.href = '/sorular'
                }, 200)
            })
        }
    }
}
</script>
