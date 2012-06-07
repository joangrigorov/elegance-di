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
 * Dependency injection container
 *
 * @category   Elegance
 * @package    Elegance DI
 * @copyright  Copyright (c) 2012 Elegance Framework
 */
class Elegance_Di_Container implements Elegance_Di_ContextInterface
{
    
    /**
     * Default top context
     * 
     * @var Elegance_Di_ContextInterface
     */
    protected $_top;
    
    /**
     * Named parameters to use for the next object instantiation
     * 
     * @var array
     */
    protected $_namedParameters = array();
    
    /**
     * Unamed parameters to use for the next object instantiation
     *
     * @var array
     */
    protected $_unnamedParameters = array();
    
    /**
     * Class repository
     * 
     * @var Elegance_Di_ClassRepository
     */
    protected $_repository;
    
    /**
     * Array with instances to use as shared objects
     * 
     * @var unknown_type
     */
    protected $_instances = array();
    
    /**
     * Array with aliases for the Service Locator
     * 
     * @var unknown_type
     */
    protected $_aliases = array();

    /**
     * Constructor
     * 
     * Creates the default top context
     * 
     * @param array $config Container configuration
     */
    public function __construct(array $config = null)
    {
        $this->_top = new Elegance_Di_Context($this);
        if (null !== $config) {
            $this->setConfig($config, $this);
        }
    }
    
    public function setConfig(array $config, Elegance_Di_ContextInterface $context)
    {    
        foreach ($config as $class => $settings) {
            // Get configuration for injection methods
            if (!empty($settings['call'])) {
                $type = $context->forType($class);
                foreach ($settings['call'] as $method) {
                    $type->call($method);
                }
            }
    
            if (!empty($settings['alias'])) {
                $context->registerAlias($class, $settings['alias']);
            }
    
            if (!empty($settings['shared']) && $settings['shared']) {
                class_exists($class, true);
                $context->willUse(new Elegance_Di_Lifecycle_Reused($class));
            }
    
            if (!empty($settings['params'])) {
                foreach ($settings['params'] as $param => $value) {
                    if (is_string($value)) {
                        $context->whenCreating($class)
                                ->forVariable($param)
                                ->useString($value);
                    } else {
                        $context->whenCreating($class)
                                ->forVariable($param)
                                ->willUse($value);
                    }
                }
            }
    
            if (isset($settings['instances'])) {
                $this->setConfig($settings['instances'], $context->whenCreating($class));
            }
    
        }
    }
    
    /**
     * Sets the top context container
     * 
     * @param Elegance_Di_ContextInterface $context
     * @return Elegance_Di_Container
     */
    public function setTop(Elegance_Di_ContextInterface $context)
    {
        $this->_top = $context;
        return $this;
    }
    
    /**
     * Gets the top context container
     * 
     * @return Elegance_Di_ContextInterface
     */
    public function getTop()
    {
        return $this->_top;
    }
    
    /**
     * Sets named parameters
     * 
     * @param array $parameters
     * @return Elegance_Di_Container
     */
    public function setNamedParameters(array $parameters)
    {
        $this->_namedParameters = $parameters;
        return $this;
    }
    
    /**
     * Gets named paramters
     * 
     * @return array
     */
    public function getNamedParameters()
    {
        return $this->_namedParameters;
    }
    
    /**
     * Sets unnamed parameters
     * 
     * @param array $parameters
     * @return Elegance_Di_Container
     */
    public function setUnnamedParameters(array $parameters)
    {
        $this->_unnamedParameters = $parameters;
        return $this;
    }
    
    /**
     * Gets unnamed paramters
     * 
     * @return array
     */
    public function getUnnamedParameters()
    {
        return $this->_unnamedParameters;
    }
    
    /**
     * Sets class repository instance
     * 
     * @param Elegance_Di_ClassRepository $repository
     * @return Elegance_Di_Container
     */
    public function setRepository(Elegance_Di_ClassRepository $repository)
    {
        $this->_repository = $repository;
        return $this;
    }
    
    /**
     * Gets class reposotory instance
     * 
     * @return Elegance_Di_ClassRepository
     */
    public function getRepository()
    {
        return $this->_repository;
    }
    
    /**
     * Value (could be anything) to use with object instantiations
     * 
     * @param mixed $preference
     * @return Elegance_Di_Container
     */
    public function willUse($preference)
    {
        $this->getTop()->willUse($preference);
        return $this;
    }

    /**
     * Conditions for a variable
     * 
     * @param string $name Variable name
     * @return Elegance_Di_Variable
     */
    public function forVariable($name)
    {
        return $this->getTop()->forVariable($name);
    }
    
    /**
     * Get context when creating instance
     *
     * @param string $type Class/interface name
     * @return Elegance_Di_Context
     */
    public function whenCreating($type)
    {
        return $this->getTop()->whenCreating($type);
    }

    /**
     * Get context for type
     *
     * @param string $type Class/interface name
     * @return Elegance_Di_Type
     */
    public function forType($type)
    {
        return $this->getTop()->forType($type);
    }

    /**
     * Create parameter placeholders
     * 
     * @return Elegance_Di_IncomingParameters
     */
    public function fill()
    {
        $names = func_get_args();
        return new Elegance_Di_IncomingParameters($names, $this);
    }

    /**
     * Fill parameter placeholders
     * 
     * @return Elegance_Di_Container
     */
    public function with()
    {
        $values = func_get_args();
        $this->setUnnamedParameters(array_merge(
            $this->_unnamedParameters, $values
        ));
        return $this;
    }

    /**
     * Create new instance
     * 
     * @return object
     */
    public function create()
    {
        $values = func_get_args();
        $type = array_shift($values);
        $this->setUnnamedParameters(array_merge(
            $this->_unnamedParameters, $values
        ));
        $this->setRepository(new Elegance_Di_ClassRepository());
        
        if (array_key_exists($type, $this->aliases)) {
            $type = $this->_aliases[$type];
        }
        
        $object = $this->getTop()->create($type);
        $this->setNamedParameters(array());
        return $object;
    }

    /**
     * Get instance as shared
     * 
     * @return object
     */
    public function get()
    {
        $values = func_get_args();
        $type = $values[0];
        
        if (array_key_exists($type, $this->_aliases)) {
            $type = $this->_aliases[$type];
        }
        
        if (!array_key_exists($type, $this->_instances) || count($values) > 1) {
            $this->_instances[$type] = call_user_func_array(array($this, 'create'), $values);
        }
        
        return $this->_instances[$type];
    }

    /**
     * Register service
     * 
     * @param string $className
     * @param string $alias
     * @throws Elegance_Di_Exception
     * @return Elegance_Di_Container
     */
    public function registerAlias($className, $alias)
    {
        if (class_exists($alias)) {
            throw new Elegance_Di_Exception("Class with name '$alias' you are trying to set as alias exists");
        }
        
        $this->_aliases[$alias] = $className;
        
        return $this;
    }

    /**
     * @throws Elegance_Di_Exception
     */
    public function pickFactory($type, $candidates)
    {
        throw new Elegance_Di_Exception('Cannot determine implementation of ' . $type);
    }

    /**
     * @param string $class
     * @return array
     */
    public function settersFor($class)
    {
        return array();
    }

    /**
     * @param string $type
     * @return array
     */
    public function wrappersFor($type)
    {
        return array();
    }

    /**
     * Set parameters for usage in the global context
     * 
     * @param array $parameters
     * @return Elegance_Di_Container
     */
    public function useParameters(array $parameters)
    {
        $this->setNamedParameters(array_merge(
            $this->_namedParameters, $parameters
        ));
        return $this;
    }

    /**
     * @param ReflectionParameter $parameter
     * @param boolean $nesting
     * @throws Elegance_Di_Exception
     * @return mixed
     */
    public function instantiateParameter(ReflectionParameter $parameter, $nesting)
    {
        if (isset($this->_namedParameters[$parameter->getName()])) {
            return $this->_namedParameters[$parameter->getName()];
        }
        if (!empty($this->_unnamedParameters)) {
            return array_shift($this->_unnamedParameters);
        }
        throw new Elegance_Di_Exception('Missing dependency with name ' . $parameter->getName());
    }
}