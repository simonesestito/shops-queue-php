<?php

class Validator {
    /**
     * Wrap an existing validator, accepting null values
     * @param $validator callable Validator to call if value is not null
     * @return callable
     */
    public static function optional(callable $validator): callable {
        return function ($value) use ($validator) {
            return $value === null || call_user_func($validator, $value);
        };
    }

    /**
     * Return an email validator
     * @return callable
     */
    public static final function isEmailAddress(): callable {
        return function ($value) {
            return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL);
        };
    }

    /**
     * Return a string validator with constraints on the string length
     * @param int $min
     * @param int $max
     * @return callable
     */
    public static final function isString(int $min = 1, int $max = 255): callable {
        return function ($value) use ($min, $max) {
            return is_string($value) && strlen($value) >= $min && strlen($value) <= $max;
        };
    }

    /**
     * Return an associative array validator
     * @param Validator $validator
     * @param $schema
     * @return callable
     */
    public static final function isSchema(Validator $validator, $schema): callable {
        return function ($value) use ($validator, $schema) {
            try {
                $validator->validate($schema, $value);
                return true;
            } catch (ModelValidationException $e) {
                return false;
            }
        };
    }

    /**
     * Validate a raw array against a schema
     *
     * @param $schema callable[] Excepted data schema
     * @param $data array Raw array containing the data to validate
     * @throws ModelValidationException
     */
    public function validate($schema, $data) {
        if ($data == null)
            throw new ModelValidationException('root');

        foreach ($schema as $field => $validator) {
            $value = @$data[$field];
            if (!call_user_func($validator, $value)) {
                throw new ModelValidationException($field);
            }
        }
    }
}