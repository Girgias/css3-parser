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
use Girgias\CSSParser\SpecificationCompliantInputStream;
use Girgias\CSSParser\Tokens\AtKeyword;
use Girgias\CSSParser\Tokens\CDC;
use Girgias\CSSParser\Tokens\CDO;
use Girgias\CSSParser\Tokens\Colon;
use Girgias\CSSParser\Tokens\Comma;
use Girgias\CSSParser\Tokens\Delimiter;
use Girgias\CSSParser\Tokens\EOF;
use Girgias\CSSParser\Tokens\LeftCurlyBracket;
use Girgias\CSSParser\Tokens\LeftParenthesis;
use Girgias\CSSParser\Tokens\LeftSquareBracket;
use Girgias\CSSParser\Tokens\RightCurlyBracket;
use Girgias\CSSParser\Tokens\RightParenthesis;
use Girgias\CSSParser\Tokens\RightSquareBracket;
use Girgias\CSSParser\Tokens\Semicolon;
use Girgias\CSSParser\Tokens\Whitespace;
use PHPUnit\Framework\TestCase;

/**
 * Class LexerBasicTest.
 *
 * @internal
 */
final class LexerBasicTest extends TestCase
{
    /**
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartIdentifier
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartNumber
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     */
    public function testEOFToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream(''));
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testEOFToken
     */
    public function testWhitespaceToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream("   \t     \n   "));
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testWhitespaceToken
     */
    public function testColonToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream(': '));
        self::assertInstanceOf(Colon::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testWhitespaceToken
     */
    public function testSemiColonToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('; '));
        self::assertInstanceOf(Semicolon::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testWhitespaceToken
     */
    public function testCommaToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream(', '));
        self::assertInstanceOf(Comma::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartIdentifier
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testWhitespaceToken
     */
    public function testCDCToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('--> '));
        self::assertInstanceOf(CDC::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testWhitespaceToken
     */
    public function testCDOToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('<!-- '));
        self::assertInstanceOf(CDO::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @depends testWhitespaceToken
     */
    public function testLeftCurlyBracketToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('{ '));
        self::assertInstanceOf(LeftCurlyBracket::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testWhitespaceToken
     */
    public function testRightCurlyBracketToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('} '));
        self::assertInstanceOf(RightCurlyBracket::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testWhitespaceToken
     */
    public function testLeftParenthesisToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('( '));
        self::assertInstanceOf(LeftParenthesis::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testWhitespaceToken
     */
    public function testRightParenthesisToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream(') '));
        self::assertInstanceOf(RightParenthesis::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testWhitespaceToken
     */
    public function testLeftSquareBracketToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('[ '));
        self::assertInstanceOf(LeftSquareBracket::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testWhitespaceToken
     */
    public function testRightSquareBracketToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('] '));
        self::assertInstanceOf(RightSquareBracket::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testWhitespaceToken
     */
    public function testAtKeyword(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('@name '));
        $token = $lexer->readNext();
        self::assertInstanceOf(AtKeyword::class, $token);
        self::assertSame('name', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('@media-query '));
        $token = $lexer->readNext();
        self::assertInstanceOf(AtKeyword::class, $token);
        self::assertSame('media-query', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('@-hyphen '));
        $token = $lexer->readNext();
        self::assertInstanceOf(AtKeyword::class, $token);
        self::assertSame('-hyphen', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('@--varStyle '));
        $token = $lexer->readNext();
        self::assertInstanceOf(AtKeyword::class, $token);
        self::assertSame('--varStyle', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * Delimiters tokens are also produced when it seems to be the start of another token but it isn't.
     *
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @depends testWhitespaceToken
     */
    public function testDelimiters(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('| '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Delimiter::class, $token);
        self::assertSame('|', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('# '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Delimiter::class, $token);
        self::assertSame('#', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('. '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Delimiter::class, $token);
        self::assertSame('.', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('- '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Delimiter::class, $token);
        self::assertSame('-', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('< '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Delimiter::class, $token);
        self::assertSame('<', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('@ '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Delimiter::class, $token);
        self::assertSame('@', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }
}
