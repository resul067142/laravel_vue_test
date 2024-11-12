<template>
    <div class="container mx-auto px-4 py-6">
        <div class="text-center mb-5">
            <h2 class="text-4xl font-bold text-blue-600">{{ $t('familyTracking') }}</h2>
            <p class="text-lg text-gray-600">{{ $t('discoverFamilyMembers') }}</p>
        </div>

        <!-- Dil Seçimi -->
        <div class="flex justify-end mb-4">
            <label class="mr-2">{{ $t('language') }}:</label>
            <select v-model="$i18n.locale" class="border rounded-lg p-2">
                <option value="tr">Türkçe</option>
                <option value="en">English</option>
            </select>
        </div>

        <!-- Arama ve Filtreleme -->
        <div class="flex justify-between items-center mb-5">
            <input
                v-model="searchQuery"
                type="text"
                :placeholder="$t('searchFamilyMember')"
                class="border rounded-lg p-2 w-1/2"
            />
            <div>
                <label for="relationFilter" class="mr-2">{{ $t('relation') }}:</label>
                <select v-model="selectedRelation" id="relationFilter" class="border rounded-lg p-2">
                    <option value="">{{ $t('all') }}</option>
                    <option v-for="relation in uniqueRelations" :key="relation" :value="relation">
                        {{ relation }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Aile Üyesi Yönetimi -->
        <div class="flex justify-end mb-5">
            <button @click="openAddModal" class="bg-green-500 text-white px-4 py-2 rounded-lg">
                {{ $t('addMember') }}
            </button>
        </div>

        <!-- Aile Üyeleri Sayfaları -->
        <div
            class="overflow-hidden relative w-full"
            @touchstart="startTouch"
            @touchend="endTouch"
        >
            <div
                class="flex transition-transform duration-300"
                :style="{ transform: `translateX(-${currentPage * 100}%)` }"
            >
                <!-- Her Sayfa -->
                <div
                    v-for="(page, pageIndex) in paginatedMembers"
                    :key="pageIndex"
                    class="w-full min-w-full flex flex-wrap justify-center items-center relative"
                >
                    <!-- Aile Üyesi Kartları -->
                    <div
                        v-for="member in page"
                        :key="member.id"
                        class="card bg-white shadow-md rounded-lg p-6 m-4 text-center flex flex-col items-center"
                    >
                        <div v-if="member.photo_path">
                            <img :src="member.photo_path" class="w-24 h-24 rounded-full mb-4" />
                        </div>
                        <div
                            v-else
                            class="bg-gray-300 rounded-full w-24 h-24 mb-4 flex items-center justify-center cursor-pointer"
                            @click="openModal(member)"
                        >
                            <!-- Fotoğraf yerine boş daire -->
                            <span class="text-4xl text-white">{{ member.initials }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ member.name }}</h3>
                        <p class="text-gray-600">{{ $t('age') }}: {{ member.age }}</p>
                        <p class="text-gray-500 italic">{{ member.relation }}</p>
                        <div class="mt-2 flex space-x-2">
                            <button @click="openEditModal(member)" class="text-blue-500 hover:underline">{{ $t('edit') }}</button>
                            <button @click="deleteMember(member.id)" class="text-red-500 hover:underline">{{ $t('delete') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sayfa Numaraları -->
        <div class="flex justify-center mt-4">
            <button
                v-for="(page, index) in totalPages"
                :key="index"
                @click="goToPage(index)"
                :class="[
                    'mx-1 px-3 py-1 rounded-full',
                    currentPage === index ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700',
                ]"
            >
                {{ index + 1 }}
            </button>
        </div>

        <!-- Detay Modal -->
        <transition name="modal">
            <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-1/2 relative">
                    <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl">
                        &times;
                    </button>
                    <div class="text-center">
                        <div v-if="selectedMember.photo_path">
                            <img :src="selectedMember.photo_path" class="w-32 h-32 rounded-full mb-4 mx-auto" />
                        </div>
                        <div v-else class="bg-gray-300 rounded-full w-32 h-32 mb-4 mx-auto flex items-center justify-center">
                            <!-- Fotoğraf yerine boş daire -->
                            <span class="text-6xl text-white">{{ selectedMember.initials }}</span>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-800">{{ selectedMember.name }}</h3>
                        <p class="text-gray-600">{{ $t('age') }}: {{ selectedMember.age }}</p>
                        <p class="text-gray-500 italic">{{ selectedMember.relation }}</p>
                        <p class="mt-4 text-gray-700">{{ $t('detailedInfoHere') }}</p>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Ekleme/Düzenleme Modal -->
        <transition name="modal">
            <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-1/2 relative">
                    <button @click="closeEditModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl">
                        &times;
                    </button>
                    <h3 class="text-xl font-semibold mb-4">{{ isEditing ? $t('editMember') : $t('addMember') }}</h3>
                    <form @submit.prevent="isEditing ? updateMember() : addMember()">
                        <div class="mb-4">
                            <label class="block text-gray-700">{{ $t('name') }}:</label>
                            <input v-model="editMemberData.name" type="text" class="border rounded-lg p-2 w-full" required />
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">{{ $t('age') }}:</label>
                            <input v-model.number="editMemberData.age" type="number" class="border rounded-lg p-2 w-full" required />
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">{{ $t('relation') }}:</label>
                            <input v-model="editMemberData.relation" type="text" class="border rounded-lg p-2 w-full" required />
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">{{ $t('photo') }}:</label>
                            <input type="file" @change="onFileChange" class="border rounded-lg p-2 w-full" />
                        </div>
                        <div class="text-right">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                                {{ isEditing ? $t('update') : $t('add') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>

        <!-- Aile Ağacı Görselleştirmesi -->
        <div class="mt-10">
            <h3 class="text-2xl font-bold mb-4">{{ $t('familyTree') }}</h3>
            <div id="familyTree" class="overflow-auto">
                <!-- Aile ağacı burada görselleştirilecek -->
                <div v-if="familyTreeData" class="p-4 bg-gray-100 rounded-lg">
                    <!-- Aile ağacı gösterimi -->
                    <ul>
                        <li v-for="member in familyTreeData" :key="member.id">
                            {{ member.name }} - {{ member.relation }}
                            <ul v-if="member.children">
                                <li v-for="child in member.children" :key="child.id">
                                    {{ child.name }} - {{ child.relation }}
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div v-else>
                    <p>{{ $t('noFamilyTreeData') }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'SoyTakibi',
    data() {
        return {
            currentPage: 0,
            startX: 0,
            familyData: [],
            itemsPerPage: 4,
            showDetailModal: false,
            showEditModal: false,
            selectedMember: {},
            searchQuery: '',
            selectedRelation: '',
            editMemberData: {
                id: null,
                name: '',
                age: null,
                relation: '',
                photo: null,
            },
            isEditing: false,
            familyTreeData: null,
            loading: false,
            error: null,
        };
    },
    computed: {
        filteredMembers() {
            return this.familyData.filter((member) => {
                const matchesSearch = member.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                const matchesRelation = this.selectedRelation ? member.relation === this.selectedRelation : true;
                return matchesSearch && matchesRelation;
            });
        },
        paginatedMembers() {
            const pages = [];
            for (let i = 0; i < this.filteredMembers.length; i += this.itemsPerPage) {
                pages.push(this.filteredMembers.slice(i, i + this.itemsPerPage));
            }
            return pages;
        },
        totalPages() {
            return this.paginatedMembers.length;
        },
        uniqueRelations() {
            const relations = this.familyData.map((member) => member.relation);
            return [...new Set(relations)];
        },
    },
    methods: {
        startTouch(event) {
            this.startX = event.touches[0].clientX;
        },
        endTouch(event) {
            const endX = event.changedTouches[0].clientX;
            if (this.startX - endX > 50) {
                this.nextPage();
            } else if (this.startX - endX < -50) {
                this.prevPage();
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages - 1) {
                this.currentPage += 1;
            }
        },
        prevPage() {
            if (this.currentPage > 0) {
                this.currentPage -= 1;
            }
        },
        goToPage(index) {
            this.currentPage = index;
        },
        onFileChange(event) {
            this.editMemberData.photo = event.target.files[0];
        },
        openModal(member) {
            this.selectedMember = member;
            this.showDetailModal = true;
        },
        closeModal() {
            this.showDetailModal = false;
        },
        openAddModal() {
            this.isEditing = false;
            this.editMemberData = {
                id: null,
                name: '',
                age: null,
                relation: '',
                photo: null,
            };
            this.showEditModal = true;
        },
        openEditModal(member) {
            this.isEditing = true;
            this.editMemberData = { ...member, photo: null };
            this.showEditModal = true;
        },
        closeEditModal() {
            this.showEditModal = false;
        },
        addMember() {
            const formData = new FormData();
            formData.append('name', this.editMemberData.name);
            formData.append('age', this.editMemberData.age);
            formData.append('relation', this.editMemberData.relation);
            if (this.editMemberData.photo) {
                formData.append('photo', this.editMemberData.photo);
            }
            axios.post('/api/family', formData).then(() => {
                this.fetchFamilyData();
                this.closeEditModal();
            });
        },
        updateMember() {
            const formData = new FormData();
            formData.append('name', this.editMemberData.name);
            formData.append('age', this.editMemberData.age);
            formData.append('relation', this.editMemberData.relation);
            if (this.editMemberData.photo) {
                formData.append('photo', this.editMemberData.photo);
            }
            axios.post(`/api/family/${this.editMemberData.id}`, formData).then(() => {
                this.fetchFamilyData();
                this.closeEditModal();
            });
        },
        deleteMember(id) {
            if (confirm(this.$t('confirmDelete'))) {
                axios.delete(`/api/family/${id}`).then(() => {
                    this.fetchFamilyData();
                });
            }
        },
        getInitials(name) {
            return name
                .split(' ')
                .map((n) => n[0])
                .join('')
                .toUpperCase();
        },
        fetchFamilyData() {
            this.loading = true;
            axios
                .get('/api/family')
                .then((response) => {
                    this.familyData = response.data.map((member) => ({
                        ...member,
                        initials: this.getInitials(member.name),
                    }));
                    this.updateFamilyTreeData();
                })
                .catch((error) => {
                    this.error = error;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        updateFamilyTreeData() {
            // Aile ağacı verilerini güncelle
            this.familyTreeData = this.familyData.filter((member) => !member.parentId);
            this.familyTreeData.forEach((member) => {
                member.children = this.familyData.filter((child) => child.parentId === member.id);
            });
        },
    },
    watch: {
        searchQuery() {
            this.currentPage = 0;
        },
        selectedRelation() {
            this.currentPage = 0;
        },
    },
    created() {
        this.fetchFamilyData();
    },
};
</script>

<style scoped>
.container {
    max-width: 1200px;
}
.card {
    width: calc(50% - 2rem); /* Her satırda 2 kart olacak şekilde ayarlandı */
    margin: 1rem;
}
@media (max-width: 768px) {
    .card {
        width: calc(100% - 2rem); /* Küçük ekranlarda 1 kart */
    }
}

/* Animasyonlar */
.modal-enter-active, .modal-leave-active {
    transition: opacity 0.3s;
}
.modal-enter, .modal-leave-to {
    opacity: 0;
}
</style>
