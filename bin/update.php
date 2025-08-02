<?php

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpClient\HttpClient;

require_once __DIR__.'/../vendor/autoload.php';

$docsDir = Path::canonicalize(__DIR__.'/../docs');
$tmpDir = __DIR__.'/../tmp';

$fs = new Filesystem();

// Build Directories (if needed)
$fs->mkdir($tmpDir, 0755);
$fs->mkdir($docsDir, 0755);

// Fetch docs from GitHub
$githubUrl = 'https://api.github.com/repos/VincentLanglet/Twig-CS-Fixer/';
$httpClient = HttpClient::create([
    'base_uri' => $githubUrl,
    'headers' => ['Accept' => 'application/vnd.github.v3+json'],
]);

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

// Fetch README.md and extract installation section
$readmeResponse = $httpClient->request('GET', 'contents/README.md');
$readmeData = $readmeResponse->toArray();
if (!isset($readmeData['download_url'])) {
    throw new RuntimeException('Missing "download_url" key in README data.');
}

$readmeContent = $httpClient->request('GET', $readmeData['download_url'])->getContent();
if (!preg_match('/^##\s+Instal.*?(?=^##?\s)/ims', $readmeContent, $matches)) {
    throw new RuntimeException('Installation section not found in README.');
}

$installationContent = ltrim($matches[0]);
$installationContent = preg_replace('/\[!\[.*?\]\(.*?\)\]\(.*?\)/', '', $installationContent); // Remove badges
$installationContent = preg_replace('/<!--.*?-->/s', '', $installationContent); // Remove HTML comments
$installationContent = trim($installationContent);

// Save installation content
$fs->dumpFile($docsDir.'/installation.md', $installationContent);

// Update docs directory (DO NOT DELETE existing files)
$fs->mirror($tmpDir.'/docs', $docsDir);
