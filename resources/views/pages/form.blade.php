@extends('layouts.app')

@section('title', 'Отправка формы — LaravelApp')

@section('content')
<div class="card" style="max-width: 650px; margin: 0 auto;">
    <h2>Форма отправки заявки</h2>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
        Заполните поля формы. Данные будут проверены валидатором Laravel и сохранены в уникальный JSON-файл в контроллере.
    </p>

    @if (session('success'))
        <div class="alert alert-success">
            <strong>✓ Успешно:</strong> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ошибки валидации:</strong>
            <ul style="margin-left: 1.25rem; margin-top: 0.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('form.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Имя отправителя *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Например: Александр" required>
        </div>

        <div class="form-group">
            <label for="email">Контактный Email *</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="alexander@example.com" required>
        </div>

        <div class="form-group">
            <label for="subject">Тема обращения *</label>
            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Краткая тема" required>
        </div>

        <div class="form-group">
            <label for="message">Текст сообщения *</label>
            <textarea id="message" name="message" rows="4" placeholder="Введите подробный текст..." required>{{ old('message') }}</textarea>
        </div>

        <button type="submit" class="btn">Отправить данные (POST)</button>
    </form>
</div>
@endsection
