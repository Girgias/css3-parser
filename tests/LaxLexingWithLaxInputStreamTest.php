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
use Girgias\CSSParser\LaxInputStream;
use Girgias\CSSParser\Lexer;
use Girgias\CSSParser\SpecificationCompliantInputStream;
use Girgias\CSSParser\Tokens\BadString;
use Girgias\CSSParser\Tokens\BadUrl;
use Girgias\CSSParser\Tokens\Comment;
use Girgias\CSSParser\Tokens\Delimiter;
use Girgias\CSSParser\Tokens\EOF;
use Girgias\CSSParser\Tokens\Identifier;
use Girgias\CSSParser\Tokens\TString;
use Girgias\CSSParser\Tokens\Url;
use Girgias\CSSParser\Tokens\Whitespace;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class LaxLexingWithLaxInputStreamTest extends TestCase
{
    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeCommentToken
     * @covers \Girgias\CSSParser\LaxInputStream
     */
    public function testUnfinishedCommentByEOF(): void
    {
        $lexer = $this->getLexer('/* Comment ');
        $token = $lexer->readNext();
        self::assertInstanceOf(Comment::class, $token);
        self::assertSame('/* Comment ', $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeEscapedCodePoint
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @covers \Girgias\CSSParser\LaxInputStream
     */
    public function testInvalidEscapeSequence(): void
    {
        $lexer = $this->getLexer("\\\n ");
        $token = $lexer->readNext();
        self::assertInstanceOf(Delimiter::class, $token);
        self::assertSame('\\', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeEscapedCodePoint
     * @covers \Girgias\CSSParser\LaxInputStream
     */
    public function testEscapeEOFSequence(): void
    {
        $lexer = $this->getLexer('\\');
        $token = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $token);
        self::assertSame(Definitions::REPLACEMENT_CHARACTER, $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeString
     * @covers \Girgias\CSSParser\LaxInputStream
     */
    public function testInvalidStringNewLine(): void
    {
        $lexer = $this->getLexer("'Hello \n World'");
        $token = $lexer->readNext();
        self::assertInstanceOf(BadString::class, $token);
        self::assertSame('Hello ', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
        self::assertInstanceOf(Identifier::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeString
     * @covers \Girgias\CSSParser\LaxInputStream
     */
    public function testInvalidStringEOF(): void
    {
        $lexer = $this->getLexer("'Hello World  ");
        $token = $lexer->readNext();
        self::assertInstanceOf(TString::class, $token);
        self::assertSame('Hello World  ', $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeString
     * @covers \Girgias\CSSParser\LaxInputStream
     */
    public function testInvalidStringEscapedEOF(): void
    {
        $lexer = $this->getLexer("'Hello World \\");
        $token = $lexer->readNext();
        self::assertInstanceOf(TString::class, $token);
        self::assertSame('Hello World ', $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeRemnantsOfBadUrl
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     * @covers \Girgias\CSSParser\LaxInputStream
     */
    public function testUrlConsumeWithInvalidCodePoints(): void
    {
        $lexer = $this->getLexer('url(http://exam"ple.com) ');
        $token = $lexer->readNext();
        self::assertInstanceOf(BadUrl::class, $token);
        self::assertSame('http://exam', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = $this->getLexer("url(http://exam'ple.com) ");
        $token = $lexer->readNext();
        self::assertInstanceOf(BadUrl::class, $token);
        self::assertSame('http://exam', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = $this->getLexer('url(http://exam(ple.com) ');
        $token = $lexer->readNext();
        self::assertInstanceOf(BadUrl::class, $token);
        self::assertSame('http://exam', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = $this->getLexer("url(http://exam\vple.com) ");
        $token = $lexer->readNext();
        self::assertInstanceOf(BadUrl::class, $token);
        self::assertSame('http://exam', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeRemnantsOfBadUrl
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     * @covers \Girgias\CSSParser\LaxInputStream
     */
    public function testUrlConsumeWithIncorrectEscapeSequence(): void
    {
        $lexer = $this->getLexer("url(http://exam\\\nple.com) ");
        $token = $lexer->readNext();
        self::assertInstanceOf(BadUrl::class, $token);
        self::assertSame('http://exam', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     * @covers \Girgias\CSSParser\LaxInputStream
     */
    public function testUrlConsumeWithEOF(): void
    {
        $lexer = $this->getLexer('url(http://example.com');
        $token = $lexer->readNext();
        self::assertInstanceOf(Url::class, $token);
        self::assertSame('http://example.com', $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     * @covers \Girgias\CSSParser\LaxInputStream
     */
    public function testUrlConsumeWithEOFAfterWhitespace(): void
    {
        $lexer = $this->getLexer('url(http://example.com    ');
        $token = $lexer->readNext();
        self::assertInstanceOf(Url::class, $token);
        self::assertSame('http://example.com', $token->getValue());
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    private function getLexer(string $input): Lexer
    {
        return new CompleteLexer(new LaxInputStream(new SpecificationCompliantInputStream($input)));
    }
}
