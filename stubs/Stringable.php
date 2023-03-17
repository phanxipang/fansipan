<?php

declare(strict_types=1);

/**
 * Backward compat for PHP < 8.0.
 */
if (\PHP_VERSION_ID < 80000) {
    interface Stringable
    {
        /**
         * @return string
         */
        public function __toString();
    }
}
