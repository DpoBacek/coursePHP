<?php
class Logger {
    private $logFile;
    private $minLevel;
    private $levels = [
        'DEBUG' => 1,
        'INFO' => 2,
        'NOTICE' => 3,
        'WARNING' => 4,
        'ERROR' => 5,
        'CRITICAL' => 6,
        'ALERT' => 7,
        'EMERGENCY' => 8
    ];
    
    public function __construct(string $logFile = 'app.log', string $minLevel = 'DEBUG') {
        $this->logFile = __DIR__ . '/../../storage/logs/' . $logFile;
        $this->minLevel = $this->levels[strtoupper($minLevel)] ?? $this->levels['DEBUG'];
        
        // Создаем директорию для логов, если нужно
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
    }
    
    public function log(string $message, string $level = 'INFO', array $context = []) {
        $levelCode = $this->levels[strtoupper($level)] ?? $this->levels['INFO'];
        
        // Фильтрация по уровню
        if ($levelCode < $this->minLevel) {
            return;
        }
        
        // Форматирование сообщения
        $formattedMessage = $this->formatMessage($message, $level, $context);
        
        // Запись в файл
        file_put_contents($this->logFile, $formattedMessage, FILE_APPEND);
    }
    
    private function formatMessage(string $message, string $level, array $context): string {
        $timestamp = date('[Y-m-d H:i:s]');
        $level = strtoupper($level);
        
        // Замена контекстных переменных
        foreach ($context as $key => $value) {
            $placeholder = '{' . $key . '}';
            if (strpos($message, $placeholder) !== false) {
                $message = str_replace($placeholder, $this->convertToString($value), $message);
            }
        }
        
        // Формирование строки лога
        $logLine = "$timestamp $level: $message";
        
        // Добавление бектрейса для ошибок
        if ($levelCode >= $this->levels['ERROR']) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            if (count($backtrace) > 2) {
                $caller = $backtrace[2];
                $file = $caller['file'] ?? 'unknown';
                $line = $caller['line'] ?? 0;
                $logLine .= " [at $file:$line]";
            }
        }
        
        return $logLine . PHP_EOL;
    }
    
    private function convertToString($value): string {
        if (is_scalar($value) || $value === null) {
            return (string)$value;
        }
        
        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string)$value;
            }
            return 'Object(' . get_class($value) . ')';
        }
        
        if (is_array($value)) {
            return 'Array[' . count($value) . ']';
        }
        
        return 'UnknownType';
    }
    
    // Методы для разных уровней логирования
    public function debug(string $message, array $context = []) {
        $this->log($message, 'DEBUG', $context);
    }
    
    public function info(string $message, array $context = []) {
        $this->log($message, 'INFO', $context);
    }
    
    public function warning(string $message, array $context = []) {
        $this->log($message, 'WARNING', $context);
    }
    
    public function error(string $message, array $context = []) {
        $this->log($message, 'ERROR', $context);
    }
    
    public function critical(string $message, array $context = []) {
        $this->log($message, 'CRITICAL', $context);
    }
}