@extends('layouts.admin')

@section('style')
    <style>
        /* Стиль для селекта */
        .custom-select-status {
            color: #fff;  /* Текст белый */
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            border: 1px solid #ccc;
        }

        /* Для кастомных опций с округлыми углами */
        .custom-select-status option {
            border-radius: 20px;
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('callback.update', $callback->id) }}" method="post">
            @csrf
            @method('patch')

            <div class="mb-3 col-12 col-md-6 col-lg-3 ps-0">
                <label for="status">Статус</label>
                <select name="status" id="status" class="form-control custom-select-status">
                    @foreach ($statuses as $status)
                        <option
                            value="{{ $status->id }}"
                            data-color="{{ $status->color }}"
                            class="rounded-pill"
                            style="background-color: {{ $status->color }}; color: #fff;"
                            {{ old('status', $callback->status_id) == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="full_name">ФИО</label>
                <input type="text" name="full_name" class="form-control" id="full_name" placeholder="Введите название" value="{{ old('full_name', $callback->full_name) }}" required readonly>
            </div>

            <div class="mb-3">
                <label for="phone">Телефон</label>
                <input type="text" name="phone" class="form-control" id="phone" placeholder="Введите телефон" value="{{ old('phone', $callback->phone) }}" required readonly>
            </div>

            <div class="mb-3">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Введите email" value="{{ old('email', $callback->email) }}" required readonly>
            </div>

            <div class="mb-3">
                <label for="comment">Комментарий</label>
                <textarea name="comment" class="form-control" id="comment" rows="4" placeholder="Введите комментарий" readonly>{{ old('comment', $callback->comment) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="note">Примечание администратора</label>
                <textarea name="note" class="form-control" id="note" rows="4" placeholder="Введите примечание администратора">{{ old('note', $callback->note) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusSelect = document.getElementById('status');
            const statusOptions = statusSelect.options;

            // Функция для обновления цвета фона
            function updateSelectColor() {
                const selectedOption = statusOptions[statusSelect.selectedIndex];
                const color = selectedOption.getAttribute('data-color');

                // Применяем стили к контейнеру селекта
                statusSelect.style.backgroundColor = color;
                statusSelect.style.color = '#fff';  // Белый цвет текста
            }

            // Обновляем цвет фона селекта при изменении выбора
            statusSelect.addEventListener('change', updateSelectColor);

            // Изначальная установка цвета для уже выбранного статуса
            updateSelectColor();  // Вызываем функцию для начальной установки цвета
        });
    </script>
@endsection
