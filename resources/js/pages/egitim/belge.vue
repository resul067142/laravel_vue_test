<template>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">SQL ve Redis Hakkında Bilgi</h1>
                <div class="card shadow-lg border-0 rounded-lg mb-5">
                    <div class="card-body p-4">
                        <h3 class="mb-3">SQL (Structured Query Language)</h3>
                        <p>
                            SQL, veri tabanları ile etkileşim kurmak ve bu veri tabanlarını yönetmek için kullanılan bir dildir. Veritabanında veri tanımlama, veri manipülasyonu, veri sorgulama ve veri kontrol işlemlerini gerçekleştirebilmek için kullanılır.
                            Kullanılan başlıca veri tabanı yönetim sistemleri arasında MySQL, PostgreSQL ve SQLite bulunmaktadır.
                        </p>
                        <ul>
                            <li><strong>Veri Tanımlama Dili (DDL)</strong>: Veri tabanı ve tablo oluşturma işlemlerini yapar (CREATE, ALTER, DROP).</li>
                            <li><strong>Veri Manipülasyon Dili (DML)</strong>: Veri ekleme, silme, güncelleme işlemleri (INSERT, DELETE, UPDATE).</li>
                            <li><strong>Veri Sorgulama Dili (DQL)</strong>: Verileri sorgulama ve listeleme işlemleri (SELECT).</li>
                        </ul>
                        <h3 class="mt-4 mb-3">Redis (Remote Dictionary Server)</h3>
                        <p>
                            Redis, açık kaynaklı, bellek içi çalışan bir veri yapısı deposudur. Yüksek hızlı veri önbellekleme, oturum yönetimi ve gerçek zamanlı analitik gibi işlemler için kullanılır. Ana bellekte veri tutarak çok düşük gecikme sürelerinde veri erişimi sağlar ve bu nedenle performans kritik uygulamalarda tercih edilir.
                        </p>
                        <ul>
                            <li><strong>Veri Türleri</strong>: Redis; string, list, set, sorted set ve hash gibi farklı veri türlerini destekler.</li>
                            <li><strong>Kullanım Alanları</strong>: Önbellekleme, mesaj kuyruğu, oturum saklama gibi işlemler için yaygın olarak kullanılır.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "SqlRedisInfoPage",
    data() {
        return {
            // Bu sayfada herhangi bir veri manipülasyonu veya API bağlantısı gerekmiyor.
        };
    },
    methods: {
        // Metotlar eklenebilir ancak bu sayfa sadece bilgi vermek amaçlı olduğu için gerek görülmedi.
    },
};
</script>

<style scoped>
.container {
    max-width: 800px;
}
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
    line-height: 1.6;
    color: #555;
}
ul {
    list-style-type: disc;
    padding-left: 20px;
}
li {
    margin-bottom: 10px;
    font-size: 1rem;
    color: #444;
}
</style>
