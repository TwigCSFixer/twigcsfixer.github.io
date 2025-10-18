<?php

declare(strict_types=1);

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use TwigCSWebsite\TwigFactory;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/TwigFactory.php';

$projectDir = dirname(__DIR__);
$buildDir = Path::canonicalize(__DIR__.'/public');
$templateDir = $projectDir.'/templates';

$fs = new Filesystem();

// Build Directory
$fs->mkdir($buildDir, 0o755);

// Copy CSS files
$fs->mkdir($buildDir.'/css', 0o755);
$fs->mirror(__DIR__.'/../public/css', $buildDir.'/css');

// Copy Images
$fs->mkdir($buildDir.'/images', 0o755);
$fs->mirror(__DIR__.'/../public/images', $buildDir.'/images');

// Configure Twig
$factory = new TwigFactory($projectDir);
$twig = $factory->getEnvironment();

// Render HTML files
$pages = glob($templateDir.'/*.html.twig');
assert(false !== $pages);
foreach ($pages as $pageFile) {
    $page = basename($pageFile, '.html.twig');
    $uri = '/'.('index' === $page ? '' : $page);
    $html = $twig->render($page.'.html.twig', [
        'page' => $page,
        'current_url' => $uri,
        'composer_package' => 'vincentlanglet/twig-cs-fixer',
        'github_repository' => 'vincentlanglet/twig-cs-fixer',
        'github_username' => 'vincentlanglet',
        'github_url' => 'https://github.com/vincentlanglet/twig-cs-fixer',
    ]);
    $fs->dumpFile(Path::join($buildDir, '/', $page.'.html'), $html);
}
