<?php

namespace Riimu\QuickShell\Code;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Token
{
    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function is($type)
    {
        return is_array($type) ? in_array($this->getType(), $type, true) : $this->getType() === $type;
    }

    public function getType()
    {
        return is_array($this->token) ? $this->token[0] : $this->token;
    }

    public function __toString()
    {
        return is_array($this->token) ? $this->token[1] : $this->token;
    }
}
