import "./bootstrap";
import * as Turbo from "@hotwired/turbo";
Turbo.start();

import ApexCharts from "apexcharts";
window.ApexCharts = ApexCharts;

import Chart from "chart.js/auto";
window.Chart = Chart;

import Alpine from "alpinejs";
window.Alpine = Alpine;

// Fungsi untuk inisialisasi chart
function initCharts() {
    console.log('Initializing charts...');
    
    // Cek apakah ada chart di halaman
    const chartCanvas = document.getElementById('historicalChart');
    if (chartCanvas && typeof window.Chart !== 'undefined') {
        // Data dari blade
        const months = window.historicalData?.months ?? [];
        const incomeData = window.historicalData?.income ?? [];
        const expenseData = window.historicalData?.expense ?? [];
        
        if (months.length > 0) {
            // Destroy chart lama jika ada
            if (window.currentChart) {
                window.currentChart.destroy();
            }
            
            const ctx = chartCanvas.getContext('2d');
            window.currentChart = new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: incomeData,
                            backgroundColor: 'rgba(16, 185, 129, 0.6)',
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Pengeluaran',
                            data: expenseData,
                            backgroundColor: 'rgba(239, 68, 68, 0.6)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 1,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Jumlah (Rp)' },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1) + ' M';
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                                    if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + ' rb';
                                    return 'Rp ' + value;
                                }
                            }
                        },
                        x: {
                            title: { display: true, text: 'Bulan' },
                            ticks: { maxRotation: 45, minRotation: 45 }
                        }
                    }
                }
            });
        }
    }
}

// Inisialisasi saat Turbo load
document.addEventListener("turbo:load", () => {
    // Re-initialize Iconify icons
    if (window.Iconify) {
        window.Iconify.scan();
    }
    
    // Inisialisasi chart
    initCharts();
});

// Inisialisasi juga saat DOMContentLoaded (untuk halaman non-Turbo)
document.addEventListener("DOMContentLoaded", () => {
    initCharts();
});

Alpine.start();

// Export data ke window untuk akses dari JS
window.initCharts = initCharts;