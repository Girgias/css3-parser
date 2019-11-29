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
use Girgias\CSSParser\Tokens\Dimension;
use Girgias\CSSParser\Tokens\FloatNumber;
use Girgias\CSSParser\Tokens\IntegerNumber;
use Girgias\CSSParser\Tokens\Percentage;
use Girgias\CSSParser\Tokens\Whitespace;
use PHPUnit\Framework\TestCase;

/**
 * Tests numerical tokenisation of the CSS CompleteLexer
 * Handles TNumber (IntegerNumber and FloatNumber), Percentage and Dimension tokens.
 *
 * @internal
 */
final class LexerNumericalTest extends TestCase
{
    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeNumericToken
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartNumber
     * @covers \Girgias\CSSParser\CompleteLexer::numericRepresentation
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @covers \Girgias\CSSParser\CompleteLexer::readNumber
     * @covers \Girgias\CSSParser\Tokens\IntegerNumber
     */
    public function testInteger(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('45 '));
        $integerToken = $lexer->readNext();
        self::assertInstanceOf(IntegerNumber::class, $integerToken);
        self::assertSame(45, $integerToken->getRawValue());
        self::assertSame('45', $integerToken->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('+45 '));
        $integerToken = $lexer->readNext();
        self::assertInstanceOf(IntegerNumber::class, $integerToken);
        self::assertSame(45, $integerToken->getRawValue());
        self::assertSame('45', $integerToken->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('-45 '));
        $integerToken = $lexer->readNext();
        self::assertInstanceOf(IntegerNumber::class, $integerToken);
        self::assertSame(-45, $integerToken->getRawValue());
        self::assertSame('-45', $integerToken->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeNumericToken
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartNumber
     * @covers \Girgias\CSSParser\CompleteLexer::numericRepresentation
     * @covers \Girgias\CSSParser\CompleteLexer::readNext
     * @covers \Girgias\CSSParser\CompleteLexer::readNumber
     * @covers \Girgias\CSSParser\Tokens\FloatNumber
     */
    public function testBasicFloats(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('0.45 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(0.45, $floatToken->getRawValue());
        self::assertSame('0.45', $floatToken->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('.45 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(0.45, $floatToken->getRawValue());
        self::assertSame('0.45', $floatToken->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('+0.45 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(0.45, $floatToken->getRawValue());
        self::assertSame('0.45', $floatToken->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('+.45 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(0.45, $floatToken->getRawValue());
        self::assertSame('0.45', $floatToken->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('-0.45 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(-0.45, $floatToken->getRawValue());
        self::assertSame('-0.45', $floatToken->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('-.45 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(-0.45, $floatToken->getRawValue());
        self::assertSame('-0.45', $floatToken->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeNumericToken
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartNumber
     * @covers \Girgias\CSSParser\CompleteLexer::numericRepresentation
     * @covers \Girgias\CSSParser\CompleteLexer::readNumber
     * @covers \Girgias\CSSParser\Tokens\FloatNumber
     */
    public function testExponentFloats(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('53e5 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(5300000.0, $floatToken->getRawValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('53E5 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(5300000.0, $floatToken->getRawValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('68e-2 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(0.68, $floatToken->getRawValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('68E-2 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(0.68, $floatToken->getRawValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('.3245e3 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(324.5, $floatToken->getRawValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('.3245E3 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(324.5, $floatToken->getRawValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('27.2875e2 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(2728.75, $floatToken->getRawValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());

        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('27.2875E2 '));
        $floatToken = $lexer->readNext();
        self::assertInstanceOf(FloatNumber::class, $floatToken);
        self::assertSame(2728.75, $floatToken->getRawValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeNumericToken
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartNumber
     * @covers \Girgias\CSSParser\CompleteLexer::numericRepresentation
     * @covers \Girgias\CSSParser\CompleteLexer::readNumber
     * @covers \Girgias\CSSParser\Tokens\Percentage
     */
    public function testIntegerPercentage(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('75% '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Percentage::class, $token);
        self::assertSame('75', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeNumericToken
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartNumber
     * @covers \Girgias\CSSParser\CompleteLexer::numericRepresentation
     * @covers \Girgias\CSSParser\CompleteLexer::readNumber
     * @covers \Girgias\CSSParser\Tokens\Percentage
     */
    public function testFloatPercentage(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('12.5% '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Percentage::class, $token);
        self::assertSame('12.5', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeNumericToken
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartNumber
     * @covers \Girgias\CSSParser\CompleteLexer::numericRepresentation
     * @covers \Girgias\CSSParser\CompleteLexer::readNumber
     * @covers \Girgias\CSSParser\Tokens\Dimension
     */
    public function testIntegerDimension(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('75px '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Dimension::class, $token);
        self::assertSame('75px', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    /**
     * @covers \Girgias\CSSParser\CompleteLexer::consumeNumericToken
     * @covers \Girgias\CSSParser\CompleteLexer::is3CodePointCheckStartNumber
     * @covers \Girgias\CSSParser\CompleteLexer::numericRepresentation
     * @covers \Girgias\CSSParser\CompleteLexer::readNumber
     * @covers \Girgias\CSSParser\Tokens\Dimension
     */
    public function testFloatDimension(): void
    {
        $lexer = new CompleteLexer(new SpecificationCompliantInputStream('0.5s '));
        $token = $lexer->readNext();
        self::assertInstanceOf(Dimension::class, $token);
        self::assertSame('0.5s', $token->getValue());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }
}
