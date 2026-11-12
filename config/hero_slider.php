<?php

/**
 * Слайдер с видео в шапке главной: серверные пределы загрузки.
 * Видео сжимается в браузере админа до загрузки (public/adminpanel/js/video-compress.js),
 * эти значения защищают сервер, если в админку отправили что-то обходным путём.
 */
return [
    // Максимум слайдов с видео
    'max_slides' => (int) env('HERO_SLIDER_MAX_SLIDES', 6),

    // Максимальный размер сжатого ролика, МБ (должен проходить через upload_max_filesize и post_max_size)
    'max_video_mb' => (int) env('HERO_VIDEO_MAX_MB', 40),

    // Короткий немой «превью-ролик» для автозапуска в шапке, МБ
    'max_preview_mb' => (int) env('HERO_VIDEO_MAX_PREVIEW_MB', 6),

    // Обложка (первый кадр), КБ
    'max_poster_kb' => (int) env('HERO_VIDEO_MAX_POSTER_KB', 2048),

    // Через сколько часов удаляются загруженные, но не сохранённые в слайд файлы
    'tmp_ttl_hours' => (int) env('HERO_VIDEO_TMP_TTL_HOURS', 24),
];
