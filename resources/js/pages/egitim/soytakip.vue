<template>
    <div class="container mx-auto px-4 py-6">
        <div class="text-center mb-5">
            <h2 class="text-4xl font-bold text-blue-600">{{ $t('familyTracking') }}</h2>
            <p class="text-lg text-gray-600">{{ $t('discoverFamilyMembers') }}</p>
        </div>

        <!-- Language Selection -->
        <div class="flex justify-end mb-4">
            <label class="mr-2">{{ $t('language') }}:</label>
            <select v-model="$i18n.locale" class="border rounded-lg p-2">
                <option value="tr">Türkçe</option>
                <option value="en">English</option>
            </select>
        </div>

        <!-- Search and Filter -->
        <div class="flex flex-wrap gap-4 justify-between items-center mb-5">
            <input
                v-model="searchQuery"
                type="text"
                :placeholder="$t('searchFamilyMember')"
                class="border rounded-lg p-2 w-full sm:w-1/2"
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

        <!-- Add Family Member Button -->
        <div class="flex justify-end mb-5">
            <button @click="openAddModal" class="bg-green-500 text-white px-4 py-2 rounded-lg">
                {{ $t('addMember') }}
            </button>
        </div>

        <!-- Family Members Pages -->
        <div class="overflow-hidden relative w-full" @touchstart="startTouch" @touchend="endTouch">
            <div class="flex transition-transform duration-300" :style="{ transform: `translateX(-${currentPage * 100}%)` }">
                <!-- Pages -->
                <div
                    v-for="(page, pageIndex) in paginatedMembers"
                    :key="pageIndex"
                    class="w-full min-w-full flex flex-wrap justify-center items-center relative"
                >
                    <!-- Family Member Cards -->
                    <div
                        v-for="member in page"
                        :key="member.id"
                        class="card bg-white shadow-md rounded-lg p-6 m-4 text-center flex flex-col items-center"
                    >
                        <div
                            class="bg-gray-300 rounded-full w-24 h-24 mb-4 flex items-center justify-center cursor-pointer"
                            @click="openModal(member)"
                        >
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

        <!-- Page Numbers -->
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

        <!-- Modal Structure and Transitions Omitted for Brevity -->

        <!-- Family Tree Visualization -->
        <div class="mt-10">
            <h3 class="text-2xl font-bold mb-4">{{ $t('familyTree') }}</h3>
            <div id="familyTree" class="overflow-auto">
                <div v-if="familyTreeData" class="p-4 bg-gray-100 rounded-lg">
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
            editMemberData: { id: null, name: '', age: null, relation: '' },
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
            return this.filteredMembers.reduce((pages, member, index) => {
                const pageIndex = Math.floor(index / this.itemsPerPage);
                if (!pages[pageIndex]) pages[pageIndex] = [];
                pages[pageIndex].push(member);
                return pages;
            }, []);
        },
        totalPages() {
            return this.paginatedMembers.length;
        },
        uniqueRelations() {
            return [...new Set(this.familyData.map((member) => member.relation))];
        },
    },
    methods: {
        startTouch(event) {
            this.startX = event.touches[0].clientX;
        },
        endTouch(event) {
            const endX = event.changedTouches[0].clientX;
            if (this.startX - endX > 50) this.nextPage();
            else if (this.startX - endX < -50) this.prevPage();
        },
        nextPage() {
            if (this.currentPage < this.totalPages - 1) this.currentPage += 1;
        },
        prevPage() {
            if (this.currentPage > 0) this.currentPage -= 1;
        },
        goToPage(index) {
            this.currentPage = index;
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
            this.editMemberData = { id: null, name: '', age: null, relation: '' };
            this.showEditModal = true;
        },
        openEditModal(member) {
            this.isEditing = true;
            this.editMemberData = { ...member };
            this.showEditModal = true;
        },
        closeEditModal() {
            this.showEditModal = false;
        },
        addMember() {
            const newMember = { ...this.editMemberData, id: Date.now(), initials: this.getInitials(this.editMemberData.name) };
            this.familyData.push(newMember);
            this.updateFamilyTreeData();
            this.closeEditModal();
        },
        updateMember() {
            const index = this.familyData.findIndex((m) => m.id === this.editMemberData.id);
            if (index !== -1) {
                this.familyData.splice(index, 1, { ...this.editMemberData, initials: this.getInitials(this.editMemberData.name) });
                this.updateFamilyTreeData();
                this.closeEditModal();
            }
        },
        deleteMember(id) {
            if (confirm(this.$t('confirmDelete'))) {
                this.familyData = this.familyData.filter((member) => member.id !== id);
                this.updateFamilyTreeData();
            }
        },
        getInitials(name) {
            return name.split(' ').map((n) => n[0]).join('').toUpperCase();
        },
        fetchFamilyData() {
            this.loading = true;
            axios.get('/api/family')
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
            this.familyTreeData = this.familyData.filter((member) => !member.parentId);
            this.familyTreeData.forEach((member) => {
                member.children = this.familyData.filter((child) => child.parentId === member.id);
            });
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
    width: calc(50% - 2rem);
    margin: 1rem;
}
@media (max-width: 768px) {
    .card {
        width: calc(100% - 2rem);
    }
}

.modal-enter-active, .modal-leave-active {
    transition: opacity 0.3s;
}
.modal-enter, .modal-leave-to {
    opacity: 0;
}
</style>
