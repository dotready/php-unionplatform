<?php

namespace phpunionplatform\Exception;

class PhpunionplatformException extends \Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
    }
}
