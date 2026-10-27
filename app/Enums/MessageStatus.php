<?php

namespace App\Enums;

enum MessageStatus: string
{
    case New = 'new';
    case Read = 'read';
    case Replied = 'replied';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Baru',
            self::Read => 'Dibaca',
            self::Replied => 'Dibalas',
            self::Archived => 'Arsip',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'amber',
            self::Read => 'blue',
            self::Replied => 'green',
            self::Archived => 'zinc',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [$status->value => $status->label()])
            ->all();
    }
}
