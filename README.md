# PHP Quick Shell #

PHP Quick Shell is an interactive shell that allows you to type PHP code using
a command line interface and to see the results of the executed code. While PHP
already offers a built in interactive mode through 'php -a', it's not ideal for
all purposes.

PHP Quick Shell differs from 'php -a' in following important ways:

  * PHP Quick Shell does not require readline support and thus works on Windows
  * Values from expressions are automatically returned and displayed
  * Statements are automatically ended with a semicolon

## Installation and usage ##

Installation and running the PHP Quick Shell is fairly simple. Just follow
the following instructions:

  1. Download the latest release from releases or clone the repository
  2. Install composer dependencies using `composer install`
  3. Run the phpshell via `php phpshell.php`

## Automatic value display ##

When you enter a expression in the shell, the script will automatically attempt
to return the value from the last statement by inserting a 'return' into the
code and running it.

For example, just by entering `1+1` and hitting enter, the shell will display
the result `2`, e.g.

```
>> 1+1
2
```

The return will only be inserted in the last statement in order to avoid
breaking the script in the middle; For example, the following code will work
just fine:

```
>> $a = 2; $a + $a
4
```

The shell will not insert a return automatically, however, if the last code
block is not an expression that can return a value. For example, the following
code will not automatically return a value:

```
>> if (true) 1+1
```

That does not mean you cannot return a value by yourself. The value will be
displayed if you use the following code:

```
>> if (true) return 1+1
2
```

### Different value types ###

The returned value will be displayed appropriately according to the type of the
value. For example:

```
>> 1
1
>> 1.0
1.0
>> true
true
>> [1,2]
[
  0 => 1,
  1 => 2
]
>> $obj = new stdClass; $obj->foo = 'bar'; $obj
object (stdClass) {
  foo : "bar"
}
>> "Text String"
"Text String"
>> imagecreate(10, 10)
resource (gd)
```

### Output in the shell ###

Even if the script returns a value for the shell to display, no value will be
outputted if the executed script itself provided output. For example:

```
>> 1+1
2
>> echo 1+1
2
>> var_dump(1+1)
int(2)
```

Note that the shell will ignore the last newline character in the output. Or
rather, if the output does not end in a newline character, it will be added
(in order to place the next input line on the next line). Thus, you can expect
following kind of output:

```
>> echo 1+1 . PHP_EOL . PHP_EOL
2

>> echo 1+1 . PHP_EOL
2
>> echo 1+1
2
```

## Automatic semicolons ##

The shell tries to intelligently determine when the expression can be ended
with a semicolon and automatically insert it and execute the code to return
the value.

It is still possible to enter multiline code, however. The code will not be
automatically finalized if your have any kind of open parentheses or unfinished
strings. For example:

```
>> function sum ($a, $b) {
 > return $a + $b;
 > }
>> sum(6, 7)
13
```

## Credits ##

PHP Quick Shell is copyright 2014 to Riikka Kalliomäki
