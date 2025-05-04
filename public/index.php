<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use League\CommonMark\Environment\Environment as CommonMarkEnvironment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Twig\TwigFilter;

require_once __DIR__.'/../vendor/autoload.php';

// ---- REQUEST HANDLER ----

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$page = trim($uri, '/') ?: 'index';
if (!\ctype_alnum($page)) {
    http_response_code(404);
    exit;
}

// ---- TWIG ENVIRONMENT ----

$loader = new FilesystemLoader([__DIR__.'/../templates']);
$loader->addPath(__DIR__.'/../docs', 'docs');
$twig = new Environment($loader);

$twig->addFilter(new TwigFilter('markdown', function($content) {
    $config = [
        'html_input' => 'allow',
        'allow_unsafe_links' => false,
    ];

    $environment = new CommonMarkEnvironment($config);
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new GithubFlavoredMarkdownExtension());

    $converter = new MarkdownConverter($environment);

    return $converter->convert($content)->getContent();
}, ['is_safe' => ['html']]));

// ---- RENDER PAGE ----

echo $twig->render($page.'.html.twig', [
    'page' => $page,
    'current_url' => $uri,
    'composer_package' => 'vincentlanglet/twig-cs-fixer',
    'github_repository' => 'vincentlanglet/twig-cs-fixer',
    'github_username' => 'vincentlanglet',
    'github_url' => 'https://github.com/vincentlanglet/twig-cs-fixer',
]);
