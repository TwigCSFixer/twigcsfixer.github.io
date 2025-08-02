# Twig CS Fixer website


Experimental/WIP


## Running Locally

Get the code:
```bash
git clone
cd twig-cs-fixer
```

Install dependencies and run the server:
```bash
composer install
```

Run the Symfony server in detached mode:
```bash
symfony server:start -d
```

### Documentation pages

```
php bin/update.php
```

Will update pages in the `docs` directory.


## Build static

```
php bin/build.php
```

Will build all pages in the `bin/public` directory.
