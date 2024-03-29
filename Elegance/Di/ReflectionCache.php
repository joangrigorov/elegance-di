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
class Elegance_Di_ReflectionCache
{

    /**
     * Interface implementations
     * 
     * @var array
     */
    protected $_implementationsOf = array();

    /**
     * Class interfaces
     * 
     * @var array
     */
    protected $_interfacesOf = array();

    /**
     * Reflections cache
     * 
     * @var array
     */
    protected $_reflections = array();

    /**
     * Subclasses
     * 
     * @var array
     */
    protected $_subclasses = array();

    /**
     * Classes parents
     * 
     * @var array
     */
    protected $_parents = array();

    /**
     * Update index
     */
    public function refresh()
    {
        $this->_buildIndex(array_diff(get_declared_classes(), $this->_indexed()));
        $this->_subclasses = array();
    }

    /**
     * Get implementation of given interface
     * 
     * @param string $interface
     * @return array
     */
    public function implementationsOf($interface)
    {
        
        return isset($this->_implementationsOf[$interface]) ? 
               $this->_implementationsOf[$interface] : 
               array();
    }

    /**
     * Get interfaces of given class
     * 
     * @param string $class
     * @return array
     */
    public function interfacesOf($class)
    {
        return isset($this->_interfacesOf[$class]) ? 
               $this->_interfacesOf[$class] : 
               array();
    }

    /**
     * Get subclasses of given class
     * 
     * @param string $class
     * @return array
     */
    public function concreteSubgraphOf($class)
    {
        if (!class_exists($class)) {
            return array();
        }
        if (!isset($this->_subclasses[$class])) {
            $this->_subclasses[$class] = $this->_isConcrete($class) ? array(
                $class
            ) : array();
            foreach ($this->_indexed() as $candidate) {
                if (is_subclass_of($candidate, $class) && $this->_isConcrete($candidate)) {
                    $this->_subclasses[$class][] = $candidate;
                }
            }
        }
        return $this->_subclasses[$class];
    }

    /**
     * Get parents of given class
     * 
     * @param string $class
     * @return array
     */
	public function parentsOf($class)
	{
		if (! isset($this->_parents[$class])) {
			$this->_parents[$class] = class_parents($class);
		}
		return $this->_parents[$class];
	}

	/**
	 * Get class reflection
	 * 
	 * @param string $class
	 * @return ReflectionClass
	 */
	public function getReflection($class)
	{
		if (! isset($this->_reflections[$class])) {
			$this->_reflections[$class] = new ReflectionClass($class);
		}
		return $this->_reflections[$class];
	}

	/**
	 * Is class concrete (it's concrete when it's not abstract)
	 * 
	 * @param string $class
	 * @return boolean
	 */
	protected function _isConcrete(string $class)
	{
		return !$this->getReflection($class)
		             ->isAbstract();
	}

	/**
	 * Get indexed interfaces
	 * 
	 * @return array
	 */
	protected function _indexed()
	{
		return array_keys($this->_interfacesOf);
	}

	/**
	 * Create index
	 * 
	 * @param array $classes
	 * @return void
	 */
	protected function _buildIndex(array $classes)
	{
		foreach ($classes as $class) {
			$interfaces = array_values(class_implements($class));
			$this->_interfacesOf[$class] = $interfaces;
			foreach ($interfaces as $interface) {
				$this->crossReference($interface, $class);
			}
		}
	}

	/**
	 * Register interface implementation
	 * 
	 * @param string $interface
	 * @param string $class
	 * @return void
	 */
	protected function crossReference($interface, $class)
	{
		if (! isset($this->_implementationsOf[$interface])) {
			$this->_implementationsOf[$interface] = array();
		}
		$this->_implementationsOf[$interface][] = $class;
		$this->_implementationsOf[$interface] = array_values(
				array_unique($this->_implementationsOf[$interface]));
	}
}