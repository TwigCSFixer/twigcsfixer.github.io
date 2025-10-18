# Frequently Asked Questions

## About Twig-CS-Fixer

### What does Twig-CS-Fixer do?

Twig-CS-Fixer automatically fixes coding style issues in your Twig templates. It enforces consistent indentation,
spacing, tag formatting, and naming conventions to ensure code quality and readability across your project.

### Is Twig-CS-Fixer compatible with my framework?

Yes! Twig-CS-Fixer works with any framework that uses Twig templates, including:

- Symfony
- Drupal
- Craft CMS
- Any custom implementation using Twig 3.4+

## Usage

### What's the difference between 'fix' and 'lint' commands?

Twig-CS-Fixer provides two main commands:

- **fix**: Automatically fixes coding style issues in your templates
- **lint**: Only reports issues without modifying files (useful for CI/CD)

```bash
# Fix templates
vendor/bin/twig-cs-fixer fix templates/

# Check templates without modifying
vendor/bin/twig-cs-fixer lint templates/
```

### How do I integrate Twig-CS-Fixer with CI/CD pipelines?

Twig-CS-Fixer can be easily integrated with CI/CD pipelines. Here's an example for GitHub Actions:

```yaml
# .github/workflows/twig-cs-fixer.yml
name: Twig-CS-Fixer

on:
    push:
        paths:
            - '**.twig'

jobs:
    twig-cs-fixer:
        runs-on: ubuntu-latest
        steps:
            -   uses: actions/checkout@v3
            -   uses: shivammathur/setup-php@v2
                with:
                    php-version: '8.4'
            -   name: Install dependencies
                run: composer install
            -   name: Check Twig templates
                run: vendor/bin/twig-cs-fixer lint templates/
```

### How do I update Twig-CS-Fixer?

To update Twig-CS-Fixer to the latest version, run:

```bash
composer update twig-cs-fixer/twig-cs-fixer
```

Always check the changelog for any breaking changes before updating to a new major version.

### How can I customize the coding standards?

You can customize all rules in your `.twig-cs-fixer.php` configuration file:

```php
$ruleset = new TwigCsFixer\Ruleset\Ruleset();

// You can start from a default standard
$ruleset->addStandard(new TwigCsFixer\Standard\TwigCsFixer());

// And then add/remove/override some rules
$ruleset->addRule(new TwigCsFixer\Rules\File\FileExtensionRule());
$ruleset->removeRule(TwigCsFixer\Rules\Whitespace\EmptyLinesRule::class);
$ruleset->overrideRule(new TwigCsFixer\Rules\Punctuation\PunctuationSpacingRule(
    ['}' => 1],
    ['{' => 1],
));

$config = new TwigCsFixer\Config\Config();
$config->setRuleset($ruleset);

return $config;
```

Check the [Configuration](/configuration) page for a complete list of available rules and options.

### Can I exclude certain files or directories?

Yes, you can exclude specific files or directories in your configuration:

```php
$finder = new TwigCsFixer\File\Finder();
$finder->in('templates');
$finder->exclude('myCustomDirectory');

$config = new TwigCsFixer\Config\Config();
$config->setFinder($finder);

return $config;
```

Check the [Configuration](/configuration) page for a complete list of available rules and options.

## Contact & Support

### I found a bug. How can I report it?

If you encounter any issues or bugs, please report them on
our [GitHub Issues](https://github.com/VincentLanglet/Twig-CS-Fixer)
page. When reporting a bug, please include:

- Twig-CS-Fixer version
- PHP version
- Your configuration file
- Steps to reproduce the issue
- Expected vs. actual behavior

### Still have questions?

Can't find the answer you're looking for? Feel free to reach out to our community or open an issue on GitHub.
