<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class ArticleApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Article::with(['category', 'tags', 'comments']);

        if ($request->boolean('published')) {
            $query->published();
        }

        if ($request->has('popular')) {
            $query->popular((int) $request->input('popular', 10));
        }

        if ($search = $request->query('search')) {
            $query->search($search);
        }

        return ArticleResource::collection($query->latest()->paginate(10));
    }

    public function store(Request $request): JsonResponse
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
            'is_published' => (bool) ($request->input('is_published', true)),
            'views_count' => 0,
        ]);

        if (!empty($validated['tags'])) {
            $article->tags()->sync($validated['tags']);
        }

        $article->load(['category', 'tags', 'comments']);

        return (new ArticleResource($article))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): ArticleResource
    {
        $article = Article::withTrashed()
            ->with(['category', 'tags', 'comments'])
            ->findOrFail($id);

        return new ArticleResource($article);
    }

    public function update(Request $request, Article $article): ArticleResource
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'title' => 'sometimes|required|string|max:255',
            'author_name' => 'sometimes|required|string|max:100',
            'content' => 'sometimes|required|string',
            'is_published' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $article->update($validated);

        if ($request->has('tags')) {
            $article->tags()->sync($validated['tags'] ?? []);
        }

        $article->load(['category', 'tags', 'comments']);

        return new ArticleResource($article);
    }

    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return response()->json([
            'message' => 'Статья успешно удалена (Soft Delete)',
            'id' => $article->id,
            'deleted_at' => $article->deleted_at,
        ]);
    }
}
