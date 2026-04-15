<?php

namespace App\Helpers;

use App\Models\Member;

class MessageHelper
{
    public static function replacePlaceholders(string $message, Member $member): string
    {
        $replacements = [
            '{{name}}' => $member->name ?: 'Member',
            '{{first_name}}' => $member->user->first_name ?: self::getFirstName($member->name) ?: 'Member',
            '{{last_name}}' => $member->user->last_name ?: self::getLastName($member->name) ?: 'Member',
            '{{full_name}}' => $member->name ?: 'Member',
            '{{member_no}}' => $member->member_no ?: '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    public static function getAvailablePlaceholders(): array
    {
        return [
            '{{name}}' => 'Full name (e.g., John Mukasa)',
            '{{first_name}}' => 'First name only (e.g., John)',
            '{{last_name}}' => 'Last name only (e.g., Mukasa)',
            '{{full_name}}' => 'Full name (same as {{name}})',
            '{{member_no}}' => 'Member number',
        ];
    }

    protected static function getFirstName(?string $fullName): string
    {
        if (! $fullName) {
            return 'Member';
        }

        $parts = explode(' ', trim($fullName));

        return $parts[0] ?? 'Member';
    }

    protected static function getLastName(?string $fullName): string
    {
        if (! $fullName) {
            return 'Member';
        }

        $parts = explode(' ', trim($fullName));

        return $parts[1] ?? 'Member';
    }
}
