<?php

declare(strict_types=1);

namespace DevSly\Services;

/**
 * Code Analysis Service
 *
 * Provides code scanning and security analysis capabilities
 */
final class CodeAnalysis extends AbstractService
{
    /**
     * Scan Dockerfile for security and best practices
     *
     * Analyzes Dockerfile content and returns:
     * - Security vulnerabilities
     * - Best practice violations
     * - Optimization suggestions
     * - Image size optimization tips
     *
     * @param string $dockerfile Dockerfile content
     * @param array<string, mixed> $options Analysis options
     * @return array<string, mixed> Analysis results
     *
     * @example
     * ```php
     * $dockerfileContent = file_get_contents('./Dockerfile');
     * $analysis = $client->codeAnalysis()->scanDockerfile($dockerfileContent, [
     *     'severity_threshold' => 'medium',
     *     'include_suggestions' => true
     * ]);
     *
     * foreach ($analysis['issues'] as $issue) {
     *     echo "[{$issue['severity']}] {$issue['message']}\n";
     *     echo "Line: {$issue['line']}\n";
     *     echo "Fix: {$issue['suggestion']}\n";
     * }
     * ```
     */
    public function scanDockerfile(string $dockerfile, array $options = []): array
    {
        $params = ['dockerfile' => $dockerfile];

        if (isset($options['severity_threshold'])) {
            $params['severity_threshold'] = $options['severity_threshold'];
        }

        if (isset($options['include_suggestions'])) {
            $params['include_suggestions'] = $options['include_suggestions'];
        }

        if (isset($options['check_base_image'])) {
            $params['check_base_image'] = $options['check_base_image'];
        }

        return $this->httpClient->post('/api/code-analysis/dockerfile/scan', $params);
    }

    /**
     * Analyze Docker image layers
     *
     * @param string $imageName Docker image name (e.g., nginx:latest)
     * @return array<string, mixed> Layer analysis
     *
     * @example
     * ```php
     * $layers = $client->codeAnalysis()->analyzeDockerLayers('nginx:latest');
     * echo 'Total Size: ' . $layers['total_size'];
     * foreach ($layers['layers'] as $layer) {
     *     echo "{$layer['command']}: {$layer['size']}\n";
     * }
     * ```
     */
    public function analyzeDockerLayers(string $imageName): array
    {
        return $this->httpClient->post('/api/code-analysis/docker/layers', [
            'image' => $imageName,
        ]);
    }

    /**
     * Scan dependencies for vulnerabilities
     *
     * Supports multiple package managers:
     * - npm (package.json)
     * - composer (composer.json)
     * - pip (requirements.txt)
     * - maven (pom.xml)
     *
     * @param string $manifestContent Dependency manifest content
     * @param string $type Manifest type (npm, composer, pip, maven)
     * @return array<string, mixed> Vulnerability report
     *
     * @example
     * ```php
     * $packageJson = file_get_contents('./package.json');
     * $vulnerabilities = $client->codeAnalysis()->scanDependencies($packageJson, 'npm');
     *
     * foreach ($vulnerabilities['vulnerabilities'] as $vuln) {
     *     echo "{$vuln['package']}: {$vuln['severity']}\n";
     *     echo "CVE: {$vuln['cve']}\n";
     *     echo "Fixed in: {$vuln['fixed_version']}\n";
     * }
     * ```
     */
    public function scanDependencies(string $manifestContent, string $type): array
    {
        return $this->httpClient->post('/api/code-analysis/dependencies/scan', [
            'manifest' => $manifestContent,
            'type' => strtolower($type),
        ]);
    }

    /**
     * Analyze code quality metrics
     *
     * @param string $code Source code to analyze
     * @param string $language Programming language (php, javascript, python, etc.)
     * @param array<string, mixed> $options Analysis options
     * @return array<string, mixed> Quality metrics
     *
     * @example
     * ```php
     * $code = file_get_contents('./MyClass.php');
     * $metrics = $client->codeAnalysis()->analyzeCodeQuality($code, 'php', [
     *     'check_complexity' => true,
     *     'check_duplicates' => true
     * ]);
     *
     * echo 'Complexity: ' . $metrics['complexity']['cyclomatic'];
     * echo 'Lines of Code: ' . $metrics['loc'];
     * echo 'Duplicate Lines: ' . $metrics['duplicates'];
     * ```
     */
    public function analyzeCodeQuality(string $code, string $language, array $options = []): array
    {
        $params = [
            'code' => $code,
            'language' => strtolower($language),
        ];

        if (isset($options['check_complexity'])) {
            $params['check_complexity'] = $options['check_complexity'];
        }

        if (isset($options['check_duplicates'])) {
            $params['check_duplicates'] = $options['check_duplicates'];
        }

        if (isset($options['check_style'])) {
            $params['check_style'] = $options['check_style'];
        }

        return $this->httpClient->post('/api/code-analysis/quality', $params);
    }

    /**
     * Detect secrets in code
     *
     * Scans for:
     * - API keys
     * - Passwords
     * - Private keys
     * - Tokens
     * - Database credentials
     *
     * @param string $content Content to scan
     * @param array<string, mixed> $options Scan options
     * @return array<string, mixed> Detected secrets
     *
     * @example
     * ```php
     * $code = file_get_contents('./config.php');
     * $secrets = $client->codeAnalysis()->detectSecrets($code);
     *
     * foreach ($secrets['findings'] as $secret) {
     *     echo "[{$secret['type']}] Found at line {$secret['line']}\n";
     *     echo "Confidence: {$secret['confidence']}\n";
     * }
     * ```
     */
    public function detectSecrets(string $content, array $options = []): array
    {
        $params = ['content' => $content];

        if (isset($options['entropy_threshold'])) {
            $params['entropy_threshold'] = $options['entropy_threshold'];
        }

        if (isset($options['exclude_patterns'])) {
            $params['exclude_patterns'] = $options['exclude_patterns'];
        }

        return $this->httpClient->post('/api/code-analysis/secrets/detect', $params);
    }

    /**
     * Analyze API specification (OpenAPI/Swagger)
     *
     * @param string $spec API specification content (YAML or JSON)
     * @param string $format Specification format (openapi, swagger)
     * @return array<string, mixed> Analysis results
     *
     * @example
     * ```php
     * $openapi = file_get_contents('./openapi.yaml');
     * $analysis = $client->codeAnalysis()->analyzeApiSpec($openapi, 'openapi');
     *
     * echo 'Endpoints: ' . $analysis['endpoint_count'];
     * echo 'Security Issues: ' . count($analysis['security_issues']);
     * ```
     */
    public function analyzeApiSpec(string $spec, string $format = 'openapi'): array
    {
        return $this->httpClient->post('/api/code-analysis/api-spec/analyze', [
            'spec' => $spec,
            'format' => strtolower($format),
        ]);
    }

    /**
     * Check license compliance
     *
     * @param array<string> $licenses List of license identifiers
     * @param array<string, mixed> $options Check options
     * @return array<string, mixed> Compliance report
     *
     * @example
     * ```php
     * $licenses = ['MIT', 'Apache-2.0', 'GPL-3.0'];
     * $compliance = $client->codeAnalysis()->checkLicenseCompliance($licenses, [
     *     'allowed_licenses' => ['MIT', 'Apache-2.0'],
     *     'project_license' => 'MIT'
     * ]);
     *
     * foreach ($compliance['incompatible'] as $license) {
     *     echo "Incompatible: {$license['name']}\n";
     * }
     * ```
     */
    public function checkLicenseCompliance(array $licenses, array $options = []): array
    {
        $params = ['licenses' => $licenses];

        if (isset($options['allowed_licenses'])) {
            $params['allowed_licenses'] = $options['allowed_licenses'];
        }

        if (isset($options['project_license'])) {
            $params['project_license'] = $options['project_license'];
        }

        return $this->httpClient->post('/api/code-analysis/license/check', $params);
    }

    /**
     * Generate code documentation
     *
     * @param string $code Source code
     * @param string $language Programming language
     * @param string $format Output format (markdown, html, json)
     * @return array<string, mixed> Generated documentation
     *
     * @example
     * ```php
     * $code = file_get_contents('./MyClass.php');
     * $docs = $client->codeAnalysis()->generateDocumentation($code, 'php', 'markdown');
     * file_put_contents('./API.md', $docs['documentation']);
     * ```
     */
    public function generateDocumentation(string $code, string $language, string $format = 'markdown'): array
    {
        return $this->httpClient->post('/api/code-analysis/documentation/generate', [
            'code' => $code,
            'language' => strtolower($language),
            'format' => strtolower($format),
        ]);
    }
}
