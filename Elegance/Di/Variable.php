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
class Elegance_Di_Variable
{
    
    /**
     * Preference factory
     * 
     * @var Elegance_Di_LifecycleInterface
     */
    protected $_preference;

    /**
     * 
     * @var Elegance_Di_ContextInterface
     */
    protected $_context;

    /**
     * Constructor
     * 
     * Sets context
     * 
     * @param Elegance_Di_ContextInterface $context
     */
    public function __construct(Elegance_Di_ContextInterface $context)
    {
        $this->_context = $context;
    }
    
    /**
     * Gets preference
     * 
     * @return Elegance_Di_LifecycleInterface
     */
    public function getPreference()
    {
        return $this->_preference;
    }

    /**
     * Will use condition
     * 
     * @param Elegance_Di_LifecycleInterface $preference
     * @return Elegance_Di_ContextInterface
     */
    public function willUse(Elegance_Di_LifecycleInterface $preference)
    {
        $this->_preference = $preference;
        return $this->_context;
    }

    /**
     * Use value
     * 
     * @param string $string
     * @return Elegance_Di_ContextInterface
     */
    public function useString($string)
    {
        $this->_preference = new Elegance_Di_Lifecycle_Value($string);
        return $this->_context;
    }
}