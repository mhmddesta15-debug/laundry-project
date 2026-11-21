// Bootstrap frontend: menyiapkan Axios untuk request AJAX agar aplikasi dapat
// berkomunikasi dengan server tanpa reload halaman.
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
