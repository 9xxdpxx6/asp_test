<?php
echo "PHP is working!<br>";

// Проверяем, есть ли файл laravel-test.php
if (file_exists(__DIR__ . '/laravel-test.php')) {
    echo "laravel-test.php exists!<br>";
} else {
    echo "laravel-test.php NOT found!<br>";
}

// Проверяем, есть ли файл routes/web.php
if (file_exists(__DIR__ . '/../routes/web.php')) {
    echo "routes/web.php exists!<br>";
} else {
    echo "routes/web.php NOT found!<br>";
}

// Проверяем, есть ли папка app
if (is_dir(__DIR__ . '/../app')) {
    echo "app directory exists!<br>";
} else {
    echo "app directory NOT found!<br>";
}

// Проверяем, есть ли файл DevController
if (file_exists(__DIR__ . '/../app/Http/Controllers/DevController.php')) {
    echo "DevController.php exists!<br>";
} else {
    echo "DevController.php NOT found!<br>";
}

phpinfo();
