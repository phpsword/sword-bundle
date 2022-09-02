<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Exception;

final class WordpressLoginSuccessfulException extends \Exception
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly bool $rememberMe,
    ) {
        parent::__construct();
    }
}
