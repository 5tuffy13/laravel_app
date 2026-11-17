<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'API Category',
            'slug' => 'api-category',
            'description' => 'Category used in API tests',
        ]);

        $this->tag = Tag::create(['name' => 'REST', 'slug' => 'rest']);
    }

    public function test_api_index_returns_paginated_article_resources(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'API Article Listing',
            'slug' => 'api-article-listing',
            'author_name' => 'API Tester',
            'content' => 'Checking JSON structure produced by ArticleResource',
            'is_published' => true,
            'views_count' => 25,
        ]);
        $article->tags()->attach($this->tag->id);
        $article->comments()->create([
            'author_name' => 'API Commenter',
            'body' => 'JSON comment verification',
        ]);

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'content',
                    'author_name',
                    'is_published',
                    'views_count',
                    'category' => ['id', 'name', 'slug'],
                    'tags' => [
                        '*' => ['id', 'name', 'slug']
                    ],
                    'comments' => [
                        '*' => ['id', 'author_name', 'body', 'created_at']
                    ],
                    'created_at',
                    'updated_at',
                    'is_deleted',
                ]
            ],
            'links',
            'meta',
        ]);
    }

    public function test_api_store_creates_article_and_returns_resource(): void
    {
        $payload = [
            'category_id' => $this->category->id,
            'title' => 'Article Created via API',
            'author_name' => 'API Author',
            'content' => 'Testing API Resource store method with validation.',
            'is_published' => true,
            'tags' => [$this->tag->id],
        ];

        $response = $this->postJson('/api/articles', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.title', 'Article Created via API');
        $response->assertJsonPath('data.category.id', $this->category->id);
        $response->assertJsonPath('data.tags.0.id', $this->tag->id);

        $this->assertDatabaseHas('articles', [
            'title' => 'Article Created via API',
            'author_name' => 'API Author',
        ]);
    }

    public function test_api_show_returns_specific_article_resource(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'API Single Show Article',
            'slug' => 'api-single-show-article',
            'author_name' => 'API Author',
            'content' => 'Show single resource content.',
            'is_published' => true,
        ]);

        $response = $this->getJson("/api/articles/{$article->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $article->id);
        $response->assertJsonPath('data.title', 'API Single Show Article');
    }

    public function test_api_update_modifies_article_resource(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Initial Title',
            'slug' => 'initial-title',
            'author_name' => 'API Author',
            'content' => 'Initial content before update.',
            'is_published' => true,
        ]);

        $response = $this->putJson("/api/articles/{$article->id}", [
            'title' => 'Updated Title via API',
            'content' => 'Updated content body.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.title', 'Updated Title via API');
        $this->assertEquals('Updated Title via API', $article->fresh()->title);
    }

    public function test_api_destroy_soft_deletes_article(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Article To Delete via API',
            'slug' => 'article-to-delete-via-api',
            'author_name' => 'API Author',
            'content' => 'This article will be soft-deleted by API.',
            'is_published' => true,
        ]);

        $response = $this->deleteJson("/api/articles/{$article->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Статья успешно удалена (Soft Delete)');

        $this->assertTrue($article->fresh()->trashed());
    }
}
