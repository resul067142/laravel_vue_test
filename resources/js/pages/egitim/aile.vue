
<template>

    <div class="family-tree" ref="familyTree">
        <!-- Üst Satır: Anne ve Baba -->
        <div class="generation top-generation">
            <div class="scroll-container">
                <div
                    class="member"
                    v-for="(parent, index) in filteredParents"
                    :key="index"
                >
                    <img class="avatar" :src="getImageUrl(parent.image)" :alt="parent.name" />
                    <p class="name">{{ parent.name }}</p>
                    <p class="role">{{ parent.role }}</p>
                    <button
                        @click="showDetails(parent)"
                        class="details-button"
                    >
                        Detayları Göster
                    </button>
                </div>
            </div>
        </div>

        <!-- İlişki Çizgisi -->
        <div class="relationship-line"></div>

        <!-- Orta Satır: Çocuklar -->
        <div class="generation middle-generation">
            <div class="scroll-container">
                <div
                    class="member"
                    v-for="(child, index) in filteredChildren"
                    :key="index"
                >
                    <img class="avatar" :src="getImageUrl(child.image)" :alt="child.name" />
                    <p class="name">{{ child.name }}</p>
                    <p class="role">{{ child.role }}</p>
                    <button
                        @click="showDetails(child)"
                        class="details-button"
                    >
                        Detayları Göster
                    </button>
                </div>
            </div>
        </div>

        <!-- İlişki Çizgisi -->
        <div class="relationship-line"></div>

        <!-- Alt Satır: Akrabalar -->
        <div class="generation bottom-generation">
            <div class="scroll-container">
                <div
                    class="member"
                    v-for="(relative, index) in filteredRelatives"
                    :key="index"
                >
                    <img class="avatar" :src="getImageUrl(relative.image)" :alt="relative.name" />
                    <p class="name">{{ relative.name }}</p>
                    <p class="role">{{ relative.role }}</p>
                    <button
                        @click="showDetails(relative)"
                        class="details-button"
                    >
                        Detayları Göster
                    </button>
                </div>
            </div>
        </div>

        <!-- Arama Kutusu -->
        <div class="search-container">
            <button @click="resetSearch" class="reset-search-button">Aramayı Sıfırla</button>
            <input
                type="text"
                v-model="searchQuery"
                placeholder="Üye Ara..."
                class="search-input"
            />
        </div>

        <!-- Üye Ekleme Butonu -->
        <div class="add-member-container">
            <button @click="openAddMemberModal" class="add-member-button">
                Yeni Üye Ekle
            </button>
        </div>

        <!-- Detaylar Modal -->
        <div v-if="selectedMember" class="modal">
            <div class="modal-content">
                <span class="close" @click="closeDetails">&times;</span>
                <img class="modal-avatar" :src="getImageUrl(selectedMember.image)" :alt="selectedMember.name" />
                <h2>{{ selectedMember.name }} - {{ selectedMember.role }}</h2>
                <p>{{ selectedMember.details }}</p>
                <!-- Ek detaylı özellikler -->
                <ul>
                    <li>Doğum Tarihi: {{ selectedMember.birthDate }}</li>
                    <li>Meslek: {{ selectedMember.occupation }}</li>
                    <li>Hobiler: {{ selectedMember.hobbies.join(", ") }}</li>
                    <li>Sosyal Medya: <a :href="selectedMember.socialMedia" target="_blank">{{ selectedMember.socialMedia }}</a></li>
                    <li>İletişim: {{ selectedMember.contact }}</li>
                </ul>
                <!-- Üye Düzenleme ve Silme -->
                <button @click="openEditMemberModal(selectedMember)" class="edit-button">
                    Düzenle
                </button>
                <button @click="deleteMember(selectedMember)" class="delete-button">
                    Sil
                </button>
            </div>
        </div>

        <!-- Üye Ekleme/Düzenleme Modal -->
        <div v-if="showAddEditModal" class="modal">
            <div class="modal-content">
                <span class="close" @click="closeAddEditModal">&times;</span>
                <h2>{{ isEditing ? 'Üye Düzenle' : 'Yeni Üye Ekle' }}</h2>
                <form @submit.prevent="saveMember">
                    <input v-model="memberForm.name" placeholder="İsim" required />
                    <input v-model="memberForm.role" placeholder="Rol" required />
                    <input type="file" @change="onImageChange" />
                    <input v-model="memberForm.birthDate" placeholder="Doğum Tarihi" />
                    <input v-model="memberForm.occupation" placeholder="Meslek" />
                    <input v-model="memberForm.contact" placeholder="İletişim" />
                    <input v-model="memberForm.socialMedia" placeholder="Sosyal Medya Profili" />
                    <input v-model="memberForm.hobbies" placeholder="Hobiler (Virgülle ayırın)" />
                    <textarea v-model="memberForm.details" placeholder="Detaylar"></textarea>
                    <select v-model="memberForm.generation" required>
                        <option value="parents">Üst Nesil (Anne/Baba)</option>
                        <option value="children">Orta Nesil (Çocuklar)</option>
                        <option value="relatives">Alt Nesil (Akrabalar)</option>
                    </select>
                    <button type="submit" class="save-button">
                        Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            members: [],
            selectedMember: null,
            showAddEditModal: false,
            isEditing: false,
            memberForm: {
                id: null,
                name: "",
                role: "",
                image: null,
                details: "",
                birthDate: "",
                occupation: "",
                hobbies: "",
                contact: "",
                socialMedia: "",
                generation: "children",
            },
            searchQuery: "",
        };
    },
    computed: {
        filteredParents() {
            return this.members.filter(
                (member) =>
                    member.generation === "parents" &&
                    member.name.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
        filteredChildren() {
            return this.members.filter(
                (member) =>
                    member.generation === "children" &&
                    member.name.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
        filteredRelatives() {
            return this.members.filter(
                (member) =>
                    member.generation === "relatives" &&
                    member.name.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
        allMembers() {
            return this.members;
        },
    },
    methods: {
        resetSearch() {
            this.searchQuery = "";
        },
        debounce(func, wait) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        },
        showDetails(member) {
            this.selectedMember = member;
        },
        closeDetails() {
            this.selectedMember = null;
        },
        openAddMemberModal() {
            this.closeDetails();
            this.showAddEditModal = true;
            this.isEditing = false;
            this.memberForm = {
                id: null,
                name: "",
                role: "",
                image: null,
                details: "",
                birthDate: "",
                occupation: "",
                hobbies: "",
                contact: "",
                socialMedia: "",
                generation: "children",
            };
        },
        openEditMemberModal(member) {
            this.showAddEditModal = true;
            this.isEditing = true;
            this.memberForm = { ...member };
            // Hobileri stringe çevir
            if (Array.isArray(this.memberForm.hobbies)) {
                this.memberForm.hobbies = this.memberForm.hobbies.join(", ");
            }
            this.memberForm.image = null; // Resmi yeniden yüklemek gerekebilir
        },
        closeAddEditModal() {
            this.showAddEditModal = false;
        },
        onImageChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.memberForm.image = file;
            }
        },
        saveMember() {
            if (!this.memberForm.name.trim()) {
                alert("İsim alanı boş bırakılamaz.");
                return;
            }
            // Hobileri diziye çevir
            if (this.memberForm.hobbies) {
                this.memberForm.hobbies = this.memberForm.hobbies
                    .split(",")
                    .map((hobby) => hobby.trim());
            } else {
                this.memberForm.hobbies = [];
            }
            // Sosyal medya URL'sini kontrol et
            if (
                this.memberForm.socialMedia &&
                !this.memberForm.socialMedia.startsWith("http")
            ) {
                this.memberForm.socialMedia = "https://" + this.memberForm.socialMedia;
            }
            if (this.isEditing) {
                this.updateMember(this.memberForm);
            } else {
                this.addMember(this.memberForm);
            }
            this.closeAddEditModal();
        },
        addMember(member) {
            const formData = new FormData();

            for (let key in member) {
                if (member[key] !== null && member[key] !== undefined) {
                    if (key === 'hobbies') {
                        formData.append(key, JSON.stringify(member[key]));
                    } else if (key === 'image' && member[key] instanceof File) {
                        formData.append(key, member[key]);
                    } else {
                        formData.append(key, member[key]);
                    }
                }
            }

            axios.post('/api/members', formData)
                .then(response => {
                    this.members.push(response.data);
                })
                .catch(error => {
                    console.error(error);
                });
        },
        updateMember(member) {
            const formData = new FormData();

            for (let key in member) {
                if (member[key] !== null && member[key] !== undefined) {
                    if (key === 'hobbies') {
                        formData.append(key, JSON.stringify(member[key]));
                    } else if (key === 'image' && member[key] instanceof File) {
                        formData.append(key, member[key]);
                    } else {
                        formData.append(key, member[key]);
                    }
                }
            }

            axios.post(`/api/members/${member.id}?_method=PUT`, formData)
                .then(response => {
                    const index = this.members.findIndex(m => m.id === member.id);
                    if (index !== -1) {
                        this.$set(this.members, index, response.data);
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        },
        deleteMember(member) {
            if (!confirm("Bu üyeyi silmek istediğinize emin misiniz?")) {
                return;
            }
            axios.delete(`/api/members/${member.id}`)
                .then(() => {
                    const index = this.members.indexOf(member);
                    if (index !== -1) {
                        this.members.splice(index, 1);
                        this.closeDetails();
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        },
        getImageUrl(imagePath) {
            if (imagePath) {
                // Eğer resim yolu tam URL değilse, storage yolunu ekle
                if (!imagePath.startsWith('http')) {
                    return `/storage/${imagePath}`;
                }
                return imagePath;
            } else {
                return 'https://via.placeholder.com/150'; // Varsayılan resim yolu
            }
        },
        fetchMembers() {
            axios.get('/api/members')
                .then(response => {
                    this.members = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
    },
    mounted() {
        this.keyboardNavigationHandler = this.debounce((event) => {
            if (event.key === "Escape") {
                this.closeDetails();
            }
        }, 200);
        document.addEventListener("keydown", this.keyboardNavigationHandler);
        this.fetchMembers();
    },
    beforeDestroy() {
        document.removeEventListener("keydown", this.keyboardNavigationHandler);
    },
};
</script>


<style scoped>
.family-tree {
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow: hidden;
    max-width: 100%;
    margin: auto;
    padding: 10px;
}

.generation {
    width: 100%;
    margin-bottom: 10px;
    display: flex;
    justify-content: center;
}

.scroll-container {
    display: flex;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    justify-content: center;
}

.member {
    flex: 0 0 auto;
    scroll-snap-align: start;
    background: #fff;
    margin: 0 10px;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    min-width: 120px;
    max-width: 150px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
}

.name {
    font-weight: bold;
    margin-top: 10px;
    font-size: 1.1em;
}

.role {
    color: #777;
    margin-bottom: 10px;
}

.details-button {
    padding: 8px 12px;
    background-color: #0066cc;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.details-button:hover {
    background-color: #004d99;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 90%;
    max-width: 400px;
    position: relative;
    text-align: center;
}

.modal-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
}

.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 1.5em;
    cursor: pointer;
}

.edit-button,
.delete-button {
    padding: 8px 12px;
    margin-top: 10px;
    margin-right: 5px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.edit-button {
    background-color: #28a745;
    color: #fff;
}

.delete-button {
    background-color: #dc3545;
    color: #fff;
}

.edit-button:hover {
    background-color: #218838;
}

.delete-button:hover {
    background-color: #c82333;
}

.search-container {
    width: 100%;
    text-align: center;
    margin-bottom: 10px;
}

.search-input {
    width: 70%;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.reset-search-button {
    padding: 8px 12px;
    margin-right: 5px;
    background-color: #ffc107;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.reset-search-button:hover {
    background-color: #e0a800;
}

.add-member-container {
    width: 100%;
    text-align: center;
    margin-bottom: 10px;
}

.add-member-button {
    padding: 8px 15px;
    background-color: #17a2b8;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.add-member-button:hover {
    background-color: #138496;
}

.relationship-line {
    width: 100%;
    height: 2px;
    background-color: #ccc;
    margin: 5px 0;
}

@media screen and (max-width: 768px) {
    .member {
        min-width: 100px;
        max-width: 120px;
    }
    .avatar {
        width: 80px;
        height: 80px;
    }
}

@media screen and (max-width: 480px) {
    .member {
        min-width: 80px;
        max-width: 100px;
    }
    .avatar {
        width: 70px;
        height: 70px;
    }
    .modal-content {
        padding: 15px;
    }
    .modal-avatar {
        width: 100px;
        height: 100px;
    }
}

@media screen and (max-width: 360px) {
    .member {
        min-width: 70px;
        max-width: 90px;
    }
    .avatar {
        width: 60px;
        height: 60px;
    }
    .modal-avatar {
        width: 80px;
        height: 80px;
    }
}
</style>
