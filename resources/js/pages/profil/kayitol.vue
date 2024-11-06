<template>
    <div class="row justify-content-center align-items-center vh-100 bg-light" style="background-image: url('https://images.unsplash.com/photo-1579256945827-58d2b8dd05b9?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'); background-size: cover; background-position: center;">
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body text-center p-4">
                    <h3 class="mb-3 text-primary">Kayıt Ol</h3>
                    <form @submit.prevent="registerUser">
                        <div class="form-group mb-4 position-relative">
                            <i class="fas fa-user icon"></i>
                            <input class="form-control" placeholder="Ad Soyad" type="text" v-model="name" required>
                        </div>
                        <div class="form-group mb-4 position-relative">
                            <i class="fas fa-envelope icon"></i>
                            <input class="form-control" placeholder="Email" type="email" v-model="email" required>
                        </div>
                        <div class="form-group mb-4 position-relative">
                            <i class="fas fa-lock icon"></i>
                            <input class="form-control" placeholder="Parola" type="password" v-model="password" required>
                        </div>

                        <div class="d-flex justify-content-around mt-4">
                            <router-link to="/giris" class="btn btn-primary w-44 fw-bold fs-6">Uygulamaya Giriş Yap</router-link>
                            <router-link to="/kayitol" class="btn btn-success btn-block btn-lg">Kaydıt Ol</router-link>
                        </div>

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
            name: null,
            email: null,
            password: null
        }
    },
    methods: {
        registerUser() {
            axios.post('api/register', {
                name: this.name,
                email: this.email,
                password: this.password
            }).then(() => {
                alert("Kayıt başarılı");
            }).catch((error) => {
                alert("Bir hata oluştu: " + error.response.data.message);
            });
        }
    }
}
</script>

<style scoped>
.card {
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: scale(1.05);
    box-shadow: 0 16px 32px rgba(0, 0, 0, 0.4);
}

h3 {
    font-size: 2rem;
    color: #007bff;
    font-weight: bold;
}

.form-group {
    position: relative;
}

.form-control {
    font-size: 1rem;
    padding: 0.75rem 2.5rem 0.75rem 1rem;
    border-radius: 10px;
    border: 1px solid #ced4da;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

.icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.2rem;
    color: #6c757d;
}

.btn-primary {
    font-size: 1.2rem;
    padding: 0.75rem 1.5rem;
    border-radius: 30px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
    border: none;
    transition: background 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #007bff);
    box-shadow: 0 10px 20px rgba(0, 123, 255, 0.4);
    transform: translateY(-3px);
}

.btn-primary:active {
    transform: translateY(1px);
    box-shadow: 0 5px 10px rgba(0, 123, 255, 0.2);
}

.btn-success {
    font-size: 1.2rem;
    padding: 0.75rem 1.5rem;
    border-radius: 30px;
    background: linear-gradient(135deg, #28a745, #218838);
    color: #fff;
    border: none;
    transition: background 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
}

.btn-success:hover {
    background: linear-gradient(135deg, #218838, #28a745);
    box-shadow: 0 10px 20px rgba(40, 167, 69, 0.4);
    transform: translateY(-3px);
}

.btn-success:active {
    transform: translateY(1px);
    box-shadow: 0 5px 10px rgba(40, 167, 69, 0.2);
}

.bg-light {
    background-color: #f8f9fa !important;
}

@media (max-width: 576px) {
    .card {
        margin: 0 10px;
    }

    h3 {
        font-size: 1.8rem;
    }

    .form-control {
        font-size: 0.9rem;
        padding: 0.5rem 2rem 0.5rem 1rem;
    }

    .btn {
        font-size: 1.1rem;
        padding: 0.5rem 1.5rem;
    }
}
</style>
