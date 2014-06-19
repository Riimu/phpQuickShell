<?php

namespace Riimu\QuickShell;
use Riimu\QuickShell\Code\TokenList;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class CodeAnalyzer
{
    /**
     * @var \Riimu\QuickShell\Code\TokenList
     */
    private $tokenList;
    private $returnPosition;

    public function setCode($code)
    {
        $this->tokenList = new Code\TokenList($code);
    }

    public function isClosed()
    {
        return $this->tokenList->seekNextRecursive(false, true) !== false;
    }

    public function finalize()
    {
        if (!$this->isTerminated()) {
            $this->terminate();
        }
        if (!$this->isReturned()) {
            $this->addReturn();
        }

        return (string) $this->tokenList;
    }

    private function isTerminated()
    {
        $this->tokenList->rewind();

        foreach ($this->tokenList->reverse() as $token) {
            if ($token->is([';', '}'])) {
                return true;
            } elseif (!$token->is(T_WHITESPACE)) {
                return false;
            }
        }

        return false;
    }

    private function terminate()
    {
        $this->tokenList->addToken(';');
    }

    private function isReturned()
    {
        $list = $this->tokenList->reverse();
        $list->rewind();

        if ($list->seekNext([';', '}']) === false || $list->current()->is('}')) {
            return true;
        }

        $list->next();
        if ($list->seekNextRecursive([';', T_OPEN_TAG], false) === false) {
            return true;
        }
        $list = $list->reverse();
        $list->next();

        $this->returnPosition = $list->key();

        while ($list->valid()) {
            $token = $list->current();
            $list->next();

            if ($token->is([
                T_ABSTRACT, T_CLASS, T_CONST, T_FUNCTION,
                T_RETURN, T_IF, T_WHILE, T_FOR, T_FOREACH, T_DO, T_REQUIRE,
                T_REQUIRE_ONCE, T_INCLUDE, T_INCLUDE_ONCE, T_ECHO, T_GLOBAL,
                T_TRY, T_EXIT, T_THROW
            ])) {
                return true;
            } elseif (!$token->is(T_WHITESPACE)) {
                return false;
            }
        }

        return false;
    }

    private function addReturn()
    {
        $this->tokenList->addToken([T_WHITESPACE, ' ', false], $this->returnPosition);
        $this->tokenList->addToken([T_RETURN, 'return', false], $this->returnPosition);
    }
}
