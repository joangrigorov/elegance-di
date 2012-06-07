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
interface Elegance_Di_ContextInterface
{
    
    /**
     * Will use condition
     *
     * @param mixed $preference
     * @return void
     */
    public function willUse($preference);
    
    /**
     * Conditions for a variable
     *
     * @param string $name
     * @return Elegance_Di_Variable
     */
    public function forVariable($name);

    /**
     * Get context when creating instance
     *
     * @param string $type
     * @return Elegance_Di_ContextInterface
     */
    public function whenCreating($type);

    /**
     * Get context for type
     *
     * @param string $type
     * @return Elegance_Di_Type
     */
    public function forType($type);

    /**
     * Get class repository
     *
     * @return Elegance_Di_ClassRepository
     */
    public function getRepository();

    /**
     * Choose factory class
     *
     * @param string $type 
     * @param array $candidates
     * @return 
     */
    public function pickFactory($type, $candidates);
    
    
    /**
     * Instantiate parameter
     *
     * @param ReflectionParameter $parameter
     * @param array $nesting
     * @return mixed
     */
    public function instantiateParameter(ReflectionParameter $parameter, $nesting);
    
}