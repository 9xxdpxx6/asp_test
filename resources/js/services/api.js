const API_BASE_URL = (import.meta.env.VITE_API_BASE_URL || '/api').replace(/\/+$/, '');

const PREFIXES = {
    guest: '/guest',
};

export const API_ENDPOINTS = {
    categories: `${API_BASE_URL}${PREFIXES.guest}/categories`,
    categoriesFeatured: `${API_BASE_URL}${PREFIXES.guest}/categories/featured`,
    categoryDetails: (id) => `${API_BASE_URL}${PREFIXES.guest}/categories/${id}`,
    posts: `${API_BASE_URL}${PREFIXES.guest}/posts`,
    postDetails: (slug) => `${API_BASE_URL}${PREFIXES.guest}/posts/${slug}`,
    callbackRequests: `${API_BASE_URL}${PREFIXES.guest}/callback-requests`,
    discounts: `${API_BASE_URL}${PREFIXES.guest}/discounts`,
    discountDetails: (id) => `${API_BASE_URL}${PREFIXES.guest}/discounts/${id}`,
};

export const getPrefixedUrl = (prefix, endpoint) => `${API_BASE_URL}${prefix}${endpoint}`;

export default API_ENDPOINTS;
