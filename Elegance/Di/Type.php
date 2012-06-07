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
 * Lifecycle factories interface
 *
 * @category   Elegance
 * @package    Elegance DI
 * @copyright  Copyright (c) 2012 Elegance Framework
 */
class Elegance_Di_Type
{

    /**
     * Setter method
     * 
     * Will be called automatically when instantiating
     * 
     * @var array
     */
    protected $_setters = array();
    
    /**
     * Get setter methods
     * 
     * @return array
     */
    public function getSetters()
    {
        return $this->_setters;
    }
    
    /**
     * Sets setter method
     * 
     * @param string $method
     * @return Elegance_Di_Type
     */
    public function call($method)
    {
        array_unshift($this->_setters, $method);
        return $this;
    }
}