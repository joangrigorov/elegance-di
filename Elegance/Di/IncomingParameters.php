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
class Elegance_Di_IncomingParameters
{

    /**
     * Injector instance itself
     * 
     * @var Elegance_Di_ContextInterface
     */
    protected $_injector;

    /**
     * Parameter names
     * 
     * @var array
     */
    protected $_names = null;
    
    /**
     * Constructor
     * 
     * Sets parameters names and injector instance
     * 
     * @param array $names
     * @param Elegance_Di_ContextInterface $injector
     */
    public function __construct(array $names = array(), Elegance_Di_ContextInterface $injector)
    {
        $this->_names = $names;
        $this->_injector = $injector;
    }
    
    /**
     * Gets injector instance
     * 
     * @return Elegance_Di_ContextInterface
     */
    public function getInjector()
    {
        return $this->_injector;
    }

    /**
     * Fill parameter placeholders with values
     * 
     * @return Elegance_Di_ContextInterface
     */
    public function with()
    {
        $values = func_get_args();
        $this->getInjector()->useParameters(array_combine($this->names, $values));
        return $this->_injector;
    }
}