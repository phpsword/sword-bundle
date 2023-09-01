<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Exception;

use Symfony\Component\HttpFoundation\Response;

final class WordpressLougoutSuccessfulException extends \Exception
{
    public function __construct(
        public readonly Response $response
    ) {
        parent::__construct();
    }
}
