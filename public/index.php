<?php

declare(strict_types=1);

use TwigCSWebsite\TwigFactory;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/TwigFactory.php';

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$page = trim($uri, '/') ?: 'index';

$projectDir = dirname(__DIR__);
$factory = new TwigFactory($projectDir, ['debug' => true]);
$twig = $factory->getEnvironment();

$template = $page.'.html.twig';
if (!$twig->getLoader()->exists($template)) {
    http_response_code(404);
    exit;
}

echo $twig->render($template, [
    'page' => $page,
    'current_url' => $uri,
    'composer_package' => 'vincentlanglet/twig-cs-fixer',
    'github_repository' => 'vincentlanglet/twig-cs-fixer',
    'github_username' => 'vincentlanglet',
    'github_url' => 'https://github.com/vincentlanglet/twig-cs-fixer',
]);
