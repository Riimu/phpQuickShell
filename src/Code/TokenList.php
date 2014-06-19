<?php

namespace Riimu\QuickShell\Code;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class TokenList implements \SeekableIterator, \ArrayAccess, \Countable
{
    private $tokens;
    private $position;

    public function __construct($code)
    {
        $this->tokens = token_get_all("<?php $code");
        $this->position = 0;
    }

    public function addToken($token, $position = false)
    {
        if ($position === false) {
            $this->tokens[] = $token;
        } else {
            array_splice($this->tokens, $position, 0, [$token]);
        }
    }

    /**
     * @return TokenList
     */
    public function reverse()
    {
        $reversed = new TokenList('');
        $reversed->tokens = array_reverse($this->tokens);
        $reversed->position = count($this->tokens) - $this->position - 1;
        return $reversed;
    }

    public function seekNext($type)
    {
        $start = $this->position;

        while ($this->position < count($this->tokens)) {
            if ((new Token($this->tokens[$this->position]))->is($type)) {
                return $this->position;
            }

            $this->position++;
        }

        $this->position = $start;
        return false;
    }

    public function seekNextRecursive($type, $forward)
    {
        if ($type === false) {
            $this->tokens[] = false;
        }

        $pairs = [
            '}' => '{',
            ')' => '(',
            ']' => '[',
            '"' => '"',
            T_OPEN_TAG => T_CLOSE_TAG
        ];

        if ($forward) {
            $pairs = array_flip($pairs);
        }

        if ($this->seekNext(array_merge((array) $type, array_keys($pairs))) === false) {
            return false;
        }

        while (!$this->current()->is($type)) {
            $seek = $pairs[$this->current()->getType()];
            $this->next();

            if ($this->seekNextRecursive($seek, $forward) === false) {
                return false;
            }

            $this->next();

            if ($this->seekNext(array_merge((array) $type, array_keys($pairs))) === false) {
                return false;
            }
        }

        if ($type === false) {
            array_pop($this->tokens);
        }

        return true;
    }

    public function __toString()
    {
        $string = '';

        $first = array_shift($this->tokens);
        foreach ($this as $token) {
            $string .= (string) $token;
        }
        array_unshift($this->tokens, $first);

        return $string;
    }

    public function count()
    {
        return count($this->tokens);
    }

    public function seek($position)
    {
        $this->position = $position;
    }

    public function current()
    {
        return new Token($this->tokens[$this->position]);
    }

    public function next()
    {
        $this->position++;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return $this->position < count($this->tokens);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function offsetExists($offset)
    {
        return isset($this->tokens[$offset]);
    }

    public function offsetGet($offset)
    {
        return new Token($this->tokens[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        $this->tokens[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->tokens[$offset]);
    }
}
