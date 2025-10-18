<?php

declare(strict_types=1);

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpClient\HttpClient;

require_once __DIR__.'/../vendor/autoload.php';

$docsDir = Path::canonicalize(__DIR__.'/../docs');
$tmpDir = __DIR__.'/../tmp';

$fs = new Filesystem();

// Build Directories (if needed)
$fs->mkdir($tmpDir, 0o755);
$fs->mkdir($docsDir, 0o755);

// Fetch docs from GitHub
$githubUrl = 'https://api.github.com/repos/VincentLanglet/Twig-CS-Fixer/';
$httpClient = HttpClient::create([
    'base_uri' => $githubUrl,
    'headers' => ['Accept' => 'application/vnd.github.v3+json'],
]);

// Fetch README.md and extract installation section
$readmeResponse = $httpClient->request('GET', 'contents/README.md');
$readmeData = $readmeResponse->toArray();
if (!isset($readmeData['download_url'])) {
    throw new RuntimeException('Missing "download_url" key in README data.');
}

$readmeContent = $httpClient->request('GET', $readmeData['download_url'])->getContent();
if (1 !== preg_match('/^##\s+Instal.*?(?=^##?\s)/ims', $readmeContent, $matches)) {
    throw new RuntimeException('Installation section not found in README.');
}

$installContent = ltrim($matches[0]);
$installContent = preg_replace('/\[!\[.*?\]\(.*?\)\]\(.*?\)/', '', $installContent); // Remove badges
assert(null !== $installContent);
$installContent = preg_replace('/<!--.*?-->/s', '', $installContent); // Remove HTML comments
assert(null !== $installContent);
$installContent = trim($installContent);

// Move up title levels for installation (h2->h1, h3->h2, etc.)
$installContent = preg_replace_callback(
    '/^(#{2,6})/m',
    fn ($match): string => str_repeat('#', strlen($match[1]) - 1),
    $installContent
);
assert(null !== $installContent);

// Save installation content
$fs->dumpFile($docsDir.'/installation.md', $installContent);

// Fetch docs pages from the repository
$docsFiles = $httpClient->request('GET', 'contents/docs');
foreach ($docsFiles->toArray() as $file) {
    if (!isset($file['path'], $file['download_url'])) {
        throw new RuntimeException('Missing "path" or "download_url" key in file data.');
    }
    if ('file' !== $file['type']) {
        continue;
    }
    $contents = $httpClient->request('GET', $file['download_url']);
    $fs->dumpFile($tmpDir.'/'.$file['path'], $contents->getContent());
}

// Update docs directory (DO NOT DELETE existing files)
$fs->mirror($tmpDir.'/docs', $docsDir);
