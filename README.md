# Laravel Web Application (v2.0 — Работа с данными)

Веб-приложение на фреймворке Laravel 11 с полноценной работой с реляционной базой данных через Eloquent ORM.

## Реализованная функциональность

### 1. Модели и миграции базы данных
- `Category` (`categories`) — категории материалов, связь `HasMany` со статьями, поддержка `SoftDeletes`.
- `Tag` (`tags`) — теги, связь `BelongsToMany` со статьями через промежуточную таблицу `article_tag`.
- `Article` (`articles`) — статьи блога (`title`, `slug`, `content`, `author_name`, `is_published`, `views_count`), поддержка `SoftDeletes`.
- `Comment` (`comments`) — комментарии через полиморфную связь `MorphTo` / `MorphMany` (`commentable_id`, `commentable_type`), поддержка `SoftDeletes`.

### 2. Параметры моделей ($fillable, $casts)
- `$fillable` настроен во всех моделях для безопасного массового заполнения.
- `$casts` настроен для булевых значений (`is_published => boolean`), счетчиков (`views_count => integer`) и временных меток (`created_at`, `updated_at`, `deleted_at => datetime`).

### 3. Связи между моделями
- **BelongsTo:** `Article -> category`
- **HasMany:** `Category -> articles`
- **BelongsToMany:** `Article <-> Tag` (многие-ко-многим с `withTimestamps()`)
- **MorphMany / MorphTo:** `Article -> comments` и `Category -> comments` (полиморфная привязка комментариев к разным сущностям)

### 4. Query Scopes
В модели `Article` реализованы локальные скоупы:
- `scopePublished($query)` — выборка только опубликованных статей (`is_published = true`).
- `scopePopular($query, $minViews = 10)` — фильтрация статей по количеству просмотров.
- `scopeSearch($query, $term)` — полнотекстовая фильтрация по названию, автору и содержанию.

### 5. Мягкое удаление (SoftDeletes)
- В таблицах `articles`, `categories` и `comments` добавлено поле `deleted_at`.
- Реализованы сценарии мягкого удаления статьи, фильтрация архива и восстановление записи (`$article->restore()`).

### 6. CRUD веб-контроллеры и представления
- `GET /articles` — каталог статей с поиском, фильтрами по скоупам, отображением категорий, тегов и счетчиков комментариев.
- `GET /articles/create` и `POST /articles` — создание статьи с привязкой категории и тегов.
- `GET /articles/{id}` — детальный просмотр статьи, авто-инкремент счетчика просмотров, список полиморфных комментариев и форма добавления нового комментария.
- `GET /articles/{id}/edit` и `PUT /articles/{id}` — редактирование статьи и обновление синхронизации тегов (`sync()`).
- `DELETE /articles/{id}` — мягкое удаление статьи.
- `POST /articles/{id}/restore` — восстановление статьи из архива.

### 7. REST API (API Resource)
- Контроллер `App\Http\Controllers\Api\ArticleApiController` (маршруты `/api/articles`).
- Ресурсы: `ArticleResource`, `CategoryResource`, `TagResource`, `CommentResource`.
- Возврат структурированного JSON с вложенными связями и мета-данными пагинатора.

### 8. Сохранение функциональности первого этапа
- Роуты формы `/form` и таблицы JSON-файлов `/records` сохранены и функционируют.

## Запуск и тестирование

1. Установка зависимостей и миграции:
   ```bash
   composer install
   php artisan migrate --seed
   ```

2. Запуск тестов (17 тестов, 120 проверок):
   ```bash
   php artisan test
   ```

3. Запуск локального веб-сервера:
   ```bash
   php artisan serve
   ```
