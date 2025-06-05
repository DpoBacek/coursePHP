<?php
class Validator {
    public static function validate(array $data, array $rules): array {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            foreach (explode('|', $fieldRules) as $rule) {
                $ruleParts = explode(':', $rule, 2);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;
                
                $method = 'validate' . ucfirst($ruleName);
                
                if (method_exists(self::class, $method)) {
                    $error = self::$method($field, $value, $ruleParam);
                    if ($error !== null) {
                        $errors[$field][] = $error;
                        break; // Останавливаем на первой ошибке для поля
                    }
                }
            }
        }
        
        return $errors;
    }
    
    private static function validateRequired(string $field, $value, $param): ?string {
        if (empty($value)) {
            return "Поле $field обязательно для заполнения";
        }
        return null;
    }
    
    private static function validateEmail(string $field, $value, $param): ?string {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return "Некорректный формат email";
        }
        return null;
    }
    
    private static function validateMin(string $field, $value, $param): ?string {
        if (is_string($value) && mb_strlen($value) < $param) {
            return "Минимальная длина поля $field: $param символов";
        }
        
        if (is_numeric($value) && $value < $param) {
            return "Значение поля $field должно быть не меньше $param";
        }
        
        return null;
    }
    
    private static function validateMax(string $field, $value, $param): ?string {
        if (is_string($value) && mb_strlen($value) > $param) {
            return "Максимальная длина поля $field: $param символов";
        }
        
        if (is_numeric($value) && $value > $param) {
            return "Значение поля $field должно быть не больше $param";
        }
        
        return null;
    }
    
    private static function validateUnique(string $field, $value, $param): ?string {
        [$table, $column] = explode(',', $param);
        $db = Database::getInstance();
        
        $result = $db->fetch(
            "SELECT COUNT(*) as count FROM $table WHERE $column = ?", 
            [$value]
        );
        
        if ($result && $result['count'] > 0) {
            return "Значение поля $field уже используется";
        }
        
        return null;
    }
    
    private static function validateConfirmed(string $field, $value, $param): ?string {
        $confirmationField = $field . '_confirmation';
        if (!isset($_POST[$confirmationField]) || $value !== $_POST[$confirmationField]) {
            return "Подтверждение поля $field не совпадает";
        }
        return null;
    }
    
    private static function validateNumeric(string $field, $value, $param): ?string {
        if (!is_numeric($value)) {
            return "Поле $field должно содержать число";
        }
        return null;
    }
    
    private static function validateInteger(string $field, $value, $param): ?string {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            return "Поле $field должно содержать целое число";
        }
        return null;
    }
    
    private static function validateIn(string $field, $value, $param): ?string {
        $allowedValues = explode(',', $param);
        if (!in_array($value, $allowedValues)) {
            return "Недопустимое значение для поля $field";
        }
        return null;
    }
}