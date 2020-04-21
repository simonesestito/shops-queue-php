<?php

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
