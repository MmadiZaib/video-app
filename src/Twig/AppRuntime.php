<?php

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    public function slugify(string $url): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $url)));
    }
}
