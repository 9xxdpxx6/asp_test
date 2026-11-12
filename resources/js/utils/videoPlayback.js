/**
 * Можно ли «играть» короткие немые превью сами: нет, если посетитель просил меньше анимации
 * или включил экономию трафика. Тогда остаётся обложка, а видео запускается кликом.
 */
export function canAutoplayPreviews() {
    if (typeof window === 'undefined') {
        return false;
    }

    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return false;
    }

    const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    if (connection && (connection.saveData || /(^|-)2g$/.test(connection.effectiveType || ''))) {
        return false;
    }

    return true;
}

/**
 * Безопасный запуск/пауза: play() возвращает промис, который браузер может отклонить (режим экономии, фон вкладки).
 */
export function setPlaying(video, shouldPlay) {
    if (!video) {
        return;
    }

    if (shouldPlay) {
        video.muted = true;
        const result = video.play();
        if (result && typeof result.catch === 'function') {
            result.catch(() => {});
        }
    } else {
        video.pause();
    }
}
