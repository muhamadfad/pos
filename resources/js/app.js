import "./bootstrap";
import * as Turbo from "@hotwired/turbo";
Turbo.start();

import ApexCharts from "apexcharts";
window.ApexCharts = ApexCharts;

import Alpine from "alpinejs";
window.Alpine = Alpine;

// Alpine harus di-start setelah Turbo agar berjalan mulus bersama Turbo Drive
document.addEventListener("turbo:load", () => {
    // Re-initialize Iconify icons setelah navigasi Turbo
    if (window.Iconify) {
        window.Iconify.scan();
    }
});

Alpine.start();
