<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * A file uploaded through the CMS media library.
 *
 * @property Carbon $created_at
 */
#[Fillable(['user_id', 'disk', 'path', 'original_name', 'mime_type', 'size', 'title', 'alt'])]
class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('id');
    }

    public function getUrlAttribute(): string
    {
        return image_url($this->path) ?? '';
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = max(0, $this->size);

        return match (true) {
            $bytes >= 1048576 => round($bytes / 1048576, 1).' MB',
            $bytes >= 1024 => round($bytes / 1024).' KB',
            default => $bytes.' B',
        };
    }

    /**
     * Store an uploaded image and record it in the media library.
     */
    public static function storeUpload(UploadedFile $file, ?int $userId = null, ?string $directory = null): static
    {
        $directory = $directory ?? 'media/'.now()->format('Y/m');

        $path = $file->store($directory, 'public');

        return static::query()->create([
            'user_id' => $userId,
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize() ?: 0,
            'title' => Str::headline(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
        ]);
    }

    /**
     * Remove the underlying file. The database row is deleted by the caller.
     */
    public function deleteFile(): void
    {
        if (! str_starts_with($this->path, '/') && ! Str::startsWith($this->path, ['http://', 'https://'])) {
            Storage::disk($this->disk)->delete($this->path);
        }
    }
}
