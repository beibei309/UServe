import Chart from 'chart.js/auto';

window.Chart = Chart;
window.dispatchEvent(new CustomEvent('upsi2u:chart-tools-ready'));
