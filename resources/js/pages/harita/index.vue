<template>

    <h1 style="text-align: center">MAP APİ SAYFASI TEST</h1>
    <div class="container" style="margin-top: 0;">
        <div class="text-center mb-5">
            <h2 class="display-4 text-primary">Harita ve İşaretleyiciler</h2>
            <p class="lead text-muted">Harita üzerinde dinamik olarak işaretleyiciler ekleyin ve belirli yerleri keşfedin. Daha fazla işaretleyici eklemek için haritaya tıklayın!</p>
        </div>
        <div id="map" class="map-container" style="width: 100%; height: 500px;"></div>
        <div class="text-center mt-5">
            <h4 class="text-info">Yeni Yerler Keşfet!</h4>
            <p class="text-dark">Harita üzerinde istediğiniz noktalara tıklayarak dinamik işaretleyiciler ekleyin ve bu yerleri keşfedin.</p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'GoogleMap',
    data() {
        return {
            map: null,
            markers: []
        };
    },
    mounted() {
        this.loadGoogleMaps();
    },
    methods: {
        loadGoogleMaps() {
            if (typeof google !== 'undefined' && google.maps) {
                this.initMap();
            } else {
                const script = document.createElement('script');
                script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCHw4XdL7tgPN2LVm2XTLZJUprNmvczPtI&libraries=places";
                script.async = true;
                script.defer = true;
                script.onload = () => {
                    this.initMap();
                };
                document.head.appendChild(script);
            }
        },
        initMap() {
            // Haritayı başlat
            this.map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 41.0082, lng: 28.9784 }, // İstanbul koordinatları
                zoom: 12
            });

            // Haritaya tıklanınca işaretleyici ekleyelim
            this.map.addListener('click', (event) => {
                this.addMarker(event.latLng);
            });
        },
        addMarker(location) {
            // Yeni işaretleyici oluştur
            const marker = new google.maps.Marker({
                position: location,
                map: this.map,
                draggable: true
            });

            // İşaretleyiciyi markers dizisine ekle
            this.markers.push(marker);
        },
        handleNavClick() {
            // Eğer bir fonksiyon bekleniyorsa bu fonksiyonu ekleyin
            console.log('Navigasyon tıklandı');
            // İlgili işlev tanımlı değilse aşağıdaki satırı ekleyin
            // this.closeRanking = () => { console.log('Ranking kapandı'); };
            if (typeof this.closeRanking === 'function') {
                this.closeRanking();
            }
        }
    }
};
</script>

<style scoped>
.container {
    max-width: 1200px;
    margin: 0 auto;
}

.map-container {
    width: 100%;
    height: 500px;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h2 {
    font-size: 2rem;
    color: #007bff;
}

p {
    font-size: 1rem;
    color: #555;
}

@media (max-width: 576px) {
    h2 {
        font-size: 1.8rem;
    }

    p {
        font-size: 0.9rem;
    }

    .map-container {
        height: 300px;
    }
}
</style>

<!-- Google Maps API Script'i public/index.html dosyasına eklemeniz gerekmez, yukarıda dinamik olarak yükleniyor -->
