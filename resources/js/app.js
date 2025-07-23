import Alpine from 'alpinejs';
import axios from 'axios';
import Swal from 'sweetalert2';
import toastr from 'toastr';

window.Alpine = Alpine;
window.axios = axios;
window.Swal = Swal;
window.toastr = toastr;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
