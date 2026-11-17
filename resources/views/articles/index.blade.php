@extends('layouts.app')

@section('title', 'Каталог статей — Eloquent ORM')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1>Каталог статей</h1>
            <p style="color: var(--text-muted);">Демонстрация моделей Eloquent, связей BelongsTo/HasMany/BelongsToMany, Query Scopes и SoftDeletes.</p>
        </div>
        <a href="{{ route('articles.create') }}" class="btn btn-primary">+ Добавить статью</a>
    </div>

    <!-- Фильтры и поиск (Query Scopes) -->
    <form method="GET" action="{{ route('articles.index') }}" style="display: flex; gap: 0.75rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ $search }}" placeholder="Поиск по заголовку, автору или тексту..." class="form-control" style="flex: 1; min-width: 240px;">

        <select name="status" class="form-control" style="width: auto;">
            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Все записи</option>
            <option value="published" {{ $status === 'published' ? 'selected' : '' }}>Только опубликованные (scopePublished)</option>
            <option value="popular" {{ $status === 'popular' ? 'selected' : '' }}>Популярные (scopePopular, 10+ просм.)</option>
            <option value="trashed" {{ $status === 'trashed' ? 'selected' : '' }}>Архив (только удаленные SoftDeletes)</option>
        </select>

        <button type="submit" class="btn btn-primary">Применить</button>
        @if(!empty($search) || $status !== 'all')
            <a href="{{ route('articles.index') }}" class="btn btn-secondary" style="text-decoration: none;">Сбросить</a>
        @endif
    </form>

    @if($articles->isEmpty())
        <div style="text-align: center; padding: 3rem 0; color: var(--text-muted);">
            <p style="font-size: 1.1rem;">Статьи не найдены по заданным критериям.</p>
        </div>
    @else
        <div style="display: grid; gap: 1.25rem;">
            @foreach($articles as $article)
                <div style="border: 1px solid var(--border); border-radius: 8px; padding: 1.25rem; background: {{ $article->trashed() ? '#fef2f2' : '#ffffff' }}; display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <span style="background: #e0e7ff; color: #3730a3; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                {{ $article->category->name }}
                            </span>
                            @if(!$article->is_published)
                                <span style="background: #fef3c7; color: #92400e; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem;">Черновик</span>
                            @endif
                            @if($article->trashed())
                                <span style="background: #fee2e2; color: #991b1b; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">В архиве (SoftDeleted)</span>
                            @endif
                            <span style="color: var(--text-muted); font-size: 0.85rem;">Автор: {{ $article->author_name }} · {{ $article->created_at->format('d.m.Y H:i') }}</span>
                        </div>

                        <h3 style="margin-bottom: 0.5rem;">
                            <a href="{{ route('articles.show', $article->id) }}" style="color: var(--text); text-decoration: none;">
                                {{ $article->title }}
                            </a>
                        </h3>

                        <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 0.75rem;">
                            {{ Str::limit($article->content, 180) }}
                        </p>

                        <!-- Теги (BelongsToMany) -->
                        <div style="display: flex; gap: 0.4rem; align-items: center; flex-wrap: wrap;">
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Теги:</span>
                            @forelse($article->tags as $tag)
                                <span style="background: #f1f5f9; color: #475569; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">#{{ $tag->name }}</span>
                            @empty
                                <span style="font-size: 0.8rem; color: var(--text-muted);">—</span>
                            @endforelse
                            <span style="font-size: 0.8rem; color: var(--text-muted); margin-left: 0.5rem;">💬 Комментариев: {{ $article->comments_count }}</span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">👁 Просмотров: {{ $article->views_count }}</span>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem; min-width: 120px;">
                        <a href="{{ route('articles.show', $article->id) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; text-align: center; text-decoration: none;">Просмотр</a>
                        @if(!$article->trashed())
                            <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; text-align: center; text-decoration: none;">Редактировать</a>
                            <form action="{{ route('articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Поместить статью в архив (Soft Delete)?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="width: 100%; padding: 0.4rem 0.8rem; font-size: 0.85rem; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer;">В архив</button>
                            </form>
                        @else
                            <form action="{{ route('articles.restore', $article->id) }}" method="POST">
                                @csrf
                                <button type="submit" style="width: 100%; padding: 0.4rem 0.8rem; font-size: 0.85rem; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 6px; cursor: pointer;">Восстановить</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $articles->links() }}
        </div>
    @endif
</div>
@endsection
