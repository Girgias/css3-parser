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
use Girgias\CSSParser\Tokens\Identifier;
use Girgias\CSSParser\Tokens\TFunction;
use Girgias\CSSParser\Tokens\Token;
use Girgias\CSSParser\Tokens\Whitespace;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class LexerIdentifierTest extends TestCase
{
    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeIndentLikeToken
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartIdentifier
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartNumber
     */
    public function testHyphenStartIdentifier(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('-hyphen '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame('-hyphen', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('--var-like '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame('--var-like', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeIndentLikeToken
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartIdentifier
     */
    public function testNormalFunctionToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('foo( 1 ) '));
        $token = $lexer->readNext();
        self::assertInstanceOf(TFunction::class, $token);
        self::assertSame('foo', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeIndentLikeToken
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartIdentifier
     */
    public function testFunctionLookLikeUrlToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('url("http://example.com") '));
        $token = $lexer->readNext();
        self::assertInstanceOf(TFunction::class, $token);
        self::assertSame('url', $token->getValue());
        self::assertInstanceOf(Token::class, $lexer->readNext());

        // 3 spaces before
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('url(   "http://example.com") '));
        $token = $lexer->readNext();
        self::assertInstanceOf(TFunction::class, $token);
        self::assertSame('url', $token->getValue());
        self::assertInstanceOf(Token::class, $lexer->readNext());

        // 4 spaces before
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('url(    "http://example.com") '));
        $token = $lexer->readNext();
        self::assertInstanceOf(TFunction::class, $token);
        self::assertSame('url', $token->getValue());
        self::assertInstanceOf(Token::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('url(\'http://example.com\') '));
        $token = $lexer->readNext();
        self::assertInstanceOf(TFunction::class, $token);
        self::assertSame('url', $token->getValue());
        self::assertInstanceOf(Token::class, $lexer->readNext());

        // 3 spaces before
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('url(   \'http://example.com\') '));
        $token = $lexer->readNext();
        self::assertInstanceOf(TFunction::class, $token);
        self::assertSame('url', $token->getValue());
        self::assertInstanceOf(Token::class, $lexer->readNext());

        // 4 spaces before
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('url(    \'http://example.com\') '));
        $token = $lexer->readNext();
        self::assertInstanceOf(TFunction::class, $token);
        self::assertSame('url', $token->getValue());
        self::assertInstanceOf(Token::class, $lexer->readNext());
    }
}
