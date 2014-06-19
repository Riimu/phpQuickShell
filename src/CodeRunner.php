<?php

namespace Riimu\QuickShell;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CodeRunner
{
    private $code;
    private $analyzer;
    private $variables;

    public function __construct()
    {
        $this->analyzer = new CodeAnalyzer();
        $this->variables = [];
    }

    public function addLine($string)
    {
        $this->code .= $string;
        $this->analyzer->setCode($this->code);
    }

    public function isFinished()
    {
        return $this->analyzer->isClosed();
    }

    public function run()
    {
        ob_start();

        $value = $this->evaluateCode($this->analyzer->finalize());
        $output = ob_get_contents();
        ob_end_clean();

        if ($output !== '') {
            if (substr($output, -1) !== "\n") {
                $output .= PHP_EOL;
            }

            echo $output;
            return null;
        }

        return $value;
    }

    private function evaluateCode($codeToRun)
    {
        extract($this->variables);

        $previousResult = eval($codeToRun);

        $variables = get_defined_vars();
        unset($variables['codeToRun']);
        $this->variables = $variables;

        return $previousResult;
    }

    public function clear()
    {
        $this->code = '';
    }
}
