<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

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
