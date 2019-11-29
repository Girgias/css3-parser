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
use Girgias\CSSParser\Tokens\Comment;
use Girgias\CSSParser\Tokens\Identifier;
use Girgias\CSSParser\Tokens\Whitespace;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class LexerCommentTest extends TestCase
{
    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeCommentToken
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     */
    public function testBasicComment(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('/* Comment */ '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Comment::class, $token);
        self::assertSame('/* Comment */', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeCommentToken
     */
    public function testDocComment(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('/** Comment */ '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Comment::class, $token);
        self::assertSame('/** Comment */', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeCommentToken
     */
    public function testCommentWithNonTerminatingAsterisks(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('/* Comment * * */ '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Comment::class, $token);
        self::assertSame('/* Comment * * */', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeCommentToken
     */
    public function testCommentWithNonTerminatingSolidus(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('/* Comment / / */ '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Comment::class, $token);
        self::assertSame('/* Comment / / */', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeCommentToken
     */
    public function test2Comments(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('/* Comment */ /* Comment2 */ '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Comment::class, $token);
        self::assertSame('/* Comment */', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
        $token = $lexer->readNext();
        self::assertInstanceOf(Comment::class, $token);
        self::assertSame('/* Comment2 */', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeCommentToken
     */
    public function test2CommentsSeparatedByAnotherNonWhitespaceToken(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('/* Comment */ identifier /* Comment2 */ '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Comment::class, $token);
        self::assertSame('/* Comment */', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
        self::assertInstanceOf(Identifier::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
        $token = $lexer->readNext();
        self::assertInstanceOf(Comment::class, $token);
        self::assertSame('/* Comment2 */', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeCommentToken
     */
    public function testUnfinishedCommentByEOF(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('/* Comment '));
        self::expectException(ParseError::class);
        self::expectExceptionMessage('Encountered EOF during comment parsing on line 1:11');
        $lexer->readNext();
    }
}
