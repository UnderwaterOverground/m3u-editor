<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UrlSafeCredential implements ValidationRule
{
    /**
     * Characters that cannot survive being embedded in an Xtream stream path
     * (`/live/{username}/{password}/{id}.{ext}`): a slash or backslash splits
     * the segment so the route never matches, `?` and `#` terminate the path,
     * `%` is decoded by the router, and whitespace is rejected or re-encoded
     * by most players. Rejecting them at input time is the only place the
     * problem is visible - the device just sees a 404 with no hint that the
     * credential itself is at fault.
     */
    private const PATTERN = '/[\s\/\\\\?#%]/u';

    public static function isSafe(string $value): bool
    {
        return preg_match(self::PATTERN, $value) !== 1;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=):PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        if (! self::isSafe($value)) {
            $fail(__('The :attribute cannot contain spaces or the characters / \\ ? # % because it is embedded in stream URLs.'));
        }
    }
}
