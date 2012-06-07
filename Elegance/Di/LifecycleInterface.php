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
 * Lifecycle factories interface
 *
 * @category   Elegance
 * @package    Elegance DI
 * @subpackage Lifecycle
 * @copyright  Copyright (c) 2012 Elegance Framework
 */
interface Elegance_Di_LifecycleInterface
{
    
    /**
     * Check if this class fit
     * 
     * @param array $candidates
     * @return boolean
     */
    public function isOneOf($candidates);

    /**
     * Instantiate class
     * 
     * @param string $dependencies
     * @return mixed
     */
    public function instantiate($dependencies);
}