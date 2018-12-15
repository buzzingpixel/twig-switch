# Twig Switch Extension

Provides a {% switch %} tag for Twig switch case statements.

## Installation

When instantiating your Twig instance, add the SwitchTwigExtension to Twig via the `addExtension()` method. Like so:

```php
<?php
declare(strict_types=1);

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use buzzingpixel\twigswitch\SwitchTwigExtension;

$twig = new Environment(new FilesystemLoader('/path/to/templates'), [
    'debug' => true,
    'cache' => '/path/to/cache',
    'strict_variables' => true,
]);

$twig->addExtension(new SwitchTwigExtension());
```

## Usage

```twig
{% switch myVar %}
    {% case 'value1' %}
        {# ...code here to run for value1 #}
    {% case 'value2' %}
        {# ...code here to run for value2 #}
    {% default %}
        {# ...code here to run for default when no case matched #}
{% endswitch %}
```
