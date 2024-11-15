<template>
    <div :class="['container mx-auto px-4 pt-0 pb-2 min-h-screen', theme === 'dark' ? 'bg-gray-900 text-gray-100' : 'bg-white text-gray-900']">
        <h2 class="text-3xl font-bold text-indigo-400 mb-4">Aile Takibi</h2>

        <!-- Search, Filter -->
        <div class="flex flex-wrap items-center gap-2 mb-2 bg-gray-800 p-2 rounded-lg shadow-md">
            <input v-model="searchQuery" type="text" placeholder="Aile Üyesi Ara" class="bg-gray-700 text-gray-100 border border-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full md:w-1/4" />
            <div class="flex items-center">
                <label for="relationFilter" class="mr-1 font-medium text-gray-300">İlişki:</label>
                <select v-model="selectedRelation" id="relationFilter" class="bg-gray-700 text-gray-100 border border-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tümü</option>
                    <option v-for="relation in uniqueRelations" :key="relation" :value="relation">{{ relation }}</option>
                </select>
            </div>
            <div class="flex items-center">
                <label for="genderFilter" class="mr-1 font-medium text-gray-300">Cinsiyet:</label>
                <select v-model="selectedGender" id="genderFilter" class="bg-gray-700 text-gray-100 border border-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tümü</option>
                    <option value="Erkek">Erkek</option>
                    <option value="Kadın">Kadın</option>
                </select>
            </div>
            <div class="flex items-center">
                <label for="sortCriteria" class="mr-1 font-medium text-gray-300">Sıralama Ölçütü:</label>
                <select v-model="sortCriteria" id="sortCriteria" class="bg-gray-700 text-gray-100 border border-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Hiçbiri</option>
                    <option value="name">İsim</option>
                    <option value="age">Yaş</option>
                </select>
            </div>
        </div>

        <!-- Family Member Management -->
        <div class="flex justify-between items-center mb-4">
            <button @click="openAddModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-green-500">
                Üye Ekle
            </button>
            <button @click="toggleTheme" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Tema Değiştir
            </button>
            <button @click="exportToCSV" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                CSV'ye Aktar
            </button>
        </div>

        <!-- Family Members Pages -->
        <div class="overflow-hidden relative w-full" @touchstart="startTouch" @touchend="endTouch">
            <div class="flex transition-transform duration-500 ease-in-out" :style="{ transform: `translateX(-${currentPage * 100}%)` }">
                <!-- Each Page with 2 Members -->
                <div v-for="(page, pageIndex) in paginatedMembers" :key="pageIndex" class="w-full min-w-full flex flex-wrap justify-start items-stretch relative">
                    <div v-for="member in page" :key="member.id" class="card bg-gray-800 shadow-lg rounded-lg p-6 m-2 text-center flex flex-col items-center w-full md:w-1/2 lg:w-1/2 cursor-pointer overflow-hidden" @click="openModal(member)" style="height: 300px;">
                        <div v-if="member.photo_path">
                            <img :src="member.photo_path" class="w-32 h-32 rounded-full mb-4 object-cover" alt="Family Member Photo" />
                        </div>
                        <div v-else class="bg-gray-700 rounded-full w-32 h-32 mb-4 flex items-center justify-center">
                            <span class="text-4xl text-white font-bold">{{ getInitials(member.name) }}</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-100">{{ member.name }}</h3>
                        <p class="text-gray-400 mt-1">Yaş: {{ member.age }}</p>
                        <p class="text-gray-500 italic mt-1">{{ member.relation }}</p>
                        <p class="text-gray-500 italic mt-1" v-if="member.gender">Cinsiyet: {{ member.gender }}</p>
                        <div class="mt-auto flex space-x-4">
                            <button @click.stop="openEditModal(member)" class="text-indigo-400 hover:underline">Düzenle</button>
                            <button @click.stop="deleteMember(member.id)" class="text-red-500 hover:underline">Sil</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aile Ağacı Görünümü Toggle -->
        <div class="flex justify-end mt-4">
            <button @click="toggleFamilyTree" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                Aile Ağacı Görünümünü Değiştir
            </button>
        </div>
        <div v-if="familyTreeVisible" class="family-tree mt-4">
            <h3 class="text-2xl font-bold mb-4">Aile Ağacı Görünümü</h3>
            <p>Family tree representation coming soon...</p>
        </div>

        <!-- Detail Modal -->
        <transition name="modal">
            <div v-if="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-gray-800 rounded-lg p-8 w-full md:w-1/2 relative shadow-lg">
                    <button @click="closeModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-100 text-3xl">×</button>
                    <div class="text-center">
                        <div v-if="selectedMember.photo_path">
                            <img :src="selectedMember.photo_path" class="w-40 h-40 rounded-full mb-6 mx-auto object-cover" alt="Family Member Photo" />
                        </div>
                        <div v-else class="bg-gray-700 rounded-full w-40 h-40 mb-6 mx-auto flex items-center justify-center">
                            <span class="text-6xl text-white font-bold">{{ getInitials(selectedMember.name) }}</span>
                        </div>
                        <h3 class="text-3xl font-semibold text-gray-100">{{ selectedMember.name }}</h3>
                        <p class="text-gray-400 text-lg mt-3">Yaş: {{ selectedMember.age }}</p>
                        <p class="text-gray-500 italic mt-1">{{ selectedMember.relation }}</p>
                        <p class="text-gray-500 italic mt-1" v-if="selectedMember.gender">Cinsiyet: {{ selectedMember.gender }}</p>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Ekle/Düzenle Modal -->
        <transition name="modal">
            <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-gray-800 rounded-lg p-8 w-full md:w-1/2 relative shadow-lg">
                    <button @click="closeEditModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-100 text-3xl">×</button>
                    <h3 class="text-2xl font-semibold mb-6 text-gray-100 text-center">{{ isEditing ? 'Düzenle Member' : 'Üye Ekle' }}</h3>
                    <form @submit.prevent="isEditing ? updateMember() : addMember()">
                        <div class="mb-6">
                            <label class="block text-lg font-medium text-gray-300">İsim:</label>
                            <input v-model="editMemberData.name" type="text" class="bg-gray-700 text-gray-100 border border-gray-600 rounded-lg p-3 w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required />
                        </div>
                        <div class="mb-6">
                            <label class="block text-lg font-medium text-gray-300">Yaş:</label>
                            <input v-model.number="editMemberData.age" type="number" class="bg-gray-700 text-gray-100 border border-gray-600 rounded-lg p-3 w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required min="0" />
                        </div>
                        <div class="mb-6">
                            <label class="block text-lg font-medium text-gray-300">İlişki:</label>
                            <input v-model="editMemberData.relation" type="text" class="bg-gray-700 text-gray-100 border border-gray-600 rounded-lg p-3 w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required />
                        </div>
                        <div class="mb-6">
                            <label class="block text-lg font-medium text-gray-300">Cinsiyet:</label>
                            <select v-model="editMemberData.gender" class="bg-gray-700 text-gray-100 border border-gray-600 rounded-lg p-3 w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                <option value="">Cinsiyet Seç</option>
                                <option value="Erkek">Erkek</option>
                                <option value="Kadın">Kadın</option>
                            </select>
                        </div>
                        <div class="mb-6">
                            <label class="block text-lg font-medium text-gray-300">Fotoğraf:</label>
                            <input type="file" @change="onFileChange" class="bg-gray-700 text-gray-100 border border-gray-600 rounded-lg p-3 w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" accept="image/*" />
                        </div>
                        <div class="flex justify-center">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
            itemsPerPage: 2, // Display 2 members per page
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
        nextPage() { if (this.currentPage < this.paginatedMembers.length - 1) { this.currentPage += 1; } else { alert('Sağa kayacak yer kalmadı!'); } },
        prevPage() { if (this.currentPage > 0) { this.currentPage -= 1; } else { alert('Sola kayacak yer kalmadı!'); } },
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

            axios
                .post('/api/family', formData)
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

            axios
                .post(`/api/family/${this.editMemberData.id}`, formData)
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
                axios
                    .delete(`/api/family/${id}`)
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
            axios
                .get('/api/family')
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
.card {
    overflow: hidden;
}
.container {
    max-width: 1400px;
}
.card {
    transition: transform 0.3s, box-shadow 0.3s;
    width: calc(50% - 1rem);
    margin-bottom: 1rem;
    background-color: #2d3748;
    color: #f7fafc;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
@media (min-width: 1024px) {
    .card {
        width: calc(50% - 1rem);
    }
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
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
}
.family-tree {
    background-color: #2d3748;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-top: 1rem;
}
.theme-light .container {
    background-color: #f7fafc;
    color: #2d3748;
}
</style>
