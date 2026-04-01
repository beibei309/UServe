import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

window.Alpine = Alpine;
window.Swal = Swal;
window.flatpickr = flatpickr;

Alpine.start();
