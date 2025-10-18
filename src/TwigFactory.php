<?php

declare(strict_types=1);

namespace TwigCSWebsite;

use League\CommonMark\Environment\Environment as CommonMarkEnvironment;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Zenstruck\CommonMark\Extension\GitHub\AdmonitionExtension;

final class TwigFactory
{
    private string $projectDir;

    /**
     * @var array<string, mixed>
     */
    private array $twigOptions;

    private Environment $twig;

    /**
     * @param array<string, mixed> $twigOptions
     *
     * @throws LoaderError
     */
    public function __construct(string $projectDir, array $twigOptions = [])
    {
        $this->projectDir = $projectDir;
        $this->twigOptions = $twigOptions;
        $this->twig = $this->createEnvironment();
    }

    public function getEnvironment(): Environment
    {
        return $this->twig;
    }

    /**
     * @throws LoaderError
     */
    private function createEnvironment(): Environment
    {
        $loader = new FilesystemLoader([$this->projectDir.'/templates']);
        $loader->addPath($this->projectDir.'/docs', 'docs');

        $twig = new Environment($loader, $this->twigOptions);

        $debug = $this->twigOptions['debug'] ?? false;
        if (true === $debug) {
            $twig->addExtension(new DebugExtension());
        }

        $this->registerFilters($twig);

        return $twig;
    }

    private function registerFilters(Environment $twig): void
    {
        $twig->addFilter(new TwigFilter('markdown', [$this, 'renderMarkdown'], ['is_safe' => ['html']]));
        $twig->addFilter(new TwigFilter('markdown_with_toc', [$this, 'renderMarkdownWithToc'], ['is_safe' => ['html']]));
    }

    /**
     * @throws CommonMarkException
     */
    public function renderMarkdown(string $content): string
    {
        $config = [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ];

        return $this->convertMarkdown(trim($content), $config);
    }

    /**
     * @return array{content: string, toc: array<int, array<string, mixed>>}
     *
     * @throws CommonMarkException
     */
    public function renderMarkdownWithToc(string $content): array
    {
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

        $content = trim($content);

        $content = str_replace(
            ["**:\n", "(Configurable):\n"],
            ["**\n", "(Configurable)\n"],
            $content
        );

        $html = $this->convertMarkdown($content, $config);

        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $toc = [];
        /** @var \DOMNodeList<\DOMElement> $headings */
        $headings = $xpath->query('//h2|//h3|//h4');

        /** @var \DOMNodeList<\DOMElement> $h1Headings */
        $h1Headings = $xpath->query('//h1');
        foreach ($h1Headings as $h1) {
            if (null !== $h1->parentNode) {
                $h1->parentNode->removeChild($h1);
            }
        }

        foreach ($headings as $heading) {
            $level = (int) substr($heading->nodeName, 1);
            $text = $heading->textContent;
            $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($text));
            \assert(null !== $slug);
            $slug = trim($slug, '-');

            $heading->setAttribute('id', $slug);

            $toc[] = [
                'level' => $level,
                'text' => $text,
                'slug' => $slug,
            ];
        }

        $index = 0;
        $toctree = $this->buildNestedToc($toc, $index, 0);

        $htmlBody = '';
        $body = $dom->getElementsByTagName('body')->item(0);
        if (null !== $body) {
            foreach ($body->childNodes as $node) {
                $htmlBody .= $dom->saveHTML($node);
            }
        }

        $htmlBody = preg_replace_callback(
            '/(href|src)="(\/?docs\/)?([a-zA-Z0-9_-]+)\.md(#?[a-zA-Z0-9_-]*\??[a-zA-Z0-9=&-]*)"/',
            static fn (array $matches): string => $matches[1].'="/'.$matches[3].$matches[4].'"',
            $htmlBody
        );
        \assert(null !== $htmlBody);

        return [
            'content' => $htmlBody,
            'toc' => $toctree,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $flatToc
     *
     * @return array<array<string, mixed>>
     */
    private function buildNestedToc(array $flatToc, int &$index, int $parentLevel): array
    {
        $nested = [];

        while ($index < \count($flatToc)) {
            $item = $flatToc[$index];

            if ($item['level'] <= $parentLevel) {
                return $nested;
            }

            $item['children'] = [];
            ++$index;

            if ($index < \count($flatToc) && $flatToc[$index]['level'] > $item['level']) {
                $item['children'] = $this->buildNestedToc($flatToc, $index, $item['level']);
            }

            $nested[] = $item;
        }

        return $nested;
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws CommonMarkException
     */
    private function convertMarkdown(string $content, array $config): string
    {
        $environment = new CommonMarkEnvironment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AdmonitionExtension());

        $converter = new MarkdownConverter($environment);

        return $converter->convert($content)->getContent();
    }
}
