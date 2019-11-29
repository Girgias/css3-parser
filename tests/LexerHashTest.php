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
use Girgias\CSSParser\Tokens\Hash;
use Girgias\CSSParser\Tokens\Whitespace;
use PHPUnit\Framework\TestCase;

/**
 * Class LexerEscapeSequenceTest
 * This tests the behavior of the lexer when it encounters a hash sequence.
 *
 * @internal
 */
final class LexerHashTest extends TestCase
{
    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @covers \Girgias\CSSParser\Tokens\Hash
     */
    public function testUnrestrictedHash(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('#2unrestricted '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Hash::class, $token);
        self::assertSame(Hash::UNRESTRICTED, $token->getType());
        self::assertSame('2unrestricted', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @covers \Girgias\CSSParser\Tokens\Hash
     */
    public function testUnrestrictedHyphenStartHash(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('#-5unrestricted-hyphen '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Hash::class, $token);
        self::assertSame(Hash::UNRESTRICTED, $token->getType());
        self::assertSame('-5unrestricted-hyphen', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @covers \Girgias\CSSParser\Tokens\Hash
     */
    public function testSimpleIDHash(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('#simple '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Hash::class, $token);
        self::assertSame('simple', $token->getValue());
        self::assertSame(Hash::ID, $token->getType());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @covers \Girgias\CSSParser\Tokens\Hash
     */
    public function testIDHash(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('#normal-id '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Hash::class, $token);
        self::assertSame('normal-id', $token->getValue());
        self::assertSame(Hash::ID, $token->getType());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @covers \Girgias\CSSParser\Tokens\Hash
     */
    public function testIDIdentLikeHash(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('#-hyphen '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Hash::class, $token);
        self::assertSame('-hyphen', $token->getValue());
        self::assertSame(Hash::ID, $token->getType());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @covers \Girgias\CSSParser\Tokens\Hash
     */
    public function testIDVariableLikeHash(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('#--variable-like '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Hash::class, $token);
        self::assertSame('--variable-like', $token->getValue());
        self::assertSame(Hash::ID, $token->getType());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @covers \Girgias\CSSParser\Tokens\Hash
     */
    public function testEscapedNotNameCodePointID(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('#\\|pipe '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Hash::class, $token);
        self::assertSame('|pipe', $token->getValue());
        self::assertSame(Hash::ID, $token->getType());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @covers \Girgias\CSSParser\Tokens\Hash
     */
    public function testEscapedIDUnicodeHash(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('#\\猫 '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Hash::class, $token);
        self::assertSame('猫', $token->getValue());
        self::assertSame(Hash::ID, $token->getType());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @covers \Girgias\CSSParser\Tokens\Hash
     */
    public function testUnicodeIDHash(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('#猫 '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Hash::class, $token);
        self::assertSame('猫', $token->getValue());
        self::assertSame(Hash::ID, $token->getType());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }
}
