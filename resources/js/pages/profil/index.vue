<template>
    <div class="row justify-content-center align-items-start mt-4">
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body text-center p-4">
                    <img class="rounded-circle mb-3" :src="user.avatar" alt="Profil Resmi" width="120" height="120" />
                    <h3 class="mb-3 text-primary">{{ user.name }}</h3>
                    <p class="text-muted mb-4 font-weight-bold">{{ user.email }}</p>
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <h5 class="text-info">{{ examStats.completed }}</h5>
                            <p class="text-muted font-weight-bold">Tamamlanan Sınavlar</p>
                        </div>
                        <div>
                            <h5 class="text-info">{{ examStats.pending }}</h5>
                            <p class="text-muted font-weight-bold">Bekleyen Sınavlar</p>
                        </div>
                        <div>
                            <h5 class="text-info">{{ examStats.score }}</h5>
                            <p class="text-muted font-weight-bold">Toplam Puan</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <h5 class="text-warning">{{ user.badges.length }}</h5>
                            <p class="text-muted font-weight-bold">Kazanılan Rozetler</p>
                        </div>
                        <div>
                            <h5 class="text-warning">{{ user.rank }}</h5>
                            <p class="text-muted font-weight-bold">Kullanıcı Sıralaması</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button style="margin-right: 4px;" class="btn btn-primary flex-fill mr-2" @click="navigateToEditProfile">Profili Düzenle</button>
                        <button class="btn btn-danger flex-fill ml-2" @click="logout">Çıkış Yap</button>
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
            user: {
                name: 'Resul AKTAŞ',
                email: 'resulaktas.com',
                avatar: 'https://i.pravatar.cc/120?img=65', // Placeholder avatar resmi
                badges: ['Başlangıç', 'Orta Seviye', 'İleri Seviye'],
                rank: 5
            },
            examStats: {
                completed: 15,
                pending: 20,
                score: 350
            }
        }
    },
    methods: {
        navigateToEditProfile() {
            // Profili düzenleme sayfasına yönlendirme
            this.$router.push('/profile/edit');
        },
        logout() {
            // Çıkış yapma işlemi
            localStorage.removeItem('authToken');
            this.$router.push('/login');
        }
    }
}
</script>

<style scoped>
/* Profil sayfası için şık ve mobil uyumlu stil */
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

p {
    font-size: 1rem;
    color: #666;
}

.btn {
    font-size: 1rem;
    padding: 0.75rem 1rem;
}

.d-flex .btn {
    min-width: 0;
}

/* Mobil görünüm için optimize edilmiş stil */
@media (max-width: 576px) {
    .card {
        margin: 0 10px;
    }

    h3 {
        font-size: 1.3rem;
    }

    p {
        font-size: 0.9rem;
    }

    .btn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
}
</style>
