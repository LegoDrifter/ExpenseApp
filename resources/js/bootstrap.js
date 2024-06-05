import axios from 'axios';
import store from "./Store/index.js";
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = null;
// if(store.getters.getToken){
//     token = store.getters.getToken
// }
if(localStorage.getItem('token')){
    token = localStorage.getItem('token')
}
//get token from localstorage and set below
window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
