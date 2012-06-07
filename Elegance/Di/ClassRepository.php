<?php
/**
 * Elegance Framework ©
 * Copyright © 2012 Elegance Team http://elegance.bg
 *
 * LICENSE
 *
 * A copy of this license is bundled with this package in the file LICENSE
 *
 * Copyright © Elegance Framework
 *
 * Platform that uses this site is protected by copyright.
 * It is provided solely for the use of this site and all its copying,
 * processing or use of parts thereof is prohibited and pursued by law.
 *
 * All rights reserved. Contact: office@elegance.bg
 *
 * @category   Elegance
 * @package    Elegance DI
 * @copyright  Copyright (c) 2012 Elegance Framework
 * @version    $Id$
 */

/**
 * @category   Elegance
 * @package    Elegance DI
 * @copyright  Copyright (c) 2012 Elegance Framework
 */
class Elegance_Di_ClassRepository
{
    
    /**
     * Using reflection could be very expensive. Cache is necessary
     *
     * @var Elegance_Di_ReflectionCache
     */
    protected static $_reflection = null;

	/**
     * Constructor
     * 
     * Sets reflection cache instance
     */
    function __construct()
    {
        if (null === self::$_reflection) {
            self::$_reflection = new Elegance_Di_ReflectionCache();
        }
        self::$_reflection->refresh();
    }

	/**
     * Get candidates for the given type
     * 
     * @param string $interface
     * @return array
     */
    public function candidatesFor($interface)
    {
        self::$_reflection->refresh();
        
        return array_merge(self::$_reflection->concreteSubgraphOf($interface), 
                self::$_reflection->implementationsOf($interface));
    }

	/**
     * Check is class supertype of the given type
     * 
     * @param string $class
 	 * @param string $type
     * @return boolean
     */
    public function isSupertype($class, $type)
    {
        $supertypes = array_merge(
            array($class), 
            self::$_reflection->interfacesOf($class), 
            self::$_reflection->parentsOf($class)
        );
        return in_array($type, $supertypes);
    }

    /**
     * Get parameters in the constructor
     * 
     * @param string $class
     * @return array
     */
    public function getConstructorParameters($class)
    {
        $reflection = self::$_reflection->getReflection($class);
        $constructor = $reflection->getConstructor();
        if (empty($constructor)) {
            return array();
        }
        /* @var $constructor ReflectionMethod */
        return $constructor->getParameters();
    }

	/**
     * Get method parameters
     * 
     * @param string $class
     * @param string $method
     * @return array
     */
    public function getParameters($class, $method)
    {
        $reflection = self::$_reflection->getReflection($class);
        if (!$reflection->hasMethod($method)) {
            throw new Elegance_Di_Exception("Setter method '$method' not found in '$class'");
        }
        
        return $reflection->getMethod($method)
                          ->getParameters();
    }
}