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

use Girgias\CSSParser\Exception\ParseError;
use Girgias\CSSParser\SpecificationCompliantInputStream;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class InputStreamTest extends TestCase
{
    /**
     * @covers \Girgias\CSSParser\SpecificationCompliantInputStream::splitInputIntoCodePoints
     */
    public function testPreprocessing(): void
    {
        // Normal string
        $input = "Hello this is a totally normal string with no bogus output\nI indeed feel very normal\n\n";
        $inputStream = new SpecificationCompliantInputStream($input);
        $preprocessedStream = $this->getInputStreamCodePoints($inputStream);
        self::assertSame($input, $preprocessedStream);

        // Normalizing Line Feeds
        $input = "Windows\r\nCarriage Return\rFormFeed\ftext";
        $inputStream = new SpecificationCompliantInputStream($input);
        $preprocessedStream = $this->getInputStreamCodePoints($inputStream);
        self::assertSame(
            "Windows\nCarriage Return\nFormFeed\ntext",
            $preprocessedStream
        );

        // Nul byte transformation
        $input = "Has nul byte\0and unicode\u{0000}\0too for good measure";
        $inputStream = new SpecificationCompliantInputStream($input);
        $preprocessedStream = $this->getInputStreamCodePoints($inputStream);
        self::assertSame(
            'Has nul byte�and unicode��too for good measure',
            $preprocessedStream
        );

        // Keep non surrogate unicode code points
        $input = "This has only valid unicode code points \u{E000} \u{F45E} \u{D799} \u{AE45}";
        $inputStream = new SpecificationCompliantInputStream($input);
        $preprocessedStream = $this->getInputStreamCodePoints($inputStream);
        self::assertSame(
            'This has only valid unicode code points   힙 깅',
            $preprocessedStream
        );
    }

    /**
     * @covers \Girgias\CSSParser\SpecificationCompliantInputStream::removeSurrogateCodePoints
     */
    public function testRemoveSurrogateCodePointsDuringPreprocessing(): void
    {
        $input = "This is a stream \u{DCAE} \u{D846}contai\u{D800}ning surrogate\u{DF84}\u{D823}code poi\u{DFFF}nts.";
        $inputStream = new SpecificationCompliantInputStream($input);
        $preprocessedStream = $this->getInputStreamCodePoints($inputStream);
        self::assertSame(
            'This is a stream � �contai�ning surrogate��code poi�nts.',
            $preprocessedStream
        );
    }

    /**
     * @covers \Girgias\CSSParser\SpecificationCompliantInputStream::next
     */
    public function testNext(): void
    {
        $input = 'This some';
        $inputStream = new SpecificationCompliantInputStream($input);

        self::assertSame('T', $inputStream->next());
        self::assertSame('h', $inputStream->next());

        $input = 'これは猫です';
        $inputStream = new SpecificationCompliantInputStream($input);
        self::assertSame('こ', $inputStream->next());
        self::assertSame('れ', $inputStream->next());
        self::assertSame('は', $inputStream->next());
        self::assertSame('猫', $inputStream->next());
    }

    /**
     * @covers \Girgias\CSSParser\SpecificationCompliantInputStream::peek
     */
    public function testPeeking(): void
    {
        $input = 'This some';
        $inputStream = new SpecificationCompliantInputStream($input);

        self::assertSame('T', $inputStream->peek());
        self::assertSame('T', $inputStream->peek(0));
        self::assertSame('i', $inputStream->peek(2));

        $input = 'これは猫です';
        $inputStream = new SpecificationCompliantInputStream($input);

        self::assertSame('こ', $inputStream->peek());
        self::assertSame('こ', $inputStream->peek(0));
        self::assertSame('は', $inputStream->peek(2));
        self::assertSame('猫', $inputStream->peek(3));
    }

    /**
     * @depends testNext
     * @depends testPeeking
     * @covers \Girgias\CSSParser\SpecificationCompliantInputStream::isEndOfStream
     */
    public function testEndOfStream(): void
    {
        $input = '';
        $inputStream = new SpecificationCompliantInputStream($input);
        self::assertTrue($inputStream->isEndOfStream());

        $input = 'word';
        $inputStream = new SpecificationCompliantInputStream($input);
        self::assertFalse($inputStream->isEndOfStream());
        $inputStream->next();
        $inputStream->next();
        $inputStream->next();
        $inputStream->next();
        self::assertTrue($inputStream->isEndOfStream());
    }

    /**
     * @depends testNext
     * @covers \Girgias\CSSParser\SpecificationCompliantInputStream::error
     */
    public function testBasicError(): void
    {
        $input = 'Hello world';
        $inputStream = new SpecificationCompliantInputStream($input);
        for ($i = 0; $i < 5; ++$i) {
            $codePoint = $inputStream->next();
        }
        self::expectException(ParseError::class);
        $this->expectExceptionMessage('o on line 1:5');
        $inputStream->error($codePoint);
    }

    /**
     * @depends testBasicError
     * @covers \Girgias\CSSParser\SpecificationCompliantInputStream::error
     * @covers \Girgias\CSSParser\SpecificationCompliantInputStream::next
     */
    public function testErrorWithNewLines(): void
    {
        $input = "H\nel\nlo \nworld";
        $inputStream = new SpecificationCompliantInputStream($input);
        for ($i = 0; $i < 12; ++$i) {
            $codePoint = $inputStream->next();
        }
        self::expectException(ParseError::class);
        $this->expectExceptionMessage('r on line 4:3');
        $inputStream->error($codePoint);
    }

    private function getInputStreamCodePoints(SpecificationCompliantInputStream $inputStream): string
    {
        $reflectionClass = new \ReflectionClass($inputStream);

        $reflectionProperty = $reflectionClass->getProperty('stream');
        $reflectionProperty->setAccessible(true);

        return \implode('', $reflectionProperty->getValue($inputStream));
    }
}
