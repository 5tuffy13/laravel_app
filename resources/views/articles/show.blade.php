@extends('layouts.app')

@section('title', $article->title)

@section('content')
<div class="card" style="max-width: 840px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <a href="{{ route('articles.index') }}" style="color: var(--primary); text-decoration: none; font-size: 0.9rem;">← Каталог статей</a>
        <div style="display: flex; gap: 0.5rem;">
            @if(!$article->trashed())
                <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-secondary" style="text-decoration: none;">Редактировать</a>
                <form action="{{ route('articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Поместить в архив?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="padding: 0.5rem 1rem; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer;">Удалить (Soft Delete)</button>
                </form>
            @else
                <form action="{{ route('articles.restore', $article->id) }}" method="POST">
                    @csrf
                    <button type="submit" style="padding: 0.5rem 1rem; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 6px; cursor: pointer;">Восстановить из архива</button>
                </form>
            @endif
        </div>
    </div>

    @if($article->trashed())
        <div class="alert" style="background: #fee2e2; color: #991b1b;">
            ⚠️ Эта статья находится в архиве (Soft Delete: {{ $article->deleted_at->format('d.m.Y H:i') }}).
        </div>
    @endif

    <div style="margin-bottom: 1rem;">
        <span style="background: #e0e7ff; color: #3730a3; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
            Категория (BelongsTo): {{ $article->category->name }}
        </span>
        @if(!$article->is_published)
            <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.85rem;">Черновик</span>
        @endif
    </div>

    <h1 style="font-size: 2rem; margin-bottom: 0.75rem;">{{ $article->title }}</h1>

    <div style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <span>👤 Автор: <strong>{{ $article->author_name }}</strong></span>
        <span>📅 Создано: {{ $article->created_at->format('d.m.Y H:i') }}</span>
        <span>👁 Просмотров: {{ $article->views_count }}</span>
    </div>

    <div style="line-height: 1.8; font-size: 1.05rem; margin-bottom: 2rem; white-space: pre-line;">
        {{ $article->content }}
    </div>

    <!-- Связь BelongsToMany: Теги -->
    <div style="border-top: 1px solid var(--border); padding-top: 1.25rem; margin-bottom: 2rem;">
        <h4 style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 0.5rem;">Связь BelongsToMany (Теги):</h4>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            @forelse($article->tags as $tag)
                <span style="background: #f1f5f9; color: #334155; padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.85rem;">
                    #{{ $tag->name }}
                </span>
            @empty
                <span style="color: var(--text-muted); font-size: 0.9rem;">Теги не привязаны</span>
            @endforelse
        </div>
    </div>

    <!-- Связь MorphMany: Полиморфные комментарии -->
    <div style="border-top: 1px solid var(--border); padding-top: 1.5rem;">
        <h3 style="margin-bottom: 1rem;">Полиморфные комментарии (MorphMany, {{ $article->comments->count() }})</h3>

        <div style="display: grid; gap: 1rem; margin-bottom: 2rem;">
            @forelse($article->comments as $comment)
                <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 8px; padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                        <strong>{{ $comment->author_name }}</strong>
                        <span style="color: var(--text-muted); font-size: 0.8rem;">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <p style="font-size: 0.95rem; color: var(--text);">{{ $comment->body }}</p>
                </div>
            @empty
                <p style="color: var(--text-muted); font-size: 0.95rem;">Комментариев пока нет. Будьте первым!</p>
            @endforelse
        </div>

        @if(!$article->trashed())
            <div style="background: #f1f5f9; padding: 1.25rem; border-radius: 8px;">
                <h4 style="margin-bottom: 0.75rem;">Оставить комментарий</h4>
                <form action="{{ route('articles.comments.store', $article->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="author_name" class="form-label">Ваше имя <span style="color: var(--error-text);">*</span></label>
                        <input type="text" name="author_name" id="author_name" class="form-control" required placeholder="Имя">
                    </div>
                    <div class="form-group">
                        <label for="body" class="form-label">Комментарий <span style="color: var(--error-text);">*</span></label>
                        <textarea name="body" id="body" rows="3" class="form-control" required placeholder="Ваш отзыв..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить комментарий</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
