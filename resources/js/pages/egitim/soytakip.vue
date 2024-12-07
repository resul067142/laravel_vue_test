<template>
    <div :class="['container max-w-screen-xl mx-auto px-1 pt-4 pb-4', theme === 'dark' ? 'bg-gray-900 text-gray-100' : 'bg-white text-gray-900']">
        <!-- Başlık ve Butonlar -->
        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
            <div class="flex gap-2">
                <button @click="openAddModal" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm shadow-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    Üye Ekle
                </button>
                <button @click="toggleTheme" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-lg text-sm shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Tema Değiştir
                </button>
                <button @click="exportToCSV" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    CSV'ye Aktar
                </button>
            </div>
        </div>

        <!-- Arama ve Filtreler (Aile Üyesi Ara ve Cinsiyet üstte, İlişki ve Sıralama altta) -->
        <div class="mb-1 p-2 rounded-lg shadow-md" :class="[theme === 'dark' ? 'bg-gray-800' : 'bg-gray-200']">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <!-- Arama Input -->
                <input v-model="searchQuery" type="text" placeholder="Aile Üyesi Ara"
                       class="flex-grow w-full sm:w-auto rounded-lg p-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       :class="theme === 'dark'
                                ? 'bg-gray-700 text-gray-100 border border-gray-600'
                                : 'bg-white text-gray-900 border border-gray-300'" />

                <!-- Cinsiyet Filtre -->
<!--                <div class="flex items-center">-->
<!--                    <label for="genderFilter" class="mr-2 text-sm font-medium"-->
<!--                           :class="theme === 'dark' ? 'text-gray-300' : 'text-gray-700'">Cinsiyet:</label>-->
<!--                    <select v-model="selectedGender" id="genderFilter"-->
<!--                            class="rounded-lg p-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"-->
<!--                            :class="theme === 'dark'-->
<!--                                    ? 'bg-gray-700 text-gray-100 border border-gray-600'-->
<!--                                    : 'bg-white text-gray-900 border border-gray-300'">-->
<!--                        <option value="">Tümü</option>-->
<!--                        <option value="Erkek">Erkek</option>-->
<!--                        <option value="Kadın">Kadın</option>-->
<!--                    </select>-->
<!--                </div>-->
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- İlişki Filtre -->
                <div class="flex items-center">
                    <label for="relationFilter" class="mr-2 text-sm font-medium"
                           :class="theme === 'dark' ? 'text-gray-300' : 'text-gray-700'">İlişki:</label>
                    <select v-model="selectedRelation" id="relationFilter"
                            class="rounded-lg p-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            :class="theme === 'dark'
                                    ? 'bg-gray-700 text-gray-100 border border-gray-600'
                                    : 'bg-white text-gray-900 border border-gray-300'">
                        <option value="">Tümü</option>
                        <option v-for="relation in uniqueRelations" :key="relation" :value="relation">{{ relation }}</option>
                    </select>
                </div>

                <!-- Sıralama Ölçütü -->
                <div class="flex items-center">
                    <label for="sortCriteria" class="mr-2 text-sm font-medium"
                           :class="theme === 'dark' ? 'text-gray-300' : 'text-gray-700'">Sıralama:</label>
                    <select v-model="sortCriteria" id="sortCriteria"
                            class="rounded-lg p-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            :class="theme === 'dark'
                                    ? 'bg-gray-700 text-gray-100 border border-gray-600'
                                    : 'bg-white text-gray-900 border border-gray-300'">
                        <option value="">Hiçbiri</option>
                        <option value="name">İsim</option>
                        <option value="age">Yaş</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Aile Üyeleri Sayfaları -->
        <div class="overflow-hidden relative w-full" @touchstart="startTouch" @touchend="endTouch">
            <div class="flex transition-transform duration-500 ease-in-out" :style="{ transform: `translateX(-${currentPage * 100}%)` }">
                <!-- Her Sayfa 2 Üye İçerir -->
                <div v-for="(page, pageIndex) in paginatedMembers" :key="pageIndex" class="w-full min-w-full flex flex-wrap justify-start items-stretch">
                    <div v-for="member in page" :key="member.id"
                         class="card shadow-lg rounded-lg p-4 m-2 text-center flex flex-col items-center cursor-pointer transition-transform transform-gpu hover:-translate-y-1 hover:shadow-xl"
                         :class="theme === 'dark' ? 'bg-gray-800 text-gray-100' : 'bg-gray-100 text-gray-900'"
                         @click="openModal(member)"
                         style="height: 360px; width: calc(50% - 1rem);">
                        <div v-if="member.photo_path">
                            <img :src="member.photo_path" class="w-32 h-32 rounded-full mb-4 object-cover" alt="Family Member Photo" />
                        </div>
                        <div v-else class="flex items-center justify-center rounded-full w-32 h-32 mb-4"
                             :class="theme === 'dark' ? 'bg-gray-700' : 'bg-gray-300'">
                            <span class="text-4xl font-bold">{{ getInitials(member.name) }}</span>
                        </div>
                        <h3 class="text-xl font-semibold">{{ member.name }}</h3>
                        <p class="text-lg mt-1">Yaş: {{ member.age }}</p>
                        <p class="text-base italic mt-1">{{ member.relation }}</p>
                        <p class="text-base italic mt-1" v-if="member.gender">Cinsiyet: {{ member.gender }}</p>
                        <div class="mt-auto flex space-x-4">
                            <button @click.stop="openEditModal(member)" class="text-indigo-400 hover:underline text-sm">Düzenle</button>
                            <button @click.stop="deleteMember(member.id)" class="text-red-500 hover:underline text-sm">Sil</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sayfa Geçiş Kontrolleri -->
        <div class="flex justify-center mt-4 space-x-2">
            <button @click="prevPage"
                    class="px-3 py-1 rounded-lg text-sm"
                    :class="theme === 'dark' ? 'bg-gray-800 text-gray-200 hover:bg-gray-700' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'">
                &larr; Geri
            </button>
            <button @click="nextPage"
                    class="px-3 py-1 rounded-lg text-sm"
                    :class="theme === 'dark' ? 'bg-gray-800 text-gray-200 hover:bg-gray-700' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'">
                İleri &rarr;
            </button>
        </div>

        <!-- Aile Ağacı Görünümü Toggle -->
        <div class="flex justify-end mt-4">
            <button @click="toggleFamilyTree"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-1 rounded-lg text-sm shadow-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                Aile Ağacı Görünümünü Değiştir
            </button>
        </div>
        <div v-if="familyTreeVisible" class="family-tree mt-4 p-4 rounded-lg shadow-md"
             :class="theme === 'dark' ? 'bg-gray-800 text-gray-100' : 'bg-gray-200 text-gray-900'">
            <h3 class="text-xl font-bold mb-2">Aile Ağacı Görünümü</h3>
            <p>Family tree representation coming soon...</p>
        </div>

        <!-- Detay Modal -->
        <transition name="modal">
            <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="rounded-lg p-6 w-full md:w-1/2 relative shadow-lg"
                     :class="theme === 'dark' ? 'bg-gray-800 text-gray-100' : 'bg-white text-gray-900'">
                    <button @click="closeModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-100 text-2xl">×</button>
                    <div class="text-center">
                        <div v-if="selectedMember.photo_path">
                            <img :src="selectedMember.photo_path" class="w-32 h-32 rounded-full mb-4 mx-auto object-cover" alt="Family Member Photo" />
                        </div>
                        <div v-else class="rounded-full w-32 h-32 mb-4 mx-auto flex items-center justify-center"
                             :class="theme === 'dark' ? 'bg-gray-700' : 'bg-gray-300'">
                            <span class="text-5xl font-bold">{{ getInitials(selectedMember.name) }}</span>
                        </div>
                        <h3 class="text-2xl font-semibold">{{ selectedMember.name }}</h3>
                        <p class="text-base mt-2">Yaş: {{ selectedMember.age }}</p>
                        <p class="italic mt-1">{{ selectedMember.relation }}</p>
                        <p class="italic mt-1" v-if="selectedMember.gender">Cinsiyet: {{ selectedMember.gender }}</p>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Ekle/Düzenle Modal -->
        <transition name="modal">
            <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div :class="theme === 'dark' ? 'bg-gray-800 text-gray-100' : 'bg-white text-gray-900'" class="rounded-lg p-6 w-full md:w-1/2 relative shadow-lg">
                    <button @click="closeEditModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-100 text-2xl">×</button>
                    <h3 class="text-xl font-semibold mb-4 text-center">{{ isEditing ? 'Üyeyi Düzenle' : 'Üye Ekle' }}</h3>
                    <form @submit.prevent="isEditing ? updateMember() : addMember()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- İsim -->
                            <div>
                                <label class="block text-sm font-medium mb-1"
                                       :class="theme === 'dark' ? 'text-gray-300' : 'text-gray-700'">İsim:</label>
                                <input v-model="editMemberData.name" type="text"
                                       class="w-full p-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                       :class="theme === 'dark'
                                               ? 'bg-gray-700 text-gray-100 border-gray-600'
                                               : 'bg-white text-gray-900 border border-gray-300'"
                                       required />
                            </div>
                            <!-- Yaş -->
                            <div>
                                <label class="block text-sm font-medium mb-1"
                                       :class="theme === 'dark' ? 'text-gray-300' : 'text-gray-700'">Yaş:</label>
                                <input v-model.number="editMemberData.age" type="number"
                                       class="w-full p-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                       :class="theme === 'dark'
                                               ? 'bg-gray-700 text-gray-100 border-gray-600'
                                               : 'bg-white text-gray-900 border border-gray-300'"
                                       required min="0" />
                            </div>
                            <!-- İlişki -->
                            <div>
                                <label class="block text-sm font-medium mb-1"
                                       :class="theme === 'dark' ? 'text-gray-300' : 'text-gray-700'">İlişki:</label>
                                <input v-model="editMemberData.relation" type="text"
                                       class="w-full p-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                       :class="theme === 'dark'
                                               ? 'bg-gray-700 text-gray-100 border-gray-600'
                                               : 'bg-white text-gray-900 border border-gray-300'"
                                       required />
                            </div>
                            <!-- Cinsiyet -->
                            <div>
                                <label class="block text-sm font-medium mb-1"
                                       :class="theme === 'dark' ? 'text-gray-300' : 'text-gray-700'">Cinsiyet:</label>
                                <select v-model="editMemberData.gender"
                                        class="w-full p-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        :class="theme === 'dark'
                                                ? 'bg-gray-700 text-gray-100 border-gray-600'
                                                : 'bg-white text-gray-900 border border-gray-300'"
                                        required>
                                    <option value="">Cinsiyet Seç</option>
                                    <option value="Erkek">Erkek</option>
                                    <option value="Kadın">Kadın</option>
                                </select>
                            </div>
                            <!-- Fotoğraf -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-1"
                                       :class="theme === 'dark' ? 'text-gray-300' : 'text-gray-700'">Fotoğraf:</label>
                                <input type="file" @change="onFileChange"
                                       class="w-full p-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                       :class="theme === 'dark'
                                               ? 'bg-gray-700 text-gray-100 border-gray-600'
                                               : 'bg-white text-gray-900 border border-gray-300'"
                                       accept="image/*" />
                            </div>
                        </div>
                        <div class="flex justify-center mt-6">
                            <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                {{ isEditing ? 'Güncelle' : 'Ekle' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import axios from 'axios';
import { saveAs } from 'file-saver';

export default {
    name: 'SoyTakibi',
    data() {
        return {
            currentPage: 0,
            familyData: [],
            itemsPerPage: 2,
            showDetailModal: false,
            showEditModal: false,
            selectedMember: {},
            searchQuery: '',
            selectedRelation: '',
            selectedGender: '',
            sortCriteria: '',
            editMemberData: { id: null, name: '', age: null, relation: '', gender: '', photo: null },
            isEditing: false,
            startX: 0,
            familyTreeVisible: false,
            notifications: [],
            theme: 'dark',
        };
    },
    computed: {
        uniqueRelations() {
            const relations = this.familyData.map(member => member.relation);
            return [...new Set(relations)];
        },
        filteredMembers() {
            const query = this.searchQuery.toLowerCase();
            return this.familyData.filter((member) => {
                const matchesSearch = member.name.toLowerCase().includes(query);
                const matchesRelation = this.selectedRelation ? member.relation === this.selectedRelation : true;
                const matchesGender = this.selectedGender ? member.gender === this.selectedGender : true;
                return matchesSearch && matchesRelation && matchesGender;
            });
        },
        paginatedMembers() {
            return this.sortedMembers.reduce((acc, curr, idx) => {
                const pageIndex = Math.floor(idx / this.itemsPerPage);
                if (!acc[pageIndex]) acc[pageIndex] = [];
                acc[pageIndex].push(curr);
                return acc;
            }, []);
        },
        sortedMembers() {
            if (this.sortCriteria) {
                return [...this.filteredMembers].sort((a, b) => {
                    if (this.sortCriteria === 'age') {
                        return a.age - b.age;
                    } else {
                        return a.name.localeCompare(b.name);
                    }
                });
            }
            return this.filteredMembers;
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
            if (this.currentPage < this.paginatedMembers.length - 1) {
                this.currentPage += 1;
            } else {
                alert('Daha ileri gidemiyorsunuz!');
            }
        },
        prevPage() {
            if (this.currentPage > 0) {
                this.currentPage -= 1;
            } else {
                alert('Daha geri gidemiyorsunuz!');
            }
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
            this.editMemberData = { id: null, name: '', age: null, relation: '', gender: '', photo: null };
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
            formData.append('gender', this.editMemberData.gender);
            if (this.editMemberData.photo) formData.append('photo', this.editMemberData.photo);

            axios.post('/api/family', formData)
                .then(() => {
                    this.fetchFamilyData();
                    this.closeEditModal();
                })
                .catch((error) => {
                    console.error('Error adding member:', error.response?.data || error);
                    alert('Üye eklenemedi. Lütfen tekrar deneyin.');
                });
        },
        updateMember() {
            const formData = new FormData();
            formData.append('name', this.editMemberData.name);
            formData.append('age', this.editMemberData.age);
            formData.append('relation', this.editMemberData.relation);
            formData.append('gender', this.editMemberData.gender);
            if (this.editMemberData.photo) formData.append('photo', this.editMemberData.photo);

            axios.post(`/api/family/${this.editMemberData.id}`, formData)
                .then(() => {
                    this.fetchFamilyData();
                    this.closeEditModal();
                })
                .catch((error) => {
                    console.error('Error updating member:', error.response?.data || error);
                    alert('Üye güncellenemedi. Lütfen tekrar deneyin.');
                });
        },
        deleteMember(id) {
            if (confirm('Bu üyeyi silmek istediğinize emin misiniz?')) {
                axios.delete(`/api/family/${id}`)
                    .then(() => {
                        this.fetchFamilyData();
                    })
                    .catch((error) => {
                        console.error('Error deleting member:', error.response?.data || error);
                        alert('Üye silinemedi. Lütfen tekrar deneyin.');
                    });
            }
        },
        fetchFamilyData() {
            axios.get('/api/family')
                .then((response) => {
                    this.familyData = response.data;
                })
                .catch((error) => {
                    console.error('Error fetching family data:', error.response?.data || error);
                });
        },
        getInitials(name) {
            return name
                .split(' ')
                .map((n) => n[0])
                .join('')
                .toUpperCase();
        },
        toggleFamilyTree() {
            this.familyTreeVisible = !this.familyTreeVisible;
        },
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
        },
        addNotification(message) {
            this.notifications.push({ message, timestamp: new Date() });
        },
        exportToCSV() {
            const csvData = [
                ['İsim', 'Yaş', 'Relation', 'Gender'],
                ...this.familyData.map(member => [member.name, member.age, member.relation, member.gender])
            ];
            const csvContent = csvData.map(e => e.join(",")).join("\n");
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            saveAs(blob, 'family_data.csv');
        }
    },
    created() {
        this.fetchFamilyData();
    },
};
</script>

<style scoped>
.container {
    max-width: 1400px;
}
.card {
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.card:hover {
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1),0 10px 10px -5px rgba(0,0,0,0.04);
}
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s;
}
.modal-enter,
.modal-leave-to {
    opacity: 0;
}
button:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(66,153,225,0.5);
}
.family-tree {
    margin-top: 0.5rem;
}
</style>
