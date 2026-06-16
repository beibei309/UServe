import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

window.L = L;
window.dispatchEvent(new CustomEvent('upsi2u:map-tools-ready'));
