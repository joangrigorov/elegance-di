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
 * @subpackage Configuration
 * @copyright  Copyright (c) 2012 Elegance Framework
 * @version    $Id$
 */

/**
 * Interface for phemto configuration entities
 * 
 * @category   Elegance
 * @package    Elegance DI
 * @subpackage Configuration
 * @copyright  Copyright (c) 2012 Elegance Framework
 */
interface Elegance_Di_ConfigInterface
{
    
    /**
     * Configuration method
     * 
     * @param Phemto $injector
     */
    public function configure(Phemto $injector);
}