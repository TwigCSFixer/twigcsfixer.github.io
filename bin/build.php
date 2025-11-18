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

// Copy favicon files
$faviconFiles = [
    'android-chrome-192x192.png', 'android-chrome-512x512.png',
    'apple-touch-icon.png',
    'favicon.ico', 'favicon.png', 'favicon-16x16.png', 'favicon-32x32.png',
];
foreach ($faviconFiles as $file) {
    $fs->copy(__DIR__.'/../public/'.$file, $buildDir.'/'.$file, true);
}

// Copy other static files
$staticFiles = [
    'google8379faca4ea7bbbb.html',
];
foreach ($staticFiles as $file) {
    $fs->copy(__DIR__.'/../public/'.$file, $buildDir.'/'.$file, true);
}

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
