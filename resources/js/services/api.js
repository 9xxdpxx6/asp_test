// const API_BASE_URL = 'http://127.0.0.1:8000/api';
const API_BASE_URL = 'https://auto.kubstu.ru/api';

const PREFIXES = {
    guest: '/guest',
};

export const API_ENDPOINTS = {
    categories: `${API_BASE_URL}${PREFIXES.guest}/categories`,
    categoryDetails: (id) => `${API_BASE_URL}${PREFIXES.guest}/categories/${id}`,
    posts: `${API_BASE_URL}${PREFIXES.guest}/posts`,
    postDetails: (slug) => `${API_BASE_URL}${PREFIXES.guest}/posts/${slug}`,
    callbackRequests: `${API_BASE_URL}${PREFIXES.guest}/callback-requests`,
    discounts: `${API_BASE_URL}${PREFIXES.guest}/discounts`,
    discountDetails: (id) => `${API_BASE_URL}${PREFIXES.guest}/discounts/${id}`,
};

export const getPrefixedUrl = (prefix, endpoint) => `${API_BASE_URL}${prefix}${endpoint}`;

export default API_ENDPOINTS;

