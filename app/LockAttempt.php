<?php

declare(strict_types=1);

namespace App;

use Illuminate\Support\Facades\Cache;

class LockAttempt
{
    private Cache $cacheFacade;

    public function __construct(Cache $cacheFacade)
    {
        $this->cacheFacade = $cacheFacade;
    }

    public function __invoke(): array
    {
        return [
            'hasLock' => ($this->cacheFacade::lock('lock', 1))->get()
        ];
    }
}
