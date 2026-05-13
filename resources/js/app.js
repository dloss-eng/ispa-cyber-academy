import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Sidebar toggle for mobile
window.toggleSidebar = function() {
    document.querySelector('.sidebar')?.classList.toggle('open');
};
window.closeSidebar = function() {
    document.querySelector('.sidebar')?.classList.remove('open');
};
