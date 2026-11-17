<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleDatabaseTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Tag $tag1;
    private Tag $tag2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Web Development',
            'slug' => 'web-dev',
            'description' => 'Category for web development topics',
        ]);

        $this->tag1 = Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $this->tag2 = Tag::create(['name' => 'PHP', 'slug' => 'php']);
    }

    public function test_article_belongs_to_category_and_has_tags(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'First Test Article',
            'slug' => 'first-test-article',
            'author_name' => 'Author A',
            'content' => 'Some test content for relationships test',
            'is_published' => true,
            'views_count' => 15,
        ]);

        $article->tags()->attach([$this->tag1->id, $this->tag2->id]);

        // BelongsTo relation
        $this->assertEquals($this->category->id, $article->category->id);
        $this->assertEquals('Web Development', $article->category->name);

        // HasMany inverse relation
        $this->assertCount(1, $this->category->articles);
        $this->assertEquals($article->id, $this->category->articles->first()->id);

        // BelongsToMany relation
        $this->assertCount(2, $article->tags);
        $this->assertTrue($article->tags->contains($this->tag1));
        $this->assertTrue($article->tags->contains($this->tag2));

        // Inverse BelongsToMany relation
        $this->assertCount(1, $this->tag1->articles);
        $this->assertEquals($article->id, $this->tag1->articles->first()->id);
    }

    public function test_polymorphic_comments_can_belong_to_article_and_category(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Article With Comments',
            'slug' => 'article-with-comments',
            'author_name' => 'Author B',
            'content' => 'Content for polymorphic relation test',
            'is_published' => true,
        ]);

        // Comment on Article
        $articleComment = $article->comments()->create([
            'author_name' => 'Reader 1',
            'body' => 'Great article!',
        ]);

        // Comment on Category
        $categoryComment = $this->category->comments()->create([
            'author_name' => 'Moderator',
            'body' => 'Approved category description.',
        ]);

        $this->assertInstanceOf(Article::class, $articleComment->commentable);
        $this->assertEquals($article->id, $articleComment->commentable->id);

        $this->assertInstanceOf(Category::class, $categoryComment->commentable);
        $this->assertEquals($this->category->id, $categoryComment->commentable->id);

        $this->assertCount(1, $article->comments);
        $this->assertCount(1, $this->category->comments);
    }

    public function test_query_scopes_filter_records_correctly(): void
    {
        $published = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Published Popular Guide',
            'slug' => 'published-popular-guide',
            'author_name' => 'Author C',
            'content' => 'Deep dive into performance optimizations',
            'is_published' => true,
            'views_count' => 120,
        ]);

        $draft = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Draft Unpopular Notes',
            'slug' => 'draft-unpopular-notes',
            'author_name' => 'Author D',
            'content' => 'Rough draft with few views',
            'is_published' => false,
            'views_count' => 2,
        ]);

        // Scope Published
        $publishedArticles = Article::published()->get();
        $this->assertCount(1, $publishedArticles);
        $this->assertEquals($published->id, $publishedArticles->first()->id);

        // Scope Popular
        $popularArticles = Article::popular(50)->get();
        $this->assertCount(1, $popularArticles);
        $this->assertEquals($published->id, $popularArticles->first()->id);

        // Scope Search
        $searchResults = Article::search('optimizations')->get();
        $this->assertCount(1, $searchResults);
        $this->assertEquals($published->id, $searchResults->first()->id);
    }

    public function test_soft_deletes_and_restoration(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Soft Delete Target',
            'slug' => 'soft-delete-target',
            'author_name' => 'Author E',
            'content' => 'Content to be soft deleted',
            'is_published' => true,
        ]);

        $id = $article->id;

        // Perform soft delete
        $article->delete();

        $this->assertNull(Article::find($id));
        $this->assertNotNull(Article::withTrashed()->find($id));
        $this->assertCount(1, Article::onlyTrashed()->get());
        $this->assertTrue(Article::withTrashed()->find($id)->trashed());

        // Restore
        Article::withTrashed()->find($id)->restore();

        $this->assertNotNull(Article::find($id));
        $this->assertFalse(Article::find($id)->trashed());
    }

    public function test_web_crud_workflow(): void
    {
        // 1. Index page
        $res = $this->get(route('articles.index'));
        $res->assertStatus(200);
        $res->assertSee('Каталог статей');

        // 2. Create page
        $res = $this->get(route('articles.create'));
        $res->assertStatus(200);
        $res->assertSee('Новая статья');

        // 3. Store action
        $postData = [
            'category_id' => $this->category->id,
            'title' => 'Web Created Article',
            'author_name' => 'Alexander Shemetov',
            'content' => 'Article created via standard web form submission.',
            'is_published' => '1',
            'tags' => [$this->tag1->id, $this->tag2->id],
        ];

        $res = $this->post(route('articles.store'), $postData);
        $res->assertRedirect();

        $article = Article::where('title', 'Web Created Article')->first();
        $this->assertNotNull($article);
        $this->assertCount(2, $article->tags);

        // 4. Show page
        $res = $this->get(route('articles.show', $article->id));
        $res->assertStatus(200);
        $res->assertSee('Web Created Article');
        $this->assertEquals(1, $article->fresh()->views_count);

        // 5. Add comment via web
        $res = $this->post(route('articles.comments.store', $article->id), [
            'author_name' => 'Student Tester',
            'body' => 'Testing polymorphic comment addition from web interface.',
        ]);
        $res->assertRedirect();
        $this->assertCount(1, $article->fresh()->comments);

        // 6. Edit page
        $res = $this->get(route('articles.edit', $article->id));
        $res->assertStatus(200);
        $res->assertSee('Редактирование статьи');

        // 7. Update action
        $updateData = [
            'category_id' => $this->category->id,
            'title' => 'Updated Web Article Title',
            'author_name' => 'Alexander Shemetov',
            'content' => 'Updated content with single tag.',
            'is_published' => '1',
            'tags' => [$this->tag1->id],
        ];
        $res = $this->put(route('articles.update', $article->id), $updateData);
        $res->assertRedirect();

        $article->refresh();
        $this->assertEquals('Updated Web Article Title', $article->title);
        $this->assertCount(1, $article->tags);

        // 8. Delete action (Soft Delete)
        $res = $this->delete(route('articles.destroy', $article->id));
        $res->assertRedirect(route('articles.index'));
        $this->assertTrue($article->fresh()->trashed());

        // 9. Restore action
        $res = $this->post(route('articles.restore', $article->id));
        $res->assertRedirect();
        $this->assertFalse($article->fresh()->trashed());
    }
}
