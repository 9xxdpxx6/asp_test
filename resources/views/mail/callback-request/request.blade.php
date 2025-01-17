<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая заявка</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #2c3e50;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            text-align: center;
        }
        .content ul {
            list-style-type: none;
            padding: 0;
            margin: 20px 0;
            text-align: left;
            display: inline-block;
        }
        .content ul li {
            margin-bottom: 10px;
        }
        .content ul li strong {
            color: #2c3e50;
        }
        .button-container {
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 20px;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
            padding: 10px;
            background-color: #ecf0f1;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Заголовок -->
    <div class="header">
        <h1>Новая заявка на обратный звонок</h1>
    </div>

    <!-- Контент -->
    <div class="content">
        <p>Вам поступила новая заявка от клиента:</p>
        <ul>
            <li><strong>ФИО:</strong> {{ $full_name }}</li>
            <li><strong>Телефон:</strong> {{ $phone }}</li>
            <li><strong>E-mail:</strong> {{ $email }}</li>
            @if($comment)
                <li><strong>Комментарий:</strong> {{ $comment }}</li>
            @endif
        </ul>
        <div class="button-container">
            <a href="https://auto.kubstu.ru/admin/callback-requests/{{ $id }}" class="button">
                Посмотреть в админке
            </a>
        </div>
    </div>

    <!-- Подвал -->
{{--    <div class="footer">--}}
{{--        Спасибо,<br>--}}
{{--        {{ config('app.name') }}--}}
{{--    </div>--}}
</div>
</body>
</html>
