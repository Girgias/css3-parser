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

namespace Tests\Tokens;

use Girgias\CSSParser\Tokens\CDC;
use Girgias\CSSParser\Tokens\CDO;
use Girgias\CSSParser\Tokens\Colon;
use Girgias\CSSParser\Tokens\Comma;
use Girgias\CSSParser\Tokens\EOF;
use Girgias\CSSParser\Tokens\LeftCurlyBracket;
use Girgias\CSSParser\Tokens\LeftParenthesis;
use Girgias\CSSParser\Tokens\LeftSquareBracket;
use Girgias\CSSParser\Tokens\RightCurlyBracket;
use Girgias\CSSParser\Tokens\RightParenthesis;
use Girgias\CSSParser\Tokens\RightSquareBracket;
use Girgias\CSSParser\Tokens\Semicolon;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ConstantTokensTest extends TestCase
{
    /**
     * @covers \Girgias\CSSParser\Tokens\EOF
     */
    public function testEOFToken(): void
    {
        self::assertSame('', (new EOF())->getValue());
    }

    /**
     * @covers \Girgias\CSSParser\Tokens\CDC
     */
    public function testCDCToken(): void
    {
        self::assertSame('-->', (new CDC())->getValue());
    }

    /**
     * @covers \Girgias\CSSParser\Tokens\CDO
     */
    public function testCDOToken(): void
    {
        self::assertSame('<!--', (new CDO())->getValue());
    }

    /**
     * @covers \Girgias\CSSParser\Tokens\Colon
     */
    public function testColonToken(): void
    {
        self::assertSame(':', (new Colon())->getValue());
    }

    /**
     * @covers \Girgias\CSSParser\Tokens\Comma
     */
    public function testCommaToken(): void
    {
        self::assertSame(',', (new Comma())->getValue());
    }

    /**
     * @covers \Girgias\CSSParser\Tokens\Semicolon
     */
    public function testSemicolonToken(): void
    {
        self::assertSame(';', (new Semicolon())->getValue());
    }

    /**
     * @covers \Girgias\CSSParser\Tokens\LeftCurlyBracket
     */
    public function testLeftCurlyBracket(): void
    {
        self::assertSame('{', (new LeftCurlyBracket())->getValue());
    }

    /**
     * @covers \Girgias\CSSParser\Tokens\LeftParenthesis
     */
    public function testLeftParenthesis(): void
    {
        self::assertSame('(', (new LeftParenthesis())->getValue());
    }

    /**
     * @covers \Girgias\CSSParser\Tokens\LeftSquareBracket
     */
    public function testLeftSquareBracket(): void
    {
        self::assertSame('[', (new LeftSquareBracket())->getValue());
    }

    /**
     * @covers \Girgias\CSSParser\Tokens\RightCurlyBracket
     */
    public function testRightCurlyBracket(): void
    {
        self::assertSame('}', (new RightCurlyBracket())->getValue());
    }

    /**
     * @covers \Girgias\CSSParser\Tokens\RightParenthesis
     */
    public function testRightParenthesis(): void
    {
        self::assertSame(')', (new RightParenthesis())->getValue());
    }

    /**
     * @covers \Girgias\CSSParser\Tokens\RightSquareBracket
     */
    public function testRightSquareBracket(): void
    {
        self::assertSame(']', (new RightSquareBracket())->getValue());
    }
}
