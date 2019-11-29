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

namespace Girgias\CSSParser;

use Dont\JustDont;

final class LaxInputStream implements InputStream
{
    use JustDont;

    private InputStream $inputStream;

    public function __construct(InputStream $inputStream)
    {
        $this->inputStream = $inputStream;
    }

    /**
     * Fetches the next code point in the input stream.
     */
    public function next(): string
    {
        return $this->inputStream->next();
    }

    /**
     * @param int $lookAhead used to look ahead in the stream of code points
     */
    public function peek(int $lookAhead = 0): string
    {
        return $this->inputStream->peek($lookAhead);
    }

    public function isEndOfStream(): bool
    {
        return $this->inputStream->isEndOfStream();
    }

    public function error(string $message): void
    {
    }
}
