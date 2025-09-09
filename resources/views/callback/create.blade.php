@extends('layouts.admin')

@section('title', 'Создание заявки')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Создание заявки</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-8">
                    <form action="{{ route('callback.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="full_name">ФИО</label>
                            <input type="text" name="full_name" id="full_name"
                                   class="form-control @error('full_name') is-invalid @enderror" placeholder="Введите ФИО"
                                   value="{{ old('full_name') }}">
                            @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="text" name="phone" id="phone"
                                   class="form-control @error('phone') is-invalid @enderror" placeholder="Введите телефон"
                                   value="{{ old('phone') }}">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror" placeholder="Введите email"
                                   value="{{ old('email') }}">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="comment">Комментарий</label>
                            <textarea name="comment" id="comment" rows="4"
                                      class="form-control @error('comment') is-invalid @enderror" placeholder="Введите комментарий">{{ old('comment') }}</textarea>
                            @error('comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Создать заявку">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection

