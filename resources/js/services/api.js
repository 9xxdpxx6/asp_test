const API_BASE_URL = (import.meta.env.VITE_API_BASE_URL || '/api').replace(/\/+$/, '');

const PREFIXES = {
    guest: '/guest',
};

export const API_ENDPOINTS = {
    hero: `${API_BASE_URL}${PREFIXES.guest}/hero`,
    advantages: `${API_BASE_URL}${PREFIXES.guest}/advantages`,
    aboutPage: `${API_BASE_URL}${PREFIXES.guest}/about`,
    contactsPage: `${API_BASE_URL}${PREFIXES.guest}/contacts`,
    whyChooseUs: `${API_BASE_URL}${PREFIXES.guest}/why-choose-us`,
    learningProcess: `${API_BASE_URL}${PREFIXES.guest}/learning-process`,
    callbackSection: `${API_BASE_URL}${PREFIXES.guest}/callback-section`,
    footer: `${API_BASE_URL}${PREFIXES.guest}/footer`,
    categories: `${API_BASE_URL}${PREFIXES.guest}/categories`,
    categoriesFeatured: `${API_BASE_URL}${PREFIXES.guest}/categories/featured`,
    categoryDetails: (id) => `${API_BASE_URL}${PREFIXES.guest}/categories/${id}`,
    posts: `${API_BASE_URL}${PREFIXES.guest}/posts`,
    postDetails: (slug) => `${API_BASE_URL}${PREFIXES.guest}/posts/${slug}`,
    callbackRequests: `${API_BASE_URL}${PREFIXES.guest}/callback-requests`,
    discounts: `${API_BASE_URL}${PREFIXES.guest}/discounts`,
    discountsHome: `${API_BASE_URL}${PREFIXES.guest}/discounts/home`,
    reviewWidgetsHome: `${API_BASE_URL}${PREFIXES.guest}/review-widgets/home`,
    discountDetails: (id) => `${API_BASE_URL}${PREFIXES.guest}/discounts/${id}`,
};

export const getPrefixedUrl = (prefix, endpoint) => `${API_BASE_URL}${prefix}${endpoint}`;

export default API_ENDPOINTS;
