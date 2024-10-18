<template>
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body text-center p-4">
                    <h3 class="mb-3">Admin Girişi</h3>
                    <form @submit.prevent="login">
                        <div class="form-group mb-3">
                            <input class="form-control" placeholder="Email" type="email" v-model="email" required>
                        </div>
                        <div class="form-group mb-3">
                            <input class="form-control" placeholder="Parola" type="password" v-model="password" required>
                        </div>
                        <button class="btn btn-danger btn-block">Giriş Yap</button>
                    </form>
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
            axios.post('api/login', { email: this.email, password: this.password }).then(res => {
                localStorage.setItem('authToken', res.data.token)
                setTimeout(() => {
                    window.location.href = '/sorular'
                }, 200)
            })
        }
    }
}
</script>

<style scoped>
/* Mobil ve web görünümü için uyumlu stil */
.card {
    background-color: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h3 {
    font-size: 1.5rem;
    color: #333;
}

.form-control {
    font-size: 1rem;
    padding: 0.75rem;
}

.btn {
    font-size: 1rem;
    padding: 0.75rem 1rem;
}

/* Küçük cihazlar için optimize edilmiş görünüm */
@media (max-width: 576px) {
    .card {
        margin: 0 10px;
    }

    h3 {
        font-size: 1.3rem;
    }

    .form-control {
        font-size: 0.9rem;
        padding: 0.5rem;
    }

    .btn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
}
</style>
