<template>
    <div class="container my-5">
        <div class="text-center mb-5">
            <h2 class="display-4 text-primary">Rozet Koleksiyonu</h2>
            <p class="lead text-muted">Kazandığınız tüm rozetleri burada keşfedin ve başarılarınızla gurur duyun. Daha fazla rozet kazanmak için yeni hedefler belirleyin ve gelişmeye devam edin!</p>
        </div>
        <div class="row badge-grid">
            <div v-if="badges.length" class="col-12 d-flex flex-wrap justify-content-center">
                <div
                    v-for="badge in badges.slice(0, 4)"
                    :key="badge.id"
                    class="badge-item card shadow-sm p-2 m-2 text-center"
                >
                    <h5 class="text-info">{{ badge.name }}</h5>
                    <p class="text-muted">{{ badge.description }}</p>
                </div>
            </div>
            <div v-else class="col-12 text-center">
                <p class="text-muted">Henüz rozet kazanmadınız, yeni sınavlara katılın ve ilk rozetinizi kazanın!</p>
            </div>
        </div>
        <div class="text-center mt-5">
                        <h4 class="text-info">Daha Fazla Rozet Kazan!</h4>
            <p class="text-dark">Kendinize meydan okuyun ve daha fazla rozet kazanmak için yeni sınavlara katılın. Başarılarınızla ilham verici olun!</p>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            badges: [
                {
                    id: 1,
                    name: 'Başarı Rozeti',
                    description: '10 sınav başarıyla tamamlandı.',
                    icon: 'https://www.example.com/rozetler/basari.png' // Gerçek rozet ikonu
                },
                {
                    id: 2,
                    name: 'Hızlı Çözücü',
                    description: 'En hızlı sınav çözen kişi.',
                    icon: 'https://www.example.com/rozetler/hizli_cozucu.png' // Gerçek rozet ikonu
                },
                {
                    id: 3,
                    name: 'İstikrarlı Öğrenci',
                    description: '20 gün boyunca aralıksız sınav çözüldü.',
                    icon: 'https://www.example.com/rozetler/istikrarli_ogrenci.png' // Gerçek rozet ikonu
                },
                {
                    id: 4,
                    name: 'Takım Oyuncusu',
                    description: 'Grup çalışmasında yer aldı.',
                    icon: 'https://www.example.com/rozetler/takim_oyuncusu.png' // Gerçek rozet ikonu
                },
                {
                    id: 5,
                    name: 'Yaratıcı Çözümler',
                    description: 'Sorunlara yaratıcı çözümler getirdi.',
                    icon: 'https://www.example.com/rozetler/yaratici_cozumler.png' // Gerçek rozet ikonu
                },
                {
                    id: 6,
                    name: 'Önder Öğrenci',
                    description: 'Diğer öğrencilere rehberlik etti.',
                    icon: 'https://www.example.com/rozetler/onder_ogrenci.png' // Gerçek rozet ikonu
                }
            ]
        };
    }
};
</script>

<style scoped>
.container {
    max-width: 1200px;
}

.badge-grid {
    gap: 20px;
}

.badge-item {
    width: 120px;
    border-radius: 15px;
    transition: transform 0.3s ease;
    background-color: #f8f9fa;
}

.badge-item:hover {
    transform: scale(1.05);
}



h5 {
    font-size: 1rem;
    color: #17a2b8;
}

p {
    font-size: 0.85rem;
    color: #555;
}

.celebration-icon {
    width: 120px; height: 120px;
    border-radius: 50%;
    border: 4px solid #17a2b8;
}

/* Mobil görünüm için optimize edilmiş stil */
@media (max-width: 576px) {
    h2 {
        font-size: 1.8rem;
    }

    h5 {
        font-size: 0.9rem;
    }

    p {
        font-size: 0.8rem;
    }

    .badge-icon {
        width: 60px;
        height: 60px;
    }

    .celebration-icon {
        width: 100px;
        height: 100px;
    }
}
</style>
