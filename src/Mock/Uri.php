<?php

declare(strict_types=1);

namespace Jenky\Atlas\Mock;

final class Uri
{
    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string|string[] $pattern
     */
    public static function matches($pattern, string $value): bool
    {
        $quoted = preg_quote('*', '/');

        $pattern = '*'.preg_replace('/^(?:'.$quoted.')+/u', '', $pattern);

        if (! is_iterable($pattern)) {
            $pattern = [$pattern];
        }

        foreach ($pattern as $pattern) {
            $pattern = (string) $pattern;

            // If the given value is an exact match we can of course return true right
            // from the beginning. Otherwise, we will translate asterisks and do an
            // actual pattern match against the two strings to see if they match.
            if ($pattern === $value) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');

            // Asterisks are translated into zero-or-more regular expression wildcards
            // to make it convenient to check if the strings starts with the given
            // pattern such as "library/*", making any string check convenient.
            $pattern = str_replace('\*', '.*', $pattern);

            if (preg_match('#^'.$pattern.'\z#u', $value) === 1) {
                return true;
            }
        }

        return false;
    }
}
