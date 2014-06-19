<?php

namespace Riimu\QuickShell;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Shell
{
    public function run()
    {
        $reader = new LineReader();
        $code = new CodeRunner();
        $encoder = new ValueEncoder();

        $reader->init();
        $continue = false;

        while (true) {
            $line = $reader->readLine($continue ? ' > ' : '>> ');

            if ($continue  === false && trim($line) === '') {
                continue;
            }

            $code->addLine($line);
            $continue = true;

            if ($code->isFinished()) {
                $value = $code->run();

                if ($value !== null) {
                    echo $encoder->encode($value) . PHP_EOL;
                }

                $code->clear();
                $continue = false;
            }
        }
    }
}
