<?php

/**
 * MIT License.
 *
 * Copyright (c) 2019 George Peter Banyard
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

declare(strict_types=1);

namespace Tests;

use Girgias\CSSParser\CompleteLexer;
use Girgias\CSSParser\Definitions;
use Girgias\CSSParser\Exception\ParseError;
use Girgias\CSSParser\SpecificationCompliantInputStream;
use Girgias\CSSParser\Tokens\EOF;
use Girgias\CSSParser\Tokens\Identifier;
use Girgias\CSSParser\Tokens\Whitespace;
use PHPUnit\Framework\TestCase;

/**
 * Class LexerEscapeSequenceTest
 * This tests the behavior of the lexer when it encounters an escape sequence.
 *
 * @internal
 */
final class LexerEscapeSequenceTest extends TestCase
{
    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeEscapedCodePoint
     * @covers \Girgias\CSSParser\CompleteLexer::is2CodePointCheckValidEscape
     */
    public function testEscapeEscapeSequence(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\\\ '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame('\\', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeEscapedCodePoint
     * @covers \Girgias\CSSParser\CompleteLexer::consumeName
     * @covers \Girgias\CSSParser\CompleteLexer::is2CodePointCheckValidEscape
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartIdentifier
     */
    public function testNonNameCodePointEscapeSequence(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\| '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame('|', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeEscapedCodePoint
     * @covers \Girgias\CSSParser\CompleteLexer::is2CodePointCheckValidEscape
     */
    public function testValidHexEscapeSequence(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\2A798 '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame('𪞘', $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeEscapedCodePoint
     * @covers \Girgias\CSSParser\CompleteLexer::is2CodePointCheckValidEscape
     */
    public function testSurrogateHexSequence(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\DCAE '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame(Definitions::REPLACEMENT_CHARACTER, $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\DF84 '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame(Definitions::REPLACEMENT_CHARACTER, $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeEscapedCodePoint
     * @covers \Girgias\CSSParser\CompleteLexer::is2CodePointCheckValidEscape
     */
    public function testNulByteSequence(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\0 '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame(Definitions::REPLACEMENT_CHARACTER, $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\00 '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame(Definitions::REPLACEMENT_CHARACTER, $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\000 '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame(Definitions::REPLACEMENT_CHARACTER, $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\0000 '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame(Definitions::REPLACEMENT_CHARACTER, $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeEscapedCodePoint
     * @covers \Girgias\CSSParser\CompleteLexer::is2CodePointCheckValidEscape
     */
    public function testHexAboveMaxCodePoint(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\110000 '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame(Definitions::REPLACEMENT_CHARACTER, $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\2A40E0 '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame(Definitions::REPLACEMENT_CHARACTER, $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeEscapedCodePoint
     * @covers \Girgias\CSSParser\CompleteLexer::is2CodePointCheckValidEscape
     */
    public function testMoreThan6HexCodePointsSequence(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\10abcde '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        // Does not consume the 'e' and appends it to the Identifier token
        self::assertSame("\u{10abcd}e", $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeEscapedCodePoint
     * @covers \Girgias\CSSParser\CompleteLexer::is2CodePointCheckValidEscape
     */
    public function testInvalidEscapeSequence(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream("\\\n "));
        self::expectException(ParseError::class);
        self::expectExceptionMessage('Bad escape sequence on line 1:1');
        $lexer->readNext();
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeEscapedCodePoint
     * @covers \Girgias\CSSParser\CompleteLexer::is2CodePointCheckValidEscape
     */
    public function testEscapeEOFSequence(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('\\'));
        self::expectException(ParseError::class);
        self::expectExceptionMessage('Cannot escape EOF on line 1:1');
        $lexer->readNext();
    }
}
