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
     * Validate a raw array against a schema
     *
     * @param $schema callable[] Excepted data schema
     * @param $data array Raw array containing the data to validate
     * @throws ModelValidationException
     */
    public function validate($schema, $data) {
        foreach ($schema as $field => $validator) {
            $value = @$data[$field];
            if (!call_user_func($validator, $value)) {
                throw new ModelValidationException($field);
            }
        }
    }
}