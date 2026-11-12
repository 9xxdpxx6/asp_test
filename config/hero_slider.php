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
    'max_video_mb' => (int) env('HERO_VIDEO_MAX_MB', 30),

    // Короткий немой «превью-ролик» для автозапуска в шапке, МБ
    'max_preview_mb' => (int) env('HERO_VIDEO_MAX_PREVIEW_MB', 6),

    // Обложка (первый кадр), КБ
    'max_poster_kb' => (int) env('HERO_VIDEO_MAX_POSTER_KB', 2048),

    // Через сколько часов удаляются загруженные, но не сохранённые в слайд файлы
    'tmp_ttl_hours' => (int) env('HERO_VIDEO_TMP_TTL_HOURS', 24),

    // Сколько загруженных, но не сохранённых в слайд файлов держим одновременно (старые удаляются первыми)
    'max_tmp_uploads' => (int) env('HERO_VIDEO_MAX_TMP_UPLOADS', 12),

    // Пределы самого ролика. Сервер читает их из присланного MP4, а не верит браузеру.
    'max_dimension' => (int) env('HERO_VIDEO_MAX_DIMENSION', 1920),            // длинная сторона кадра, px
    'max_duration_seconds' => (int) env('HERO_VIDEO_MAX_DURATION', 900),       // 15 минут
    'max_bitrate_kbps' => (int) env('HERO_VIDEO_MAX_BITRATE_KBPS', 4500),      // средний битрейт файла
    'max_preview_seconds' => (int) env('HERO_VIDEO_MAX_PREVIEW_SECONDS', 20),
    'max_preview_dimension' => (int) env('HERO_VIDEO_MAX_PREVIEW_DIMENSION', 960),
    'max_poster_dimension' => (int) env('HERO_VIDEO_MAX_POSTER_DIMENSION', 2000),
];
