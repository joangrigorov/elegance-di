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
 * @subpackage Lifecycle
 * @copyright  Copyright (c) 2012 Elegance Framework
 * @version    $Id$
 */

/**
 * @category   Elegance
 * @package    Elegance DI
 * @subpackage Lifecycle
 * @copyright  Copyright (c) 2012 Elegance Framework
 */
class Elegance_Di_Lifecycle_Reused implements Elegance_Di_LifecycleInterface
{
    
    /**
     * Class name
     *
     * @var string
     */
    protected $_class;
    
    /**
     * Single instance
     *
     * @var string
     */
    protected $_instance = null;
    
    /**
     * Constructor
     * 
     * Sets class name
     *
     * @param string $class
     */
    public function __construct($class)
    {
        $this->_class = $class;
    }
    
    /**
     * Check if this class fit
     *
     * @param array $candidates
     * @return boolean
     */
    public function isOneOf($candidates)
    {
        return in_array($this->_class, $candidates);
    }

    /**
     * Create/get instance
     *
     * @param array $dependencies
     * @return mixed
     */
    public function instantiate($dependencies)
    {
        if (null == $this->_instance) {
            $this->_instance = call_user_func_array(
                array(
                    new ReflectionClass($this->_class), 'newInstance'
                ), 
                $dependencies
            );
        }
        return $this->_instance;
    }
}