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
$twig = new Environment($loader, [
    'debug' => true,
]);
$twig->addExtension(new \Twig\Extension\DebugExtension());

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

$twig->addFilter(new TwigFilter('markdown_with_toc', function($content) {
    // Setup CommonMark environment
    $config = [
        'html_input' => 'allow',
        'allow_unsafe_links' => false,
        'commonmark' => [
            'enable_em' => true,
            'enable_strong' => true,
            'use_asterisk' => true,
            'use_underscore' => true,
        ],
    ];
    
    $content = trim($content, '\n');
    
    $environment = new CommonMarkEnvironment($config);
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new GithubFlavoredMarkdownExtension());

    // Convert Markdown to HTML
    $converter = new MarkdownConverter($environment);
    $html = $converter->convert($content)->getContent();
    
    // Parse HTML to extract headings and add IDs
    $dom = new \DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new \DOMXPath($dom);
    
    // Find all h2, h3, h4 headings
    $toc = [];
    $headings = $xpath->query('//h2|//h3|//h4');
    
    foreach ($headings as $heading) {
        $level = (int) substr($heading->nodeName, 1);
        $text = $heading->textContent;
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($text));
        $slug = trim($slug, '-');
        
        // Add ID attribute to the heading
        $heading->setAttribute('id', $slug);
        
        $toc[] = [
            'level' => $level,
            'text' => $text,
            'slug' => $slug
        ];
    }
    
    // Nest the TOC structure
    function buildNestedToc(array $flatToc, int &$index, int $parentLevel): array
    {
        $nested = [];
        while ($index < count($flatToc)) {
            $item = $flatToc[$index];
            if ($item['level'] <= $parentLevel) {
                return $nested;
            }
            $item['children'] = [];
            $index++;
            if ($index < count($flatToc) && $flatToc[$index]['level'] > $item['level']) {
                $item['children'] = buildNestedToc($flatToc, $index, $item['level']);
            }
            $nested[] = $item;
        }
        
        return $nested;
    }
    
    $index= 0;
    $toctree = buildNestedToc($toc, $index, 0);
    
    // Get the modified HTML
    $bodyNodes = $xpath->query('//body')->item(0);
    $html = '';
    foreach ($bodyNodes->childNodes as $node) {
        $html .= $dom->saveHTML($node);
    }
    
    return [
        'content' => $html,
        'toc' => $toctree,
    ];
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
