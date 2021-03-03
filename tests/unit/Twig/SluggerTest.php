<?php

namespace App\Tests\Unit\Twig;

use App\Twig\AppRuntime;
use PHPUnit\Framework\TestCase;

class SluggerTest extends TestCase
{
    /**
     * @dataProvider getSlugs
     */
    public function testSlugify(string $string, string $slug): void
    {
        $slugger = new AppRuntime;

        $this->assertSame($slug,$slugger->slugify($string));
    }

    public function getSlugs(): iterable
    {
        yield ['Cell phones', 'cell-phones'];
        yield ['Lorem ipsum', 'lorem-ipsum'];
        yield ['Remove Space', 'remove-space'];
    }
}
