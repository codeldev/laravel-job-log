<?php

declare(strict_types=1);

arch('it uses strict types and strict equality')
    ->expect('src')
    ->toUseStrictTypes()
    ->toUseStrictEquality()
    ->toBeCasedCorrectly();

arch('it conforms to Laravel conventions')
    ->preset()
    ->laravel();
