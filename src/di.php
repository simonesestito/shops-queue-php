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

$_instancesCache = [];

/**
 * Get a singleton instance of an object by its class name
 *
 * It injects all the dependencies required in the constructor.
 * NOTE: The constructor must use types on parameters!
 * @param $name string Class name
 * @return object Instance of the given class
 */
function getInstanceOf($name) {
    global $_instancesCache;

    if (isset($_instancesCache[$name]))
        return $_instancesCache[$name];

    $class = new ReflectionClass($name);
    $constr = $class->getConstructor();
    $params = [];

    if ($constr != NULL)
        $params = $constr->getParameters();

    $paramValues = [];
    foreach ($params as $param) {
        $paramClass = $param->getClass()->name;
        array_push($paramValues, getInstanceOf($paramClass));
    }
    $instance = $class->newInstanceArgs($paramValues);

    $_instancesCache[$name] = $instance;
    return $instance;
}

/**
 * Provide an instance of a class instead of
 * creating it from constructor injection.
 *
 * @param $class string Class name of the object
 * @param $object object Instance of the given class
 */
function provideInstance($class, $object) {
    global $_instancesCache;
    $_instancesCache[$class] = $object;
}
