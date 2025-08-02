## Installation

### From composer

This tool can be installed with [Composer](https://getcomposer.org/).

Add the package as a dependency of your project

```bash
composer require --dev vincentlanglet/twig-cs-fixer
```

Then, use it!

```bash
vendor/bin/twig-cs-fixer lint /path/to/code
vendor/bin/twig-cs-fixer lint --fix /path/to/code
```

> [!NOTE]
> Although [bin-dependencies may have composer conflicts](https://github.com/bamarni/composer-bin-plugin#why-a-hard-problem-with-a-simple-solution),
> this is the recommended way because it will autoload everything you need.

### As a PHAR

You can always fetch the stable version as a Phar archive through the following
link with the `VERSION` you're looking for:

```bash
wget -c https://github.com/VincentLanglet/Twig-CS-Fixer/releases/download/VERSION/twig-cs-fixer.phar
```

The PHAR files are signed with a public key which can be queried at 
`keys.openpgp.org` with the id `AC0E7FD8858D80003AA88FF8DEBB71EDE9601234`.

> [!TIP]
> You will certainly need to add
> ```php
> require_once __DIR__.'/vendor/autoload.php';
> ```
> in your [config file](docs/configuration.md) in order to:
> - Use existing [node based rules](docs/configuration.md#node-based-rules).
> - Write your own custom rules.