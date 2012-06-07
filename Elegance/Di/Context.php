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
class Elegance_Di_Context implements Elegance_Di_ContextInterface
{

    /**
     * The parent context
     * 
     * @var Elegance_Di_ContextInterface
     */
    protected $_parent;

    /**
     * 
     * @var Elegance_Di_ClassRepository
     */
    protected $_repository;

    /**
     * Registered preferences
     * 
     * @var multitype:Elegance_Di_LifecycleInterface
     */
    protected $_registry = array();
    
    /**
     * Container for variable contexts
     * 
     * @var mutlitype:Elegance_Di_Variable
     */
    protected $_variables = array();

    /**
     * Container for contexts
     * 
     * @var multitype:Elegance_Di_ContextInterface
     */
    protected $_contexts = array();

    /**
     * Container for types
     * 
     * @var multitype:Elegance_Di_Type
     */
    protected $_types = array();

    /**
     * Container for wrappers
     * 
     * @var array
     */
    protected $_wrappers = array();

    /**
     * Consructor
     * 
     * Sets the parent context
     * 
     * @param Elegance_Di_ContextInterface $parent
     */
    public function __construct(Elegance_Di_ContextInterface &$parent)
    {
        $this->_parent = $parent;
    }
    
    /**
     * Get parent context
     * 
     * @return Elegance_Di_ContextInterface
     */
    public function getParent()
    {
        return $this->_parent;
    }
    
    /**
     * Will use condition
     *
     * @param mixed $preference
     * @return void
     */
    public function willUse($preference)
    {
        if ($preference instanceof Elegance_Di_LifecycleInterface) {
            $lifecycle = $preference;
        } elseif (is_object($preference)) {
            $lifecycle = new Elegance_Di_Lifecycle_Value($preference);
        } else {
            $lifecycle = new Elegance_Di_Lifecycle_Factory($preference);
        }
        array_unshift($this->_registry, $lifecycle);
    }

    /**
     * Conditions for a variable
     *
     * @param string $name
     * @return Elegance_Di_Variable
     */
    public function forVariable($name)
    {
        return $this->_variables[$name] = new Elegance_Di_Variable($this);
    }

    /**
     * Get context when creating instance
     *
     * @param string $type
     * @return Elegance_Di_ContextInterface
     */
    public function whenCreating($type)
    {
        if (! isset($this->_contexts[$type])) {
            $this->_contexts[$type] = new self($this);
        }
        return $this->_contexts[$type];
    }

    /**
     * Get context for type
     *
     * @param string $type
     * @return Elegance_Di_Type
     */
    public function forType($type)
    {
        if (! isset($this->_types[$type])) {
            $this->_types[$type] = new Elegance_Di_Type();
        }
        return $this->_types[$type];
    }

    /**
     * Wrap with
     *
     * @param string $type
     * @return TO DO
     */
    public function wrapWith($type)
    {
        array_push($this->_wrappers, $type);
    }

    /**
     * Create new instance
     *
     * @param string $type Class to instantiate
     * @param array $nesting
     * @return mixed
     */
    public function create($type, $nesting = array())
    {
        class_exists($type, true);
        
        $lifecycle = $this->pickFactory($type, 
                $this->repository()
                    ->candidatesFor($type));
        
        $context = $this->determineContext($lifecycle->class);
        
        if ($context->hasWrapper($type, $nesting)) {
            $wrapper = $context->getWrapper($type, $nesting);
            return $this->create($wrapper, $this->cons($wrapper, $nesting));
        }
        
        $instance = $lifecycle->instantiate(
            $context->createDependencies(
                $this->repository()
                     ->getConstructorParameters($lifecycle->class), 
                $this->cons($lifecycle->class, $nesting)
            )
        );
        $this->invokeSetters($context, $nesting, $lifecycle->class, $instance);
        return $instance;
    }

    /**
     * Choose factory class
     *
     * @param string $type 
     * @param array $candidates
     * @return 
     */
    public function pickFactory($type, $candidates)
    {
        if (count($candidates) == 0) {
            throw new Elegance_Di_Exception('Cannot find implementation of ' . $type);
        } elseif ($this->hasPreference($candidates)) {
            return $this->preferFrom($candidates);
        } elseif (count($candidates) == 1) {
            return new Elegance_Di_Lifecycle_Factory($candidates[0]);
        } else {
            return $this->getParent()
                        ->pickFactory($type, $candidates);
        }
    }

    /**
     * Check for a wrapper class
     *
     * @param string $type 
     * @param array $alreadyApplied
     * @return boolean
     */
    public function hasWrapper($type, $alreadyApplied)
    {
        foreach ($this->wrappersFor($type) as $wrapper) {
            if (!in_array($wrapper, $alreadyApplied)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get wrapper class
     *
     * @param string $type 
     * @param array $alreadyApplied
     * @return mixed
     */
    public function getWrapper($type, $alreadyApplied)
    {
        foreach ($this->wrappersFor($type) as $wrapper) {
            if (!in_array($wrapper, $alreadyApplied)) {
                return $wrapper;
            }
        }
        return null;
    }

    /**
     * Invoke setter methods
     * 
     * Used for setter injection
     * 
     * @param Elegance_Di_ContextInterface $context Context to use
     * @param array $nesting
     * @param string $class
     * @param mixed $instance Instance, on which to invoke setters
     */
    protected function _invokeSetters(Elegance_Di_ContextInterface $context, $nesting, $class, $instance)
    {
        foreach ($context->_settersFor($class) as $setter) {
            
            $context->invoke(
                $instance, $setter, $context->createDependencies(
                    $this->repository()
                         ->getParameters($class, $setter), 
                    $this->cons($class, $nesting)
                )
            );
        }
    }

    /**
     * Get setter methods for the given class
     *
     * @param string $class
     * @return array
     */
    protected function _settersFor($class)
    {
        $reflection = new ReflectionClass($class);
        $interfaces = $reflection->getInterfaces();
        
        $interfaceSetters = array();
        
        foreach ($interfaces as $interface => $interfaceReflection) {
            if (isset($this->_types[$interface])) {
                $interfaceSetters = array_merge($interfaceSetters, 
                        $this->_types[$interface]->getSetters());
            }
        }
        
        $setters = isset($this->_types[$class]) ? $this->_types[$class]->getSetters() : array();
        return array_values(
            array_unique(
                array_merge(
                    $setters, 
                    $this->getParent()->_settersFor($class), 
                    $interfaceSetters
                )
            )
        );
    }


    /**
     * Get wrapper for type
     *
     * @param $type
     * @return array
     */
    public function wrappersFor($type)
    {
        return array_values(
            array_merge(
                $this->_wrappers, $this->getParent()->wrappersFor($type)
            )
        );
    }

    /**
     * Create dependencies
     *
     * @param array $parameters 
     * @param array $nesting
     * @return array
     */
    public function createDependencies($parameters, $nesting)
    {
        $values = array();
        foreach ($parameters as $parameter) {
            /* @var $parameter ReflectionParameter */
            try {
                $values[] = $this->_instantiateParameter($parameter, $nesting);
            } catch (Exception $e) {
                if ($parameter->isOptional()) {
                    break;
                }
                throw $e;
            }
        }
        
        return $values;
    }

    /**
     * Instantiate parameter
     *
     * @param ReflectionParameter $parameter 
     * @param array $nesting
     * @return mixed
     */
    public function instantiateParameter(ReflectionParameter $parameter, $nesting)
    {
        if ($hint = $parameter->getClass()) {
            if (array_key_exists($parameter->getName(), $this->_variables)) {
                if ($this->_variables[$parameter->getName()]->getPreference() instanceof Elegance_Di_LifecycleInterface) {
                    return $this->_variables[$parameter->getName()]->getPreference()->instantiate(
                            array());
                } elseif (!is_string(
                        $this->_variables[$parameter->getName()]->getPreference())) {
                    return $this->_variables[$parameter->getName()]->getPreference();
                }
            }
            return $this->create($hint->getName(), $nesting);
        } elseif (isset($this->_variables[$parameter->getName()])) {
            if ($this->_variables[$parameter->getName()]->getPreference() instanceof Elegance_Di_LifecycleInterface) {
                return $this->_variables[$parameter->getName()]->getPreference()->instantiate(
                        array());
            } elseif (!is_string(
                    $this->_variables[$parameter->getName()]->getPreference())) {
                return $this->_variables[$parameter->getName()]->getPreference();
            }
            return $this->create(
                    $this->_variables[$parameter->getName()]->getPreference(),
                    $nesting);
        }
        return $this->getParent()->instantiateParameter($parameter, $nesting);
    }


    /**
     * Determine context
     *
     * @param string $class
     * @return Elegance_Di_ContextInterface
     */
    protected function _determineContext($class)
    {
        foreach ($this->_contexts as $type => $context) {
            /* @var $context Elegance_Di_ContextInterface */
            if ($this->getRepository()->isSupertype($class, $type)) {
                return $context;
            }
        }
        return $this;
    }

    /**
     * Invoke object's method
     *
     * @param mixed $instance
     * @param string $method
     * @param array $arguments
     * @return void
     */
    protected function _invoke($instance, $method, $arguments)
    {
        call_user_func_array(array(
            $instance, $method
        ), $arguments);
    }

    /**
     * Prefer type from given candidates
     *
     * @param array $candidates
     * @return Elegance_Di_LifecycleInterface|false
     */
    public function preferFrom($candidates)
    {
        foreach ($this->_registry as $preference) {
            /* @var $preference Elegance_Di_LifecycleInterface */
            if ($preference->isOneOf($candidates)) {
                return $preference;
            }
        }
        return false;
    }

    /**
     * Prefer type from given candidates
     *
     * @param array $candidates
     * @return boolean
     */
    public function hasPreference($candidates)
    {
        foreach ($this->_registry as $preference) {
            /* @var $preference Elegance_Di_LifecycleInterface */
            if ($preference->isOneOf($candidates)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $head
     * @param array $tail
     * @return array
     */
    protected function _cons($head, $tail)
    {
        array_unshift($tail, $head);
        return $tail;
    }

    /**
     * Get class repository
     *
     * @return Elegance_Di_ClassRepository
     */
    public function getRepository()
    {
        return $this->getParent()->getRepository();
    }
}