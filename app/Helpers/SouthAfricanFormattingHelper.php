<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * South African Formatting Helper
 *
 * Provides formatting functions that conform to South African standards:
 * - Currency: R 1,250.00 (with space after R)
 * - Dates: DD/MM/YYYY
 * - Phone: +27 12 345 6789 (with spaces)
 *
 * @see DESIGN_SYSTEM.md - South African UX Standards section
 */
class SouthAfricanFormattingHelper
{
    /**
     * Format currency in South African Rand (ZAR)
     *
     * @param float|int|string $amount The amount to format
     * @param bool $compact If true, omit the space (R1,250 instead of R 1,250.00)
     * @return string Formatted currency string
     *
     * @example
     * format_za_currency(1250.00) // "R 1,250.00"
     * format_za_currency(1250, true) // "R1,250"
     */
    public static function formatCurrency(float|int|string $amount, bool $compact = false): string
    {
        $amount = floatval($amount);

        if ($compact) {
            return 'R' . number_format($amount, 0, '.', ',');
        }

        return 'R ' . number_format($amount, 2, '.', ',');
    }

    /**
     * Format date in South African standard (DD/MM/YYYY)
     *
     * @param Carbon|string|null $date Date to format
     * @param bool $includeTime Include time in format
     * @return string Formatted date string
     *
     * @example
     * format_za_date(now()) // "15/10/2025"
     * format_za_date(now(), true) // "15/10/2025 14:30"
     */
    public static function formatDate(Carbon|string|null $date, bool $includeTime = false): string
    {
        if (!$date) {
            return '-';
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        if ($includeTime) {
            return $date->format('d/m/Y H:i');
        }

        return $date->format('d/m/Y');
    }

    /**
     * Format date in long South African format
     *
     * @param Carbon|string|null $date Date to format
     * @return string Formatted date string
     *
     * @example
     * format_za_date_long(now()) // "15 October 2025"
     */
    public static function formatDateLong(Carbon|string|null $date): string
    {
        if (!$date) {
            return '-';
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->format('j F Y');
    }

    /**
     * Format South African phone number with spaces
     *
     * @param string|null $phone Phone number to format
     * @return string Formatted phone number
     *
     * @example
     * format_za_phone('+27123456789') // "+27 12 345 6789"
     * format_za_phone('0821234567') // "082 123 4567"
     */
    public static function formatPhone(?string $phone): string
    {
        if (!$phone) {
            return '-';
        }

        // Remove all spaces and special characters
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);

        // Format international numbers (+27 12 345 6789)
        if (str_starts_with($cleaned, '+27')) {
            $number = substr($cleaned, 3);
            if (strlen($number) >= 9) {
                return '+27 ' . substr($number, 0, 2) . ' ' . substr($number, 2, 3) . ' ' . substr($number, 5);
            }
        }

        // Format mobile numbers (082 123 4567)
        if (str_starts_with($cleaned, '0') && strlen($cleaned) === 10) {
            return substr($cleaned, 0, 3) . ' ' . substr($cleaned, 3, 3) . ' ' . substr($cleaned, 6);
        }

        // Format landline numbers ((012) 345 6789)
        if (str_starts_with($cleaned, '0') && strlen($cleaned) >= 9) {
            return '(' . substr($cleaned, 0, 3) . ') ' . substr($cleaned, 3, 3) . ' ' . substr($cleaned, 6);
        }

        return $phone; // Return original if format not recognized
    }

    /**
     * Format a number with South African thousand separators
     *
     * @param float|int $number Number to format
     * @param int $decimals Number of decimal places
     * @return string Formatted number
     *
     * @example
     * format_za_number(1234567.89) // "1,234,567.89"
     */
    public static function formatNumber(float|int $number, int $decimals = 0): string
    {
        return number_format($number, $decimals, '.', ',');
    }

    /**
     * Format percentage for South African display
     *
     * @param float|int $value Percentage value (e.g., 0.125 for 12.5%)
     * @param int $decimals Number of decimal places
     * @return string Formatted percentage
     *
     * @example
     * format_za_percentage(0.125) // "12.5%"
     * format_za_percentage(12.5, 1) // "12.5%" (already in percent form)
     */
    public static function formatPercentage(float|int $value, int $decimals = 1): string
    {
        // If value is less than 1, assume it's a decimal (0.125 = 12.5%)
        if ($value < 1 && $value > -1) {
            $value *= 100;
        }

        return number_format($value, $decimals, '.', ',') . '%';
    }

    /**
     * Get South African date format string for Filament date pickers
     *
     * @return string Date format string
     */
    public static function getFilamentDateFormat(): string
    {
        return 'd/m/Y';
    }

    /**
     * Get South African datetime format string for Filament date pickers
     *
     * @return string Datetime format string
     */
    public static function getFilamentDateTimeFormat(): string
    {
        return 'd/m/Y H:i';
    }
}
