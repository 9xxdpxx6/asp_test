const API_BASE_URL = 'http://localhost:8000/api';

const PREFIXES = {
    guest: '/guest',
};

export const API_ENDPOINTS = {
    categories: `${API_BASE_URL}${PREFIXES.guest}/categories`,
    categoryDetails: (id) => `${API_BASE_URL}${PREFIXES.guest}/categories/${id}`,
    posts: `${API_BASE_URL}${PREFIXES.guest}/posts`,
    postDetails: (id) => `${API_BASE_URL}${PREFIXES.guest}/posts/${id}`,
    callbackRequests: `${API_BASE_URL}${PREFIXES.guest}/callback-requests`,
    discounts: `${API_BASE_URL}${PREFIXES.guest}/discounts`,
    discountDetails: (id) => `${API_BASE_URL}${PREFIXES.guest}/discounts/${id}`,
};

export const getPrefixedUrl = (prefix, endpoint) => `${API_BASE_URL}${prefix}${endpoint}`;

export default API_ENDPOINTS;

