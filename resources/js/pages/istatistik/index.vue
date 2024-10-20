<template>
    <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh; margin: 0; padding: 0; position: fixed; top: 0; left: 0; right: 0;">
        <div class="container" style="margin: 0; padding: 0; width: 100%;">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body p-1">
                    <h3 class="mb-3 text-center">Sınav Sonuçları ve Grafikleri</h3>
                    <div class="row">
                        <div class="col-12 col-md-7 mb-7">
                            <div class="p-2 bg-light rounded">
                                <h5 class="text-center">SQL Dersi Sonuçları (Bar Grafik)</h5>
                                <canvas id="barChart" class="chart"></canvas>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="p-2 bg-light rounded">
                                <h5 class="text-center">Java Dersi Başarı Oranı (Pie Grafik)</h5>
                                <canvas id="pieChart" class="chart"></canvas>
                            </div>
                        </div>
                        <div class="col-12 mb-1">
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
import { Chart, registerables } from 'chart.js/auto';
import { nextTick } from 'vue';

export default {
    mounted() {
        nextTick(() => {
            Chart.register(...registerables);

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
.card {
    background-color: #fff;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 0 !important;
}

h3 {
    font-size: 1.4rem;
    color: #333;
}

canvas.chart {
    max-height: 100px;
    width: 100%;
}

h5 {
    font-size: 1.1rem;
    color: #333;
    margin-bottom: 10px;
}

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
        height: 120px;
    }
}
</style>
