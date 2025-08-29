# Frequently Asked Questions

## About Twig-CS-Fixer

### What exactly does Twig CS Fixer do?

Twig CS Fixer automatically fixes coding style issues in your Twig templates. It enforces consistent indentation,
spacing, tag formatting, and naming conventions to ensure code quality and readability across your project.

### Is Twig CS Fixer compatible with my framework?

Yes! Twig CS Fixer works with any framework that uses Twig templates, including:

- Symfony
- Drupal
- Craft CMS
- Any custom implementation using Twig 2.0+ or 3.0+

## Usage

### How can I customize the coding standards?

You can customize all rules in your `.twig-cs-fixer.php` configuration file:

```php
use TwigCsFixer\Config;

return (new Config())
    ->setRules([
        'indentation' => [
            'size' => 4,
            'use_tabs' => false
        ],
        'spacing' => true,
        'lowercase_names' => true,
        'empty_tags' => true,
    ]);
```

Check the [Configuration](/configuration) page for a complete list of available rules and options.

### How do I integrate Twig CS Fixer with CI/CD pipelines?

Twig CS Fixer can be easily integrated with CI/CD pipelines. Here's an example for GitHub Actions:

```yaml
# .github/workflows/twig-cs-fixer.yml
name: Twig CS Fixer

on:
    push:
        paths:
            - '**.twig'
            - '**.html.twig'

jobs:
    twig-cs-fixer:
        runs-on: ubuntu-latest
        steps:
            -   uses: actions/checkout@v3
            -   uses: shivammathur/setup-php@v2
                with:
                    php-version: '8.1'
            -   name: Install dependencies
                run: composer install
            -   name: Check Twig templates
                run: vendor/bin/twig-cs-fixer check templates/
```

### How do I update Twig CS Fixer?

To update Twig CS Fixer to the latest version, run:

```bash
composer update twig-cs-fixer/twig-cs-fixer
```

Always check the changelog for any breaking changes before updating to a new major version.

### Can I exclude certain files or directories?

Yes, you can exclude specific files or directories in your configuration:

```php
use TwigCsFixer\Config;

return (new Config())
    ->setPaths(['templates/'])
    ->setExcludedPaths([
        'templates/vendor/',
        'templates/legacy/'
    ]);
```

### What's the difference between 'fix' and 'check' commands?

Twig CS Fixer provides two main commands:

- **fix**: Automatically fixes coding style issues in your templates
- **check**: Only reports issues without modifying files (useful for CI/CD)

```bash
# Fix templates
vendor/bin/twig-cs-fixer fix templates/

# Check templates without modifying
vendor/bin/twig-cs-fixer check templates/
```

### How does Twig CS Fixer handle custom Twig extensions?

Twig CS Fixer can be configured to work with custom Twig functions, filters, and tags. You can register your custom
elements in the configuration:

```php
use TwigCsFixer\Config;

return (new Config())
    ->registerCustomElements([
        'function' => [
            'custom_function',
            'another_function',
        ],
        'filter' => [
            'custom_filter',
        ],
        'tag' => [
            'custom_tag',
        ],
    ]);
```

## Contact & Support

### I found a bug. How can I report it?

If you encounter any issues or bugs, please report them on
our [GitHub Issues](https://github.com/VincentLanglet/Twig-CS-Fixer)
page. When reporting a bug, please include:

- Twig CS Fixer version
- PHP version
- Your configuration file
- Steps to reproduce the issue
- Expected vs. actual behavior

### Still Have Questions?

Can't find the answer you're looking for? Feel free to reach out to our community or open an issue on GitHub.
