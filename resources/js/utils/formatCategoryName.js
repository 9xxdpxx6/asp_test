const CATEGORY_MARKER_REGEX = /(^|[^A-Za-z0-9])(B\+E|B1E|B96|BE|AB|A2|A1|B1|A|B)(?=$|[^A-Za-z0-9])/gi;

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

export function formatCategoryName(value) {
    const escaped = escapeHtml(value);
    return escaped.replace(CATEGORY_MARKER_REGEX, (_, prefix, marker) => {
        return `${prefix}<strong class="category-marker">${marker}</strong>`;
    });
}

