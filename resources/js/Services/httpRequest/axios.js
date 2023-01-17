import axios from 'axios';

const Axios = axios.create({
    baseURL: 'http://localhost:8000/'
    // baseURL: 'https://panel.polexofficial.com/'
});

export default Axios;