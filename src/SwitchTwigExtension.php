<?php
declare(strict_types=1);

namespace buzzingpixel\twigswitch;

use Twig_Extension;

class SwitchTwigExtension extends Twig_Extension
{
    public function getTokenParsers(): array
    {
        return [
            new SwitchTokenParser(),
        ];
    }
}
