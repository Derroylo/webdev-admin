<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class LogParserService
{
    private const MAX_LINES = 1000;

    public function __construct(
        #[Autowire(env: 'APACHE_ERROR_LOG_PATH')] private readonly string $apacheErrorLogPath,
        #[Autowire(env: 'APACHE_ACCESS_LOG_PATH')] private readonly string $apacheAccessLogPath,
        #[Autowire(env: 'PHP_ERROR_LOG_PATH')] private readonly string $phpErrorLogPath,
        #[Autowire(env: 'SYMFONY_LOG_PATH')] private readonly string $symfonyLogPath,
    ) {
    }

    public function getLogPath(string $type): string
    {
        return match ($type) {
            'apache-error'  => $this->apacheErrorLogPath,
            'apache-access' => $this->apacheAccessLogPath,
            'php'           => $this->phpErrorLogPath,
            'symfony'       => $this->symfonyLogPath,
            default         => throw new \InvalidArgumentException("Unknown log type: {$type}"),
        };
    }

    public function parseLogs(string $type): array
    {
        $logPath = $this->getLogPath($type);

        if (!file_exists($logPath)) {
            throw new \RuntimeException("Log file not found: {$logPath}");
        }

        if (!is_readable($logPath)) {
            throw new \RuntimeException("Log file is not readable: {$logPath}");
        }

        $lines = $this->readLastLines($logPath, self::MAX_LINES);

        return match ($type) {
            'apache-error'  => $this->parseApacheErrorLog($lines),
            'apache-access' => $this->parseApacheAccessLog($lines),
            'php'           => $this->parsePhpErrorLog($lines),
            'symfony'       => $this->parseSymfonyLog($lines),
            default         => [],
        };
    }

    public function getLogTypes(): array
    {
        return [
            'apache-error'  => 'Apache Error Log',
            'apache-access' => 'Apache Access Log',
            'php'           => 'PHP Error Log',
            'symfony'       => 'Symfony Log',
        ];
    }

    public function isLogAvailable(string $type): bool
    {
        try {
            $logPath = $this->getLogPath($type);

            return file_exists($logPath) && is_readable($logPath);
        } catch (\InvalidArgumentException) {
            return false;
        }
    }

    private function readLastLines(string $filePath, int $maxLines): array
    {
        $file = new \SplFileObject($filePath, 'r');
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key() + 1;

        $startLine = max(0, $totalLines - $maxLines);
        $lines     = [];

        $file->seek($startLine);
        while (!$file->eof()) {
            $line = $file->fgets();

            if ($line !== false && trim($line) !== '') {
                $lines[] = trim($line);
            }
        }

        return array_reverse($lines); // Most recent first
    }

    private function parseApacheErrorLog(array $lines): array
    {
        $logs = [];

        foreach ($lines as $line) {
            // Apache error log format: [Day Mon DD HH:MM:SS.mmmmmm YYYY] [module:level] [pid XXXXX] message
            if (preg_match('/^\[([^\]]+)\] \[([^\]]+)\] (?:\[pid \d+\])?\s*(.+)$/', $line, $matches)) {
                $logs[] = [
                    'timestamp' => $matches[1],
                    'level'     => $matches[2],
                    'message'   => $matches[3],
                    'raw'       => $line,
                ];
            } else {
                // Fallback for unparseable lines
                $logs[] = [
                    'timestamp' => '',
                    'level'     => 'unknown',
                    'message'   => $line,
                    'raw'       => $line,
                ];
            }
        }

        return $logs;
    }

    private function parseApacheAccessLog(array $lines): array
    {
        $logs = [];

        foreach ($lines as $line) {
            // Apache access log format: IP - - [timestamp] "METHOD URI PROTOCOL" status size
            if (preg_match('/^(\S+) \S+ \S+ \[([^\]]+)\] "(\S+) (\S+) (\S+)" (\d+) (\S+)/', $line, $matches)) {
                $logs[] = [
                    'ip'        => $matches[1],
                    'timestamp' => $matches[2],
                    'method'    => $matches[3],
                    'uri'       => $matches[4],
                    'protocol'  => $matches[5],
                    'status'    => (int) $matches[6],
                    'size'      => $matches[7],
                    'raw'       => $line,
                ];
            } else {
                // Fallback for unparseable lines
                $logs[] = [
                    'ip'        => '',
                    'timestamp' => '',
                    'method'    => '',
                    'uri'       => $line,
                    'protocol'  => '',
                    'status'    => 0,
                    'size'      => '',
                    'raw'       => $line,
                ];
            }
        }

        return $logs;
    }

    private function parsePhpErrorLog(array $lines): array
    {
        $logs = [];

        foreach ($lines as $line) {
            // PHP error log format: [DD-Mon-YYYY HH:MM:SS Timezone] PHP Type: message in /path/to/file on line XX
            if (preg_match('/^\[([^\]]+)\] PHP ([^:]+): (.+?) in (.+?) on line (\d+)$/', $line, $matches)) {
                $logs[] = [
                    'timestamp' => $matches[1],
                    'type'      => $matches[2],
                    'message'   => $matches[3],
                    'file'      => $matches[4],
                    'line'      => (int) $matches[5],
                    'raw'       => $line,
                ];
            } elseif (preg_match('/^\[([^\]]+)\] PHP ([^:]+): (.+)$/', $line, $matches)) {
                // Simpler format without file/line
                $logs[] = [
                    'timestamp' => $matches[1],
                    'type'      => $matches[2],
                    'message'   => $matches[3],
                    'file'      => '',
                    'line'      => 0,
                    'raw'       => $line,
                ];
            } else {
                // Fallback for unparseable lines
                $logs[] = [
                    'timestamp' => '',
                    'type'      => 'unknown',
                    'message'   => $line,
                    'file'      => '',
                    'line'      => 0,
                    'raw'       => $line,
                ];
            }
        }

        return $logs;
    }

    private function parseSymfonyLog(array $lines): array
    {
        $logs = [];

        foreach ($lines as $line) {
            // Symfony log format: [YYYY-MM-DD HH:MM:SS] channel.LEVEL: message {"context":"data"} []
            if (preg_match('/^\[([^\]]+)\] (\w+)\.(\w+): (.+?)(?:\s+(\{.+\}))?(?:\s+\[.+\])?$/', $line, $matches)) {
                $logs[] = [
                    'timestamp' => $matches[1],
                    'channel'   => $matches[2],
                    'level'     => $matches[3],
                    'message'   => $matches[4],
                    'context'   => $matches[5] ?? '',
                    'raw'       => $line,
                ];
            } else {
                // Fallback for unparseable lines
                $logs[] = [
                    'timestamp' => '',
                    'channel'   => 'unknown',
                    'level'     => 'unknown',
                    'message'   => $line,
                    'context'   => '',
                    'raw'       => $line,
                ];
            }
        }

        return $logs;
    }
}
