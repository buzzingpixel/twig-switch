# Twig Switch Extension

Provides a `{% switch %}` tag for Twig switch case statements.

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

## License

Copyright 2018 BuzzingPixel, LLC

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at [http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0).

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
