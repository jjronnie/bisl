<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Normalize phone number to international format with country code
     * Uganda's country code is +256
     */
    public static function normalize(string $phone): string
    {
        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // If already has country code, return as is
        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        // If starts with 0, replace with country code
        if (str_starts_with($phone, '0')) {
            return '+256' . substr($phone, 1);
        }

        // If no country code, add it
        return '+256' . $phone;
    }

    /**
     * Validate phone number format
     */
    public static function isValid(string $phone): bool
    {
        $normalized = self::normalize($phone);

        // Should be +256 followed by 9 digits
        return preg_match('/^\+256\d{9}$/', $normalized);
    }
}
