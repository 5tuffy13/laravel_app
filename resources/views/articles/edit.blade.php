@extends('layouts.app')

@section('title', 'Редактировать статью: ' . $article->title)

@section('content')
<div class="card" style="max-width: 720px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('articles.show', $article->id) }}" style="color: var(--primary); text-decoration: none; font-size: 0.9rem;">← Назад к статье</a>
        <h1 style="margin-top: 0.5rem;">Редактирование статьи</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left: 1.25rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('articles.update', $article->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="category_id" class="form-label">Категория <span style="color: var(--error-text);">*</span></label>
            <select name="category_id" id="category_id" class="form-control" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="title" class="form-label">Заголовок статьи <span style="color: var(--error-text);">*</span></label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $article->title) }}" required>
        </div>

        <div class="form-group">
            <label for="author_name" class="form-label">Имя автора <span style="color: var(--error-text);">*</span></label>
            <input type="text" name="author_name" id="author_name" class="form-control" value="{{ old('author_name', $article->author_name) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Теги</label>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; padding: 0.5rem 0;">
                @php $activeTags = $article->tags->pluck('id')->toArray(); @endphp
                @foreach($tags as $tag)
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.9rem; cursor: pointer;">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $activeTags)) ? 'checked' : '' }}>
                        #{{ $tag->name }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label for="content" class="form-label">Текст статьи <span style="color: var(--error-text);">*</span></label>
            <textarea name="content" id="content" rows="6" class="form-control" required>{{ old('content', $article->content) }}</textarea>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $article->is_published) ? 'checked' : '' }}>
                <span>Опубликовано</span>
            </label>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('articles.show', $article->id) }}" class="btn btn-secondary" style="text-decoration: none;">Отмена</a>
        </div>
    </form>
</div>
@endsection
