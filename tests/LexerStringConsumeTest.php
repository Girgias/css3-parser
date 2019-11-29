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
use Girgias\CSSParser\Exception\ParseError;
use Girgias\CSSParser\SpecificationCompliantInputStream;
use Girgias\CSSParser\Tokens\TString;
use Girgias\CSSParser\Tokens\Whitespace;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class LexerStringConsumeTest extends TestCase
{
    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeString
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     */
    public function testBasicValidString(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('"Hello world" '));
        $token = $lexer->readNext();
        self::assertInstanceOf(TString::class, $token);
        self::assertSame('Hello world', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeString
     */
    public function testBasicValidStringWithEscapeSequence(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('"Hello \\4C ife" '));
        $token = $lexer->readNext();
        self::assertInstanceOf(TString::class, $token);
        self::assertSame('Hello Life', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeString
     */
    public function testBasicValidStringWithEscapedDelimiterSequence(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('"Hello \\" (quotation)" '));
        $token = $lexer->readNext();
        self::assertInstanceOf(TString::class, $token);
        self::assertSame('Hello " (quotation)', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeString
     */
    public function testBasicValidStringWithEscapedNewLineSequence(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream("'Hello \\\n(newline)' "));
        $token = $lexer->readNext();
        self::assertInstanceOf(TString::class, $token);
        self::assertSame('Hello (newline)', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeString
     */
    public function testInvalidStringNewLine(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream("'Hello \n World'"));
        self::expectException(ParseError::class);
        self::expectExceptionMessage('Encountered newline during string parsing on line 1:7');
        $lexer->readNext();
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeString
     */
    public function testInvalidStringEOF(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream("'Hello World  "));
        self::expectException(ParseError::class);
        self::expectExceptionMessage('Encountered EOF during string parsing on line 1:14');
        $lexer->readNext();
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeString
     */
    public function testInvalidStringEscapedEOF(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream("'Hello World \\"));
        self::expectException(ParseError::class);
        self::expectExceptionMessage('Encountered EOF during string parsing on line 1:14');
        $lexer->readNext();
    }
}
