<?php

namespace Riimu\QuickShell;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class LineReader
{
    private $pointer;

    public function init()
    {
        $this->pointer = fopen("php://stdin", "r");
    }

    public function readLine($prompt = '')
    {
        if ($prompt != '') {
            echo $prompt;
        }

        return fgets($this->pointer);
    }
}
