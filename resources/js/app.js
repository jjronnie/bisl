import './bootstrap'
import './custom'
import Alpine from 'alpinejs'
import { createIcons, icons } from 'lucide'
import './custom.js';
import Chart from 'chart.js/auto';

window.Alpine = Alpine
Alpine.start()

window.Chart = Chart;

document.addEventListener('DOMContentLoaded', () => {
  createIcons({ icons })
})







