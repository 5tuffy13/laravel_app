@extends('layouts.app')

@section('title', 'Главная — LaravelApp')

@section('content')
<div class="card">
    <h1>Основа веб-приложения на Laravel</h1>
    <p style="margin-bottom: 1.5rem; color: var(--text-muted);">
        Демонстрационное приложение, разработанное в рамках лабораторной работы по дисциплине 
        «Технологии создания и отладки сценариев интерактивного контента».
    </p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
        <div style="border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; background: #fafafa;">
            <h3>📝 Отправка формы</h3>
            <p style="color: var(--text-muted); margin-bottom: 1rem;">
                Форма с POST-запросом, серверной валидацией и сохранением результата в уникальный JSON-файл на сервере.
            </p>
            <a href="{{ route('form.show') }}" class="btn">Перейти к форме</a>
        </div>

        <div style="border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; background: #fafafa;">
            <h3>📊 Таблица записей</h3>
            <p style="color: var(--text-muted); margin-bottom: 1rem;">
                Просмотр всех сохраненных JSON-файлов в виде структурированной таблицы.
            </p>
            <a href="{{ route('records.index') }}" class="btn" style="background: #475569;">Смотреть записи</a>
        </div>
    </div>
</div>
@endsection
