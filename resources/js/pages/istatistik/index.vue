<template>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="container">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body p-3">
                    <h3 class="mb-3 text-center">Sınav Sonuçları ve Grafikleri</h3>
                    <!-- Grafikler -->
                    <div class="row">
                        <!-- Bar Grafik -->
                        <div class="col-12 col-md-6 mb-3">
                            <div class="p-2 bg-light rounded">
                                <h5 class="text-center">SQL Dersi Sonuçları (Bar Grafik)</h5>
                                <canvas id="barChart" class="chart"></canvas>
                            </div>
                        </div>
                        <!-- Pie Grafik -->
                        <div class="col-12 col-md-6 mb-3">
                            <div class="p-2 bg-light rounded">
                                <h5 class="text-center">Java Dersi Başarı Oranı (Pie Grafik)</h5>
                                <canvas id="pieChart" class="chart"></canvas>
                            </div>
                        </div>
                        <!-- Line Grafik -->
                        <div class="col-12 mb-3">
                            <div class="p-2 bg-light rounded">
                                <h5 class="text-center">Aylık Sınav Başarı Artışı (Line Grafik)</h5>
                                <canvas id="lineChart" class="chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
import { nextTick } from 'vue';

export default {
    mounted() {
        // Vue.nextTick ile grafikleri oluşturmak
        nextTick(() => {
            // Chart.js bileşenlerini kaydetme
            Chart.register(...registerables);

            // Bar Grafik
            const barCtx = document.getElementById('barChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs'],
                    datasets: [
                        {
                            label: 'SQL Dersi Sonuçları (puan)',
                            data: [85, 90, 75, 88, 95],
                            backgroundColor: ['#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe', '#dbeafe'],
                            borderColor: '#3b82f6',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true
                }
            });

            // Pie Grafik
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['Başarılı', 'Başarısız'],
                    datasets: [
                        {
                            label: 'Java Dersi Başarı Oranı',
                            data: [75, 25],
                            backgroundColor: ['#3b82f6', '#f87171'],
                            borderColor: '#fff',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true
                }
            });

            // Line Grafik
            const lineCtx = document.getElementById('lineChart').getContext('2d');
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs'],
                    datasets: [
                        {
                            label: 'Başarı Artışı (%)',
                            data: [60, 65, 70, 75, 80],
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            borderColor: '#3b82f6',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true
                }
            });
        });
    }
};
</script>

<style scoped>
/* Grafik sayfası için şık ve mobil uyumlu stil */
.card {
    margin-top: 2px;
    background-color: #fff;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h3 {
    font-size: 1.4rem;
    color: #333;
}

canvas.chart {
    max-height: 100px;  /* Maksimum yüksekliği küçülterek grafiklerin daha kompakt görünmesini sağla */
    width: 100%;
}

h5 {
    font-size: 1.1rem;
    color: #333;
    margin-bottom: 10px;
}

/* Mobil görünüm için optimize edilmiş stil */
@media (max-width: 576px) {
    .card {
        margin: 0 5px;
    }

    h3 {
        font-size: 1.2rem;
    }

    h5 {
        font-size: 1rem;
    }

    canvas.chart {
        height: 120px;  /* Mobil cihazlarda grafik boyutunu küçült */
    }
}
</style>
