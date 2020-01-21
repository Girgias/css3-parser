# PHP CSS 3 Parser

[![pipeline status](https://gitlab.com/Girgias/php-css3-parser/badges/master/pipeline.svg)](https://gitlab.com/Girgias/php-css3-parser/commits/master)
[![coverage report](https://gitlab.com/Girgias/php-css3-parser/badges/master/coverage.svg)](https://gitlab.com/Girgias/php-css3-parser/commits/master)
[![Codeac.io report](https://static.codeac.io/badges/3-15605407.svg "Codeac.io")](https://app.codeac.io/gitlab/Girgias/php-css3-parser)

A CSS 3 Lexer and Parser written in PHP following the CSS Syntax Module Level 3
W3C Candidate Recommendation specification, revision 2019-07-16
(https://www.w3.org/TR/2019/CR-css-syntax-3-20190716/)

Current CSS Syntax revision located at: https://www.w3.org/TR/css-syntax-3/

## Installing

```shell
composer require girgias/css3-parser
```

## Features
### Tokens
All tokens returned by the lexers are an instance of:
```php
namespace Girgias\CSSParser\Tokens;

interface Token
{
    public function getValue(): string;
}
```

The Hash tokens posses an extra method ```public function getType(): int``` which provides information if the hash
is unrestricted (``Girgias\CSSParser\Tokens\Hash::UNRESTRICTED``) or an ID (``Girgias\CSSParser\Tokens\Hash::ID``).

The IntegerNumber and FloatNumber tokens expose a method to get access to the raw value of the token:
``public function getRawValue(): float|int``

### A CSS specification compliant input stream
```php
new Girgias\CSSParser\SpecificationCompliantInputStream (string $input)
```
Which splits the input string into UTF-8 code points and preprocess the input stream as required
  by the specification, also throws a ``Girgias\CSSParser\Exception\ParseError`` when a parse error
  arises as defined per the specification.

#### An input stream decorator
```php
new Girgias\CSSParser\LaxInputStream (InputStream $inputStream)
```
Which suppress any parse error which would be emitted by the provided InputStream.


### Lexer/Tokenizer interface
```php
namespace Girgias\CSSParser;

use Girgias\CSSParser\Tokens\Token;

interface Lexer
{
    public function readNext(): Token;
}
```

### A complete CSS Lexer/Tokenizer
```php
new Girgias\CSSParser\CompleteLexer implements Girgias\CSSParser\Lexer (InputStream $inputStream)
```
The core lexer which tokenizes the input stream into a token stream.

It also capable of returning unofficial ``Girgias\Tokens\Comment`` tokens which may be needed
for linters.

It also possesses the following method ``public function consumeComments(): void`` which allows to remove
any comments from the input stream passed to the ``CompleteLexer``.

### A CSS specification compliant Lexer/Tokenizer
```php
new Girgias\CSSParser\SpecificationCompliantLexer (CompleteLexer $inputStream)
```
A decorator class which automatically calls ``CompleteLexer->consumeComments()`` to remove the comments
from the input stream as defined per the CSS Specification section 3.2.

## Advanced usage
### Input stream interface
```php
namespace Girgias\CSSParser;

interface InputStream
{
    /**
     * Fetch the next code point from the input stream.
     */
    public function next(): string;

    /**
     * Peek at the next code point from the input stream.
     * 
     * @param int $lookAhead used to skip $lookAhead code points from the input stream before peeking.
     */
    public function peek(int $lookAhead = 0): string;

    /**
     * Inform if the next code point is the end of the stream.
     */
    public function isEndOfStream(): bool;

    /**
     * Emit a parse error.
     */
    public function error(string $message): void;
}
```

To implement custom InputStreams.

## Roadmap

 * Adding support for other character encodings as per section 3.2. of the specification
 (see: https://www.w3.org/TR/css-syntax-3/#input-byte-stream)
 * Adding the parser as defined per section 5.
 (see: https://www.w3.org/TR/css-syntax-3/#parsing)
 * Improving current test setup.

## Contributing

Contributions are warmly welcomed,
for more information please refer to the dedicated CONTRIBUTING page.

## Links

- Repository: https://gitlab.com/Girgias/php-css3-parser/
- Issue tracker: https://gitlab.com/Girgias/php-css3-parser/issues

## Licensing

The code in this project is licensed under MIT license.
