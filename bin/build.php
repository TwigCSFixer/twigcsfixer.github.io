<?php

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__.'/../vendor/autoload.php';

$buildDir = Path::canonicalize(__DIR__.'/public');
$templateDir = __DIR__.'/../templates';

$fs = new Filesystem();

// Build Directory
$fs->mkdir($buildDir, 0755);

// Copy CSS files
$fs->mkdir($buildDir.'/css', 0755);
$fs->mirror(__DIR__.'/../public/css', $buildDir.'/css');

// Copy Images
$fs->mkdir($buildDir.'/images', 0755);
$fs->mirror(__DIR__.'/../public/images', $buildDir.'/images');

// Configure Twig
$loader = new FilesystemLoader($templateDir);
$twig = new Environment($loader);

// Render HTML files
$pages = glob($templateDir.'/*.html.twig');
foreach ($pages as $page) {
    $page = basename($page, '.html.twig');
    $uri = '/'.($page === 'index' ? '' : $page);
    $html = $twig->render($page.'.html.twig', [
        'page' => $page,
        'current_url' => $uri,
        'composer_package' => 'vincentlanglet/twig-cs-fixer',
        'github_repository' => 'vincentlanglet/twig-cs-fixer',
        'github_username' => 'vincentlanglet',
    ]);
    $fs->dumpFile(Path::join($buildDir, '/', $page.'.html'), $html);
}
