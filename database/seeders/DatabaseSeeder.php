<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tech = Category::firstOrCreate(
            ['slug' => 'tech'],
            ['name' => 'Технологии', 'description' => 'Статьи о программировании, IT и разработке']
        );

        $design = Category::firstOrCreate(
            ['slug' => 'design'],
            ['name' => 'Дизайн и UI', 'description' => 'Интерфейсы, типографика и пользовательский опыт']
        );

        $backend = Tag::firstOrCreate(['slug' => 'backend'], ['name' => 'Backend']);
        $laravel = Tag::firstOrCreate(['slug' => 'laravel'], ['name' => 'Laravel']);
        $php = Tag::firstOrCreate(['slug' => 'php'], ['name' => 'PHP']);
        $architecture = Tag::firstOrCreate(['slug' => 'architecture'], ['name' => 'Архитектура']);

        $article1 = Article::firstOrCreate(
            ['slug' => 'intro-to-laravel-11'],
            [
                'category_id' => $tech->id,
                'title' => 'Введение в архитектуру Laravel 11',
                'content' => 'Laravel 11 получил упрощенную структуру каталогов и оптимизированный механизм загрузки конфигураций.',
                'author_name' => 'Александр Шеметов',
                'is_published' => true,
                'views_count' => 42,
            ]
        );
        $article1->tags()->sync([$laravel->id, $php->id, $backend->id]);

        $article2 = Article::firstOrCreate(
            ['slug' => 'eloquent-orm-deep-dive'],
            [
                'category_id' => $tech->id,
                'title' => 'Продвинутая работа с Eloquent ORM',
                'content' => 'Подробное руководство по связям BelongsTo, HasMany, BelongsToMany, Query Scopes и полиморфным отношениям.',
                'author_name' => 'Александр Шеметов',
                'is_published' => true,
                'views_count' => 128,
            ]
        );
        $article2->tags()->sync([$laravel->id, $architecture->id]);

        $article3 = Article::firstOrCreate(
            ['slug' => 'clean-ui-principles'],
            [
                'category_id' => $design->id,
                'title' => 'Принципы чистого веб-интерфейса',
                'content' => 'Удобная навигация, читаемые шрифты и контрастные акценты делают сайт понятным для любого пользователя.',
                'author_name' => 'Александр Шеметов',
                'is_published' => false,
                'views_count' => 5,
            ]
        );
        $article3->tags()->sync([$design->id]);

        Comment::firstOrCreate(
            [
                'commentable_type' => Article::class,
                'commentable_id' => $article1->id,
                'author_name' => 'Иван Петров',
            ],
            [
                'body' => 'Отличный обзор нововведений одиннадцатой версии!',
            ]
        );

        Comment::firstOrCreate(
            [
                'commentable_type' => Article::class,
                'commentable_id' => $article2->id,
                'author_name' => 'Сергей Смирнов',
            ],
            [
                'body' => 'Примеры со связями и полиморфизмом очень наглядные.',
            ]
        );

        Comment::firstOrCreate(
            [
                'commentable_type' => Category::class,
                'commentable_id' => $tech->id,
                'author_name' => 'Модератор',
            ],
            [
                'body' => 'В этой категории публикуются только проверенные технические материалы.',
            ]
        );
    }
}
