<template>
<!--    <div class="row justify-content-center align-items-center vh-100 bg-light">-->
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body text-center p-4">
                    <h3 class="mb-3 text-primary">Giriş Yap</h3>
                    <form @submit.prevent="loginUser">
                        <div class="form-group mb-4 position-relative">
                            <i class="fas fa-envelope icon"></i>
                            <input class="form-control" placeholder="Email" type="email" v-model="email" required>
                        </div>
                        <div class="form-group mb-4 position-relative">
                            <i class="fas fa-lock icon"></i>
                            <input class="form-control" placeholder="Parola" type="password" v-model="password" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <router-link to="/giris" class="btn btn-primary btn-block ">UYGULAMAYA GİRİŞ YAP</router-link> <!-- Giriş sayfasına yönlendirme bağlantısı: Kullanıcının uygulamaya giriş yapmasını sağlayacak bağlantı. -->
                            <router-link to="/kayitol" class="btn btn-primary btn-block btn-lg">Kayıt Ol</router-link> <!-- Kayıt ol sayfasına yönlendirme bağlantısı: Kullanıcının kayıt olmasını sağlayacak bağlantı. -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
<!--    </div>-->
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
        loginUser() {
            axios.post('api/login', {
                email: this.email,
                password: this.password
            }).then(() => {
                alert("Giriş başarılı");
            }).catch((error) => {
                alert("Bir hata oluştu: " + error.response.data.message);
            });
        }
    }
}
</script>

<style scoped>
.card {
    background-color: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
}

.card:hover {
    transform: scale(1.05);
}

h3 {
    font-size: 1.8rem;
    color: #007bff;
}

.form-group {
    position: relative;
}

.form-control {
    font-size: 1rem;
    padding: 0.75rem 2.5rem 0.75rem 1rem;
    border-radius: 20px;
}

.icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.2rem;
    color: #007bff;
}

.btn {
    font-size: 1.1rem;
    padding: 0.75rem 1rem;
    border-radius: 20px;
    background: linear-gradient(90deg, #007bff, #0056b3);
    color: #fff;
    transition: background 0.3s ease;
}

.btn:hover {
    background: linear-gradient(90deg, #0056b3, #007bff);
}

.bg-light {
    background-color: #f8f9fa !important;
}

@media (max-width: 576px) {
    .card {
        margin: 0 10px;
    }

    h3 {
        font-size: 1.5rem;
    }

    .form-control {
        font-size: 0.9rem;
        padding: 0.5rem 2rem 0.5rem 1rem;
    }

    .btn {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }
}
</style>
