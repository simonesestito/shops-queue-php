<?php
/**
 * Copyright 2020 Simone Sestito
 * This file is part of Shops Queue.
 *
 * Shops Queue is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Shops Queue is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Shops Queue.  If not, see <http://www.gnu.org/licenses/>.
 */

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
     * Return a validator which checks if a value is valid against a filter.
     * @link https://php.net/manual/en/filter.constants.php
     * @param $filter int One of the filters of FILTER_VALIDATE_*
     * @return callable
     */
    public static function filterAs($filter): callable {
        return function ($value) use ($filter) {
            return filter_var($value, $filter) !== false;
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
     * Return a validator to check if the value is in a set of accepted ones
     * @param $values string[] Enum values
     * @return callable
     */
    public static function isIn($values) {
        return function ($value) use ($values) {
            return is_string($value) && in_array($value, $values);
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
     * Check if the given value is a non-empty array
     * @return callable
     */
    public static final function isNonEmptyArray(): callable {
        return function ($value) {
            return is_array($value) && count($value) > 0;
        };
    }

    /**
     * Check if the given value is a string and it's a valid EAN value
     * It doesn't perform checks against control code (last digit) yet
     * @return callable
     */
    public static final function isEan() {
        return function ($value) {
            if (!is_string($value))
                return false;

            if (strlen($value) != 13)
                return false;

            if (!is_numeric($value))
                return false;

            return true;
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
            $data = [];

        foreach ($schema as $field => $validator) {
            $value = @$data[$field];
            if (!call_user_func($validator, $value)) {
                throw new ModelValidationException($field);
            }
        }
    }
}