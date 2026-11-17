@extends('layouts.app')

@section('title', 'Главная — LaravelApp')

@section('content')
<div class="card">
    <h1>Веб-приложение на Laravel (Работа с данными)</h1>
    <p style="margin-bottom: 1.5rem; color: var(--text-muted);">
        Лабораторное приложение по дисциплине «Технологии создания и отладки сценариев интерактивного контента».
        Реализована работа с базой данных через Eloquent ORM, связи моделей, Query Scopes, SoftDeletes и REST API.
    </p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
        <div style="border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; background: #fafafa;">
            <h3>📚 Каталог статей (БД)</h3>
            <p style="color: var(--text-muted); margin-bottom: 1rem;">
                Модели Eloquent, связи <code>BelongsTo</code>, <code>HasMany</code>, <code>BelongsToMany</code>, полиморфные комментарии <code>MorphMany</code>, локальные Query Scopes и мягкое удаление.
            </p>
            <a href="{{ route('articles.index') }}" class="btn btn-primary" style="text-decoration: none;">Открыть каталог</a>
        </div>

        <div style="border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; background: #fafafa;">
            <h3>⚡ REST API (JSON)</h3>
            <p style="color: var(--text-muted); margin-bottom: 1rem;">
                Контроллеры API с трансформацией ответа через <code>ArticleResource</code>. Поддержка пагинации и вложенных ресурсов.
            </p>
            <a href="/api/articles" target="_blank" class="btn btn-secondary" style="text-decoration: none;">Открыть JSON API</a>
        </div>

        <div style="border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; background: #fafafa;">
            <h3>📝 Отправка формы (JSON)</h3>
            <p style="color: var(--text-muted); margin-bottom: 1rem;">
                Форма с POST-запросом, серверной валидацией и сохранением результата в уникальный JSON-файл на сервере.
            </p>
            <a href="{{ route('form.show') }}" class="btn btn-secondary" style="text-decoration: none;">Перейти к форме</a>
        </div>

        <div style="border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; background: #fafafa;">
            <h3>📊 Таблица записей (JSON)</h3>
            <p style="color: var(--text-muted); margin-bottom: 1rem;">
                Просмотр сохраненных JSON-файлов в виде структурированной таблицы.
            </p>
            <a href="{{ route('records.index') }}" class="btn btn-secondary" style="text-decoration: none;">Смотреть записи</a>
        </div>
    </div>
</div>
@endsection
