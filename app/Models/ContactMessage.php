<?php

namespace App\Models;

use App\Enums\MessageStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Message submitted through the public contact form.
 */
#[Fillable(['name', 'email', 'phone', 'subject', 'message', 'status', 'ip_address'])]
class ContactMessage extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MessageStatus::class,
        ];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', MessageStatus::New);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    public function markAs(MessageStatus $status): void
    {
        $this->update(['status' => $status]);
    }
}
