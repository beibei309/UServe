import Quill from 'quill';
import 'quill/dist/quill.snow.css';

window.Quill = Quill;
window.dispatchEvent(new CustomEvent('upsi2u:editor-tools-ready'));
