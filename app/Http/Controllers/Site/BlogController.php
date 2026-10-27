<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Support\JsonLd;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Article listing with search, category and tag filters.
     */
    public function index(Request $request): View
    {
        $term = $request->string('cari')->trim()->toString();

        $articles = Article::query()
            ->published()
            ->ordered()
            ->with(['author', 'category', 'tags'])
            ->search($term)
            ->paginate(9)
            ->withQueryString();

        if ($articles->total() === 0) {
            return view('site.empty', ['title' => 'Blog']);
        }

        return view('site.blog.index', [
            'articles' => $articles,
            'term' => $term,
            'categories' => $this->categories(),
            'tags' => $this->tags(),
            'featured' => Article::query()->published()->featured()->ordered()->with(['author', 'category'])->first(),
            'schema' => [
                JsonLd::breadcrumbs([
                    ['label' => 'Beranda', 'url' => route('home')],
                    ['label' => 'Blog'],
                ]),
            ],
        ]);
    }

    /**
     * Articles inside one category.
     */
    public function category(Category $category): View
    {
        $articles = Article::query()
            ->published()
            ->ordered()
            ->with(['author', 'category', 'tags'])
            ->where('category_id', $category->getKey())
            ->paginate(9)
            ->withQueryString();

        return view('site.blog.index', [
            'articles' => $articles,
            'term' => null,
            'categories' => $this->categories(),
            'tags' => $this->tags(),
            'featured' => null,
            'heading' => $category->name,
            'description' => $category->description,
            'crumb' => ['label' => $category->name, 'url' => route('blog.category', $category)],
            'schema' => [
                JsonLd::breadcrumbs([
                    ['label' => 'Beranda', 'url' => route('home')],
                    ['label' => 'Blog', 'url' => route('blog.index')],
                    ['label' => $category->name],
                ]),
            ],
        ]);
    }

    /**
     * Articles carrying one tag.
     */
    public function tag(Tag $tag): View
    {
        $articles = Article::query()
            ->published()
            ->ordered()
            ->with(['author', 'category', 'tags'])
            ->whereHas('tags', fn ($query) => $query->whereKey($tag->getKey()))
            ->paginate(9)
            ->withQueryString();

        return view('site.blog.index', [
            'articles' => $articles,
            'term' => null,
            'categories' => $this->categories(),
            'tags' => $this->tags(),
            'featured' => null,
            'heading' => '#'.$tag->name,
            'description' => 'Kumpulan artikel seputar '.$tag->name.' dari tim kami.',
            'crumb' => ['label' => $tag->name, 'url' => route('blog.tag', $tag)],
            'schema' => [
                JsonLd::breadcrumbs([
                    ['label' => 'Beranda', 'url' => route('home')],
                    ['label' => 'Blog', 'url' => route('blog.index')],
                    ['label' => $tag->name],
                ]),
            ],
        ]);
    }

    /**
     * A single article, with related reading and breadcrumbs.
     */
    public function show(Article $article): View
    {
        abort_unless($article->isPublished(), 404);

        // A light view counter that never blocks the response or spams the cache.
        $article->registerView();

        return view('site.blog.show', [
            'article' => $article,
            'related' => $article->related(3),
            'categories' => $this->categories(),
            'tags' => $this->tags(),
            'recent' => Article::query()->published()->ordered()->whereKeyNot($article->getKey())->limit(4)->get(),
            'schema' => [
                JsonLd::article($article),
                JsonLd::breadcrumbs([
                    ['label' => 'Beranda', 'url' => route('home')],
                    ['label' => 'Blog', 'url' => route('blog.index')],
                    ...(
                        $article->category
                            ? [['label' => $article->category->name, 'url' => route('blog.category', $article->category)]]
                            : []
                    ),
                    ['label' => $article->title],
                ]),
            ],
        ]);
    }

    /**
     * Sidebar categories with published-article counts.
     */
    private function categories()
    {
        return Category::query()
            ->ordered()
            ->whereHas('articles', fn ($query) => $query->published())
            ->withCount(['articles' => fn ($query) => $query->published()])
            ->get();
    }

    /**
     * Sidebar tags, limited to those that still have published articles.
     */
    private function tags()
    {
        return Tag::query()
            ->ordered()
            ->whereHas('articles', fn ($query) => $query->published())
            ->withCount(['articles' => fn ($query) => $query->published()])
            ->limit(24)
            ->get();
    }
}
