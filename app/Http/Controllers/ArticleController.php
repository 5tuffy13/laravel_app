<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status', 'all');

        $query = Article::with(['category', 'tags'])->withCount('comments');

        if ($status === 'published') {
            $query->published();
        } elseif ($status === 'popular') {
            $query->popular(10);
        } elseif ($status === 'trashed') {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            $query->search($search);
        }

        $articles = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('articles.index', compact('articles', 'categories', 'search', 'status'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('articles.create', compact('categories', 'tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'author_name' => 'required|string|max:100',
            'content' => 'required|string',
            'is_published' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug ?: 'article';
        $counter = 1;
        while (Article::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $article = Article::create([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'author_name' => $validated['author_name'],
            'content' => $validated['content'],
            'is_published' => (bool) ($request->input('is_published', false)),
            'views_count' => 0,
        ]);

        if (!empty($validated['tags'])) {
            $article->tags()->sync($validated['tags']);
        }

        return redirect()->route('articles.show', $article)
            ->with('success', 'Статья успешно создана!');
    }

    public function show(int $id): View
    {
        $article = Article::withTrashed()
            ->with(['category', 'tags', 'comments' => function ($q) {
                $q->latest();
            }])
            ->findOrFail($id);

        if (!$article->trashed()) {
            $article->increment('views_count');
        }

        return view('articles.show', compact('article'));
    }

    public function edit(Article $article): View
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('articles.edit', compact('article', 'categories', 'tags'));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'author_name' => 'required|string|max:100',
            'content' => 'required|string',
            'is_published' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $article->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'author_name' => $validated['author_name'],
            'content' => $validated['content'],
            'is_published' => (bool) ($request->input('is_published', false)),
        ]);

        $article->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('articles.show', $article)
            ->with('success', 'Статья успешно обновлена!');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Статья помещена в архив (мягкое удаление)!');
    }

    public function restore(int $id): RedirectResponse
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $article->restore();

        return redirect()->route('articles.show', $article)
            ->with('success', 'Статья успешно восстановлена из архива!');
    }

    public function addComment(Request $request, int $id): RedirectResponse
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'author_name' => 'required|string|max:100',
            'body' => 'required|string|max:1000',
        ]);

        $article->comments()->create($validated);

        return redirect()->route('articles.show', $article)
            ->with('success', 'Комментарий добавлен через полиморфную связь!');
    }
}
