<?php

namespace App\Models;

use App\Concerns\HasSlug;
use App\Enums\ArticleStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * A blog article written in Markdown and published through the CMS.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $category_id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $body
 * @property string|null $cover_image
 * @property ArticleStatus $status
 * @property bool $is_featured
 * @property Carbon|null $published_at
 * @property int $views_count
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property string|null $seo_keywords
 * @property-read string $body_html
 * @property-read string|null $cover_url
 * @property-read int $reading_time
 */
#[Fillable([
    'user_id', 'category_id', 'title', 'slug', 'excerpt', 'body', 'cover_image',
    'status', 'is_featured', 'published_at', 'views_count',
    'seo_title', 'seo_description', 'seo_keywords',
])]
class Article extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    /**
     * Words per minute used for the reading-time estimate.
     */
    public const int READING_SPEED = 200;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ArticleStatus::class,
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'views_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $article) {
            if (blank($article->excerpt)) {
                $article->excerpt = Str::limit(trim(strip_tags($article->body)), 160);
            }

            // Publishing for the first time stamps the publish date automatically.
            if ($article->status === ArticleStatus::Published && blank($article->published_at)) {
                $article->published_at = now();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Only articles that are published and whose publish date has arrived.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', ArticleStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }

    /**
     * Keyword search across title, excerpt and body.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], (string) $term).'%';

        // Note: the `when` callback receives the *condition*, not the term, so the
        // pattern is captured from the outer scope instead of a second parameter.
        return $query->when(filled($term), function (Builder $builder) use ($like) {
            $builder->where(function (Builder $inner) use ($like) {
                $inner->where('title', 'like', $like)
                    ->orWhere('excerpt', 'like', $like)
                    ->orWhere('body', 'like', $like);
            });
        });
    }

    public function isPublished(): bool
    {
        return $this->status === ArticleStatus::Published
            && $this->published_at !== null
            && ! $this->published_at->isFuture();
    }

    public function getBodyHtmlAttribute(): string
    {
        return Str::markdown($this->body);
    }

    public function getCoverUrlAttribute(): ?string
    {
        return image_url($this->cover_image);
    }

    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->body));

        return max(1, (int) ceil($words / self::READING_SPEED));
    }

    /**
     * SEO title with a sensible fallback to the article title.
     */
    public function getMetaTitleAttribute(): string
    {
        return filled($this->seo_title) ? (string) $this->seo_title : $this->title;
    }

    /**
     * Meta description derived from the SEO field, excerpt or body.
     */
    public function getMetaDescriptionAttribute(): string
    {
        if (filled($this->seo_description)) {
            return Str::limit((string) $this->seo_description, 160, '');
        }

        $source = filled($this->excerpt) ? (string) $this->excerpt : strip_tags($this->body);

        return Str::limit(trim($source), 160, '');
    }

    /**
     * Count a page view without touching updated_at or firing model events.
     */
    public function registerView(): void
    {
        static::query()->whereKey($this->getKey())->increment('views_count');
    }

    /**
     * Other published articles, most recent first, for the "related posts" block.
     */
    public function related(int $limit = 3): Collection
    {
        $tagIds = $this->tags()->pluck('tags.id');

        return static::query()
            ->published()
            ->ordered()
            ->whereKeyNot($this->getKey())
            ->where(function (Builder $query) use ($tagIds) {
                $query->where('category_id', $this->category_id)
                    ->orWhereHas('tags', fn (Builder $tagQuery) => $tagQuery->whereIn('tags.id', $tagIds));
            })
            ->limit($limit)
            ->get();
    }
}
