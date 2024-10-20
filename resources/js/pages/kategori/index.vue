<template>
    <div class="row justify-content-center align-items-start" style="margin: 0; padding: 0; position: relative; top: 0; margin-top: 0;">
        <div class="col-12 col-md-10" style="padding: 0; margin: 0;">
            <div class="card shadow-lg border-0 rounded-lg" style="margin: 0; padding: 0;">
                <div class="card-body p-4">
                    <h3 class="mb-4 text-center text-primary">Kategoriler</h3>
                    <div v-if="categories.length" class="category-list">
                        <div class="row">
                            <div
                                v-for="category in categories"
                                :key="category.id"
                                class="col-md-6 mb-0" style="margin-bottom: 5px;"
                            >
                                <div class="category-item p-2 shadow-sm rounded-lg">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0 text-success">{{ category.name }}</h5>
                                        <span class="badge badge-pill badge-info">#{{ category.id }}</span>
                                    </div>
                                    <p class="text-muted">{{ category.description }}</p>
                                    <div class="d-flex justify-content-between mt-2">
                                        <button class="btn btn-outline-primary" @click="goToCategory(category.id)">
                                            İncele
                                        </button>
                                        <button class="btn btn-outline-danger" @click="deleteCategory(category.id)">
                                            Sil
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-center text-muted">Henüz kategori yok.</p>
                    </div>
                </div>
                <div class="card-footer text-center bg-primary text-white py-3">
                    <p class="mb-2">Yeni kategoriler ekleyerek çeşitliliği artırabilirsiniz!</p>
                    <button class="btn btn-light" @click="addCategory">Yeni Kategori Ekle</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            categories: [
                { id: 1, name: 'Java', description: 'Java programlama dili ve uygulamalarına katılabilirsiniz.' },
                { id: 2, name: 'SQL', description: 'SQL veritabanı sorguları ve veri yönetimi hakkında bilgi edinin.' },
                { id: 3, name: 'JavaScript', description: 'JavaScript ile web geliştirme ve interaktif uygulamalar hakkında bilgi edinin.' }
            ]
        }
    },
    methods: {
        goToCategory(id) {
            // Kategori detay sayfasına yönlendirme
            window.location.href = `/category/${id}`;
        },
        deleteCategory(id) {
            // Kategori silme işlemi
            this.categories = this.categories.filter(category => category.id !== id);
            alert('Kategori başarıyla silindi.');
        },
        addCategory() {
            // Yeni kategori ekleme işlemi
            const newId = this.categories.length + 1;
            const newCategory = {
                id: newId,
                name: `Yeni Kategori ${newId}`,
                description: `Yeni eklenen kategori ${newId} için açıklama.`
            };
            this.categories.push(newCategory);
            alert('Yeni kategori başarıyla eklendi.');
        }
    }
}
</script>

<style scoped>
/* Kategori sayfası için şık ve mobil uyumlu stil */
.card {
    background-color: #fff;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    margin-top: 0;
}

h3 {
    font-size: 1.8rem;
    color: #007bff;
}

.category-item {
    background-color: #e9ecef;
    border-left: 5px solid #17a2b8;
    padding: 1px;
    height: auto;
}

h5 {
    font-size: 1.4rem;
    color: #28a745;
}

p {
    font-size: 0.7rem;
    color: #666;
}

.btn {
    font-size: 1rem;
    padding: 0.4rem 0.8rem;
}

.card-footer {
    background-color: #007bff;
    color: #fff;
    border-top: none;
    border-radius: 0 0 15px 15px;
}

/* Mobil görünüm için optimize edilmiş stil */
@media (max-width: 576px) {
    .card {
        margin: 0 5px;
    }

    h3 {
        font-size: 1.4rem;
    }

    h5 {
        font-size: 1.1rem;
    }

    p {
        font-size: 0.85rem;
    }

    .btn {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }
}
</style>
