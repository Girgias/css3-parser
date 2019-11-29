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
use Girgias\CSSParser\Lexer;
use Girgias\CSSParser\SpecificationCompliantInputStream;
use Girgias\CSSParser\Tokens\BadUrl;
use Girgias\CSSParser\Tokens\Semicolon;
use Girgias\CSSParser\Tokens\Url;
use Girgias\CSSParser\Tokens\Whitespace;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class LexerUrlConsumeTest extends TestCase
{
    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeIndentLikeToken
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     */
    public function testBasicUrl(): void
    {
        $lexer = $this->getLexer('url(http://example.com) ');
        $token = $lexer->readNext();
        self::assertInstanceOf(Url::class, $token);
        self::assertSame('http://example.com', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     */
    public function testBasicUrlWithWhitespaceInBetweenParenthesis(): void
    {
        $lexer = $this->getLexer("url(  \n http://example.com  \t ) ");
        $token = $lexer->readNext();
        self::assertInstanceOf(Url::class, $token);
        self::assertSame('http://example.com', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     */
    public function testUrlConsumeWithInvalidCodePoints(): void
    {
        $lexer = $this->getLexer('url(http://exam"ple.com) ');
        self::expectException(ParseError::class);
        self::expectExceptionMessage('Unexpected character " in URL on line 1:24');
        $lexer->readNext();

        $lexer = $this->getLexer("url(http://exam'ple.com) ");
        self::expectException(ParseError::class);
        self::expectExceptionMessage("Unexpected character ' in URL on line 1:24");
        $lexer->readNext();

        $lexer = $this->getLexer('url(http://exam(ple.com) ');
        self::expectException(ParseError::class);
        self::expectExceptionMessage('Unexpected character ( in URL on line 1:24');
        $lexer->readNext();

        $lexer = $this->getLexer("url(http://exam\vple.com) ");
        self::expectException(ParseError::class);
        self::expectExceptionMessage("Unexpected character \v in URL on line 1:24");
        ${$lexer}->readNext();
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     */
    public function testUrlConsumeWithCorrectEscapeSequence(): void
    {
        $lexer = $this->getLexer('url(http://exam\\"ple.com) ');
        $token = $lexer->readNext();
        self::assertInstanceOf(Url::class, $token);
        self::assertSame('http://exam"ple.com', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     */
    public function testUrlConsumeWithIncorrectEscapeSequence(): void
    {
        $lexer = $this->getLexer("url(http://exam\\\nple.com) ");
        self::expectException(ParseError::class);
        self::expectExceptionMessage('Bad escape sequence in URL on line 2:8');
        $lexer->readNext();
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     */
    public function testUrlConsumeWithEOF(): void
    {
        $lexer = $this->getLexer('url(http://example.com');
        self::expectException(ParseError::class);
        self::expectExceptionMessage('Encountered EOF during URL parsing on line 1:22');
        $lexer->readNext();
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     */
    public function testUrlConsumeWithEOFAfterWhitespace(): void
    {
        $lexer = $this->getLexer('url(http://example.com    ');
        self::expectException(ParseError::class);
        self::expectExceptionMessage('Encountered EOF during URL parsing on line 1:26');
        $lexer->readNext();
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeRemnantsOfBadUrl
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     */
    public function testUrlConsumeRemnantsWithCodePointAfterWhitespaceAndBeforeRightParenthesis(): void
    {
        $lexer = $this->getLexer('url(http://example.com   a  ) ');
        $token = $lexer->readNext();
        self::assertInstanceOf(BadUrl::class, $token);
        self::assertSame('http://example.com', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeRemnantsOfBadUrl
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     */
    public function testUrlConsumeRemnantsWithValidEscapedRightParenthesisSequence(): void
    {
        $lexer = $this->getLexer('url(http://example.com  before \\)  after  ); ');
        $token = $lexer->readNext();
        self::assertInstanceOf(BadUrl::class, $token);
        self::assertSame('http://example.com', $token->getValue());
        self::assertInstanceOf(Semicolon::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeRemnantsOfBadUrl
     * @covers \Girgias\CSSParser\CompleteLexer::consumeUrlToken
     */
    public function testUrlConsumeRemnantsWith2SuccessiveValidEscapedSequences(): void
    {
        $lexer = $this->getLexer('url(http://example.com  before \\b7\\d5  ); ');
        $token = $lexer->readNext();
        self::assertInstanceOf(BadUrl::class, $token);
        self::assertSame('http://example.com', $token->getValue());
        self::assertInstanceOf(Semicolon::class, $lexer->readNext());
    }

    private function getLexer(string $input): Lexer
    {
        return new CompleteLexer(new SpecificationCompliantInputStream($input));
    }
}
