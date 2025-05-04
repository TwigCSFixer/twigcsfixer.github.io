<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__.'/../vendor/autoload.php';

$buildDir = __DIR__.'/public';
if (!is_dir($buildDir)) {
    mkdir($buildDir, 0755, true);
}

// Copy CSS files
$cssDir = __DIR__.'/../public/css';
if (!is_dir($buildDir.'/css')) {
    mkdir($buildDir.'/css', 0755, true);
}
foreach (glob($cssDir.'/*') as $file) {
    copy($file, $buildDir.'/css/'.basename($file));
}

function generate_pages(string $templateDir, string $publicDir): void
{
    $loader = new FilesystemLoader($templateDir);
    $twig = new Environment($loader);
    
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
        file_put_contents($publicDir.'/'.$page.'.html', $html);
    }
}

generate_pages(__DIR__.'/../templates', $buildDir);
