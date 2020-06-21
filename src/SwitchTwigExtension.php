<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\twigswitch;

use Twig\Extension\AbstractExtension;

class SwitchTwigExtension extends AbstractExtension
{
    public function getTokenParsers(): array
    {
        return [
            new SwitchTokenParser(),
        ];
    }
}
