<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DevSly\Client;
use DevSly\Exceptions\DevSlyException;

$client = new Client('your-api-key-here');

try {
    // Dockerfile Scanning
    echo "=== Dockerfile Scanning ===\n";
    $dockerfile = <<<'DOCKERFILE'
FROM ubuntu:latest
RUN apt-get update && apt-get install -y nginx
COPY . /app
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
DOCKERFILE;

    $analysis = $client->codeAnalysis()->scanDockerfile($dockerfile, [
        'severity_threshold' => 'medium',
        'include_suggestions' => true
    ]);

    echo "Issues Found: " . count($analysis['issues']) . "\n";
    foreach ($analysis['issues'] as $issue) {
        echo "[{$issue['severity']}] Line {$issue['line']}: {$issue['message']}\n";
        if (isset($issue['suggestion'])) {
            echo "  Fix: {$issue['suggestion']}\n";
        }
    }
    echo "\n";

    // Docker Layer Analysis
    echo "=== Docker Layer Analysis ===\n";
    $layers = $client->codeAnalysis()->analyzeDockerLayers('nginx:latest');
    echo "Total Size: {$layers['total_size']}\n";
    echo "Layers:\n";
    foreach ($layers['layers'] as $layer) {
        echo "  {$layer['size']}: {$layer['command']}\n";
    }
    echo "\n";

    // Dependency Scanning
    echo "=== Dependency Vulnerability Scanning ===\n";
    $packageJson = <<<'JSON'
{
  "dependencies": {
    "express": "4.17.1",
    "lodash": "4.17.15"
  }
}
JSON;

    $vulnerabilities = $client->codeAnalysis()->scanDependencies($packageJson, 'npm');
    echo "Vulnerabilities Found: " . count($vulnerabilities['vulnerabilities']) . "\n";
    foreach ($vulnerabilities['vulnerabilities'] as $vuln) {
        echo "[{$vuln['severity']}] {$vuln['package']}\n";
        echo "  CVE: {$vuln['cve']}\n";
        echo "  Current: {$vuln['current_version']}\n";
        echo "  Fixed in: {$vuln['fixed_version']}\n";
    }
    echo "\n";

    // Code Quality Analysis
    echo "=== Code Quality Analysis ===\n";
    $phpCode = <<<'PHP'
<?php
class Calculator {
    public function add($a, $b) {
        return $a + $b;
    }

    public function complexMethod($x, $y, $z) {
        if ($x > 0) {
            if ($y > 0) {
                if ($z > 0) {
                    return $x + $y + $z;
                }
            }
        }
        return 0;
    }
}
PHP;

    $metrics = $client->codeAnalysis()->analyzeCodeQuality($phpCode, 'php', [
        'check_complexity' => true,
        'check_duplicates' => true
    ]);

    echo "Lines of Code: {$metrics['loc']}\n";
    echo "Cyclomatic Complexity: {$metrics['complexity']['cyclomatic']}\n";
    echo "Maintainability Index: {$metrics['maintainability_index']}\n\n";

    // Secret Detection
    echo "=== Secret Detection ===\n";
    $codeWithSecrets = <<<'CODE'
<?php
$apiKey = "sk_live_1234567890abcdef";
$dbPassword = "SuperSecret123!";
$awsAccessKey = "AKIAIOSFODNN7EXAMPLE";
CODE;

    $secrets = $client->codeAnalysis()->detectSecrets($codeWithSecrets);
    echo "Secrets Found: " . count($secrets['findings']) . "\n";
    foreach ($secrets['findings'] as $secret) {
        echo "[{$secret['type']}] Line {$secret['line']}\n";
        echo "  Confidence: {$secret['confidence']}\n";
    }
    echo "\n";

    // API Spec Analysis
    echo "=== API Specification Analysis ===\n";
    $openapi = <<<'YAML'
openapi: 3.0.0
info:
  title: Sample API
  version: 1.0.0
paths:
  /users:
    get:
      summary: Get users
      responses:
        '200':
          description: Success
YAML;

    $apiAnalysis = $client->codeAnalysis()->analyzeApiSpec($openapi, 'openapi');
    echo "Endpoints: {$apiAnalysis['endpoint_count']}\n";
    echo "Security Issues: " . count($apiAnalysis['security_issues']) . "\n\n";

    // License Compliance
    echo "=== License Compliance ===\n";
    $licenses = ['MIT', 'Apache-2.0', 'GPL-3.0'];
    $compliance = $client->codeAnalysis()->checkLicenseCompliance($licenses, [
        'allowed_licenses' => ['MIT', 'Apache-2.0'],
        'project_license' => 'MIT'
    ]);

    echo "Compatible Licenses:\n";
    foreach ($compliance['compatible'] as $license) {
        echo "  - {$license['name']}\n";
    }

    if (!empty($compliance['incompatible'])) {
        echo "Incompatible Licenses:\n";
        foreach ($compliance['incompatible'] as $license) {
            echo "  - {$license['name']}: {$license['reason']}\n";
        }
    }
    echo "\n";

    // Documentation Generation
    echo "=== Documentation Generation ===\n";
    $classCode = <<<'PHP'
<?php
class UserManager {
    public function createUser(string $name, string $email): User {
        // Create user logic
    }

    public function deleteUser(int $userId): bool {
        // Delete user logic
    }
}
PHP;

    $docs = $client->codeAnalysis()->generateDocumentation($classCode, 'php', 'markdown');
    echo "Documentation generated:\n";
    echo substr($docs['documentation'], 0, 200) . "...\n";

} catch (DevSlyException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
