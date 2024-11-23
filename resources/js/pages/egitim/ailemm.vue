<template>
    <div class="family-tree" ref="familyTree">
        <!-- Arama ve Butonlar -->
        <div class="top-controls">
            <div class="search-input-group">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Üye Ara..."
                    class="search-input"
                />
                <button @click="resetSearch" class="reset-search-button">Sıfırla</button>
            </div>
            <button @click="openAddMemberModal" class="add-member-button">
                Yeni Üye Ekle
            </button>
        </div>

        <!-- Üst Satır: Anne ve Baba -->
        <div class="generation top-generation" v-if="filteredParents.length">
            <h3>Üst Nesil (Anne/Baba)</h3>
            <div class="scroll-container">
                <div
                    class="member"
                    v-for="parent in filteredParents"
                    :key="parent.id"
                    @click="showDetails(parent)"
                >
                    <img v-if="parent.image" class="avatar" :src="parent.image" :alt="parent.name" />
                    <div v-else class="no-image-avatar">{{ getInitials(parent.name) }}</div>
                    <p class="name">{{ parent.name }}</p>
                    <p class="role">{{ parent.role }}</p>
                </div>
            </div>
        </div>

        <!-- İlişki Çizgisi -->
        <div class="relationship-line" v-if="filteredParents.length && filteredRelatives.length"></div>

        <!-- Alt Satır: Akrabalar -->
        <div class="generation bottom-generation" v-if="filteredRelatives.length">
            <h3>Alt Nesil (Akrabalar)</h3>
            <div class="scroll-container">
                <div
                    class="member"
                    v-for="relative in filteredRelatives"
                    :key="relative.id"
                    @click="showDetails(relative)"
                >
                    <img v-if="relative.image" class="avatar" :src="relative.image" :alt="relative.name" />
                    <div v-else class="no-image-avatar">{{ getInitials(relative.name) }}</div>
                    <p class="name">{{ relative.name }}</p>
                    <p class="role">{{ relative.role }}</p>
                </div>
            </div>
        </div>

        <!-- Detaylar Modal -->
        <div v-if="selectedMember" class="modal" @click.self="closeDetails">
            <div class="modal-content">
                <span class="close" @click="closeDetails">&times;</span>
                <img v-if="selectedMember.image" class="modal-avatar" :src="selectedMember.image" :alt="selectedMember.name" />
                <div v-else class="no-image-avatar-modal">{{ getInitials(selectedMember.name) }}</div>
                <h2>{{ selectedMember.name }} - {{ selectedMember.role }}</h2>
                <p>{{ selectedMember.details }}</p>
                <!-- Ek detaylı özellikler -->
                <ul>
                    <li><strong>Doğum Tarihi:</strong> {{ selectedMember.birthDate }}</li>
                    <li><strong>Meslek:</strong> {{ selectedMember.occupation }}</li>
                    <li><strong>Hobiler:</strong> {{ selectedMember.hobbies.join(", ") }}</li>
                    <li>
                        <strong>Sosyal Medya:</strong>
                        <a :href="selectedMember.socialMedia" target="_blank">{{ selectedMember.socialMedia }}</a>
                    </li>
                    <li><strong>İletişim:</strong> {{ selectedMember.contact }}</li>
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
        <div v-if="showAddEditModal" class="modal" @click.self="closeAddEditModal">
            <div class="modal-content">
                <span class="close" @click="closeAddEditModal">&times;</span>
                <h2>{{ isEditing ? 'Üye Düzenle' : 'Yeni Üye Ekle' }}</h2>
                <form @submit.prevent="saveMember" enctype="multipart/form-data">
                    <input v-model="memberForm.name" placeholder="İsim" required />
                    <input v-model="memberForm.role" placeholder="Rol" required />
                    <input type="file" @change="onFileChange" accept="image/*" />
                    <input v-model="memberForm.birthDate" placeholder="Doğum Tarihi" />
                    <input v-model="memberForm.occupation" placeholder="Meslek" />
                    <input v-model="memberForm.contact" placeholder="İletişim" />
                    <input v-model="memberForm.socialMedia" placeholder="Sosyal Medya Profili" />
                    <input v-model="memberForm.hobbies" placeholder="Hobiler (Virgülle ayırın)" />
                    <textarea v-model="memberForm.details" placeholder="Detaylar"></textarea>
                    <select v-model="memberForm.generation" required>
                        <option value="" disabled>-- Nesil Seçin --</option>
                        <option value="parents">Üst Nesil (Anne/Baba)</option>
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

axios.defaults.baseURL = 'http://localhost:8000'; // Laravel projenizin URL'sini buraya yazın

export default {
    data() {
        return {
            parents: [],
            relatives: [],
            selectedMember: null,
            showAddEditModal: false,
            isEditing: false,
            memberForm: {
                id: null,
                name: "",
                role: "",
                photo: null, // Fotoğraf dosyasını tutacak
                details: "",
                birthDate: "",
                occupation: "",
                hobbies: "",
                contact: "",
                socialMedia: "",
                generation: "parents",
            },
            searchQuery: "",
        };
    },
    computed: {
        filteredParents() {
            return this.parents.filter((member) =>
                member.name.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
        filteredRelatives() {
            return this.relatives.filter((member) =>
                member.name.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
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
                photo: null,
                details: "",
                birthDate: "",
                occupation: "",
                hobbies: "",
                contact: "",
                socialMedia: "",
                generation: "parents",
            };
        },
        openEditMemberModal(member) {
            this.showAddEditModal = true;
            this.isEditing = true;
            this.memberForm = { ...member, photo: null };
            // Hobileri stringe çevir
            this.memberForm.hobbies = this.memberForm.hobbies.join(", ");
        },
        closeAddEditModal() {
            this.showAddEditModal = false;
        },
        onFileChange(event) {
            this.memberForm.photo = event.target.files[0];
        },
        saveMember() {
            if (!this.memberForm.name.trim()) {
                alert("İsim alanı boş bırakılamaz.");
                return;
            }
            if (!this.memberForm.role.trim()) {
                alert("Rol alanı boş bırakılamaz.");
                return;
            }
            // Hobileri diziye çevir
            const memberData = { ...this.memberForm };
            memberData.hobbies = memberData.hobbies
                ? memberData.hobbies.split(',').map(hobby => hobby.trim())
                : [];

            // Sosyal medya URL'sini kontrol et
            if (
                memberData.socialMedia &&
                !/^https?:\/\//i.test(memberData.socialMedia)
            ) {
                memberData.socialMedia = "https://" + memberData.socialMedia;
            }

            // FormData oluştur
            const formData = new FormData();
            formData.append('name', memberData.name);
            formData.append('role', memberData.role);
            formData.append('details', memberData.details);
            formData.append('birthDate', memberData.birthDate);
            formData.append('occupation', memberData.occupation);
            formData.append('contact', memberData.contact);
            formData.append('socialMedia', memberData.socialMedia);
            formData.append('generation', memberData.generation);
            formData.append('hobbies', JSON.stringify(memberData.hobbies));
            if (memberData.photo) {
                formData.append('photo', memberData.photo);
            }

            if (this.isEditing) {
                // Üye güncelleme API çağrısı
                axios.post(`/api/members/${memberData.id}`, formData)
                    .then(() => {
                        this.fetchMembers(); // Üye listesini yeniden yükle
                        this.closeAddEditModal();
                    })
                    .catch(error => {
                        console.error('Üye güncellenirken hata oluştu:', error.response?.data || error);
                        alert('Üye güncellenemedi. Lütfen tekrar deneyin.');
                    });
            } else {
                // Yeni üye ekleme API çağrısı
                axios.post('/api/members', formData)
                    .then(() => {
                        this.fetchMembers(); // Üye listesini yeniden yükle
                        this.closeAddEditModal();
                    })
                    .catch(error => {
                        console.error('Üye eklenirken hata oluştu:', error.response?.data || error);
                        alert('Üye eklenemedi. Lütfen tekrar deneyin.');
                    });
            }
        },
        deleteMember(member) {
            if (!confirm("Bu üyeyi silmek istediğinize emin misiniz?")) {
                return;
            }
            axios.delete(`/api/members/${member.id}`)
                .then(() => {
                    this.fetchMembers(); // Üye listesini yeniden yükle
                    this.closeDetails();
                })
                .catch(error => {
                    console.error('Üye silinirken hata oluştu:', error.response?.data || error);
                    alert('Üye silinemedi. Lütfen tekrar deneyin.');
                });
        },
        fetchMembers() {
            axios.get('/api/members')
                .then(response => {
                    const members = response.data;
                    this.parents = members.filter(member => member.generation === 'parents');
                    this.relatives = members.filter(member => member.generation === 'relatives');
                })
                .catch(error => {
                    console.error('Veriler alınırken hata oluştu:', error.response?.data || error);
                });
        },
        getInitials(name) {
            return name
                .split(' ')
                .map((n) => n[0])
                .join('')
                .toUpperCase();
        },
    },
    mounted() {
        this.fetchMembers();
        this.keyboardNavigationHandler = this.debounce((event) => {
            if (event.key === "Escape") {
                this.closeDetails();
                this.closeAddEditModal();
            }
        }, 200);
        document.addEventListener("keydown", this.keyboardNavigationHandler);
    },
    beforeUnmount() {
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
    margin: 0 auto;
    padding: 0;
}

/* Üst Kontroller */
.top-controls {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 10px;
}

.search-input-group {
    display: flex;
    margin-bottom: 5px;
}

.search-input {
    width: 200px;
    padding: 8px;
    border-radius: 5px 0 0 5px;
    border: 1px solid #ccc;
}

.reset-search-button {
    padding: 8px 12px;
    background-color: #ffc107;
    color: #fff;
    border: none;
    border-left: none;
    border-radius: 0 5px 5px 0;
    cursor: pointer;
}

.add-member-button {
    padding: 8px 12px;
    background-color: #17a2b8;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.add-member-button:hover {
    background-color: #138496;
}

.generation {
    width: 100%;
    margin-bottom: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.generation h3 {
    margin-bottom: 10px;
    color: #333;
}

.scroll-container {
    display: flex;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    justify-content: center;
    padding-bottom: 10px;
}

.member {
    flex: 0 0 auto;
    scroll-snap-align: start;
    background: #fff;
    margin: 0 10px;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    min-width: 100px;
    max-width: 120px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
    cursor: pointer;
}

.member:hover {
    transform: translateY(-5px);
}

.avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
}

.no-image-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background-color: #ccc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2em;
    color: #fff;
}

.no-image-avatar-modal {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background-color: #ccc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3em;
    color: #fff;
    margin-bottom: 15px;
}

.name {
    font-weight: bold;
    margin-top: 10px;
    font-size: 1em;
}

.role {
    color: #777;
    margin-bottom: 10px;
    font-size: 0.9em;
}

.relationship-line {
    width: 80%;
    height: 2px;
    background-color: #ccc;
    margin: 10px 0;
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
    padding: 20px 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
    position: relative;
    text-align: center;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-avatar {
    width: 100px;
    height: 100px;
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
    font-size: 0.9em;
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

.save-button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 15px;
    font-size: 1em;
}

.save-button:hover {
    background-color: #0056b3;
}

.modal-content form {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.modal-content form input,
.modal-content form textarea,
.modal-content form select {
    width: 100%;
    padding: 8px 10px;
    margin: 5px 0;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 1em;
}

.modal-content form textarea {
    resize: vertical;
    min-height: 60px;
}

.modal-content ul {
    list-style-type: none;
    padding: 0;
    text-align: left;
}

.modal-content li {
    margin-bottom: 5px;
}

.modal-content a {
    color: #0066cc;
    text-decoration: none;
}

.modal-content a:hover {
    text-decoration: underline;
}

@media screen and (max-width: 768px) {
    .member {
        min-width: 90px;
        max-width: 100px;
    }
    .avatar,
    .no-image-avatar {
        width: 70px;
        height: 70px;
    }
}

@media screen and (max-width: 480px) {
    .member {
        min-width: 80px;
        max-width: 90px;
        margin: 0 5px;
    }
    .avatar,
    .no-image-avatar {
        width: 60px;
        height: 60px;
    }
    .modal-content {
        padding: 15px 20px;
    }
    .modal-avatar,
    .no-image-avatar-modal {
        width: 80px;
        height: 80px;
    }
    .name, .role {
        font-size: 0.9em;
    }
}

@media screen and (max-width: 360px) {
    .member {
        min-width: 70px;
        max-width: 80px;
        margin: 0 3px;
    }
    .avatar,
    .no-image-avatar {
        width: 50px;
        height: 50px;
    }
    .modal-avatar,
    .no-image-avatar-modal {
        width: 70px;
        height: 70px;
    }
    .name, .role {
        font-size: 0.8em;
    }
}
</style>
