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
use Girgias\CSSParser\Exception\ParseError;

/**
 * Class InputStream
 * This class is an implementation of section 3.2 of the CSS Syntax 3 specification.
 *
 * @see https://www.w3.org/TR/css-syntax-3/#input-byte-stream
 */
final class SpecificationCompliantInputStream implements InputStream
{
    use JustDont;

    private const EOS = '';
    /**
     * Taken from https://magp.ie/2011/01/06/remove-non-utf8-characters-from-string-with-php/.
     */
    private const IS_SURROGATES_CODE_POINTS = "/\xED[\xA0-\xBF][\x80-\xBF]/S";

    /**
     * @var array<int, string>
     */
    private array $stream = [];
    private int $position = 0;
    private int $line = 1;
    private int $column = 0;

    /**
     * InputStream constructor.
     * Preprocess string as defined per CSS spec then split into array of code points.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#input-preprocessing
     */
    public function __construct(string $input)
    {
        $input = \str_replace(["\r\n", "\r", "\f"], Definitions::NEWLINE, $input);
        $input = \str_replace("\u{0000}", Definitions::REPLACEMENT_CHARACTER, $input);
        $input = self::removeSurrogateCodePoints($input);

        /** @psalm-suppress MixedPropertyTypeCoercion */
        $this->stream = \mb_str_split($input);
    }

    /**
     * Fetches the next code point in the input stream.
     */
    public function next(): string
    {
        $codePoint = ($this->stream[$this->position++] ?? self::EOS);
        if ($codePoint === Definitions::NEWLINE) {
            $this->column = 0;
            ++$this->line;
        } else {
            ++$this->column;
        }

        return $codePoint;
    }

    /**
     * @param int $lookAhead used to look ahead in the stream of code points
     */
    public function peek(int $lookAhead = 0): string
    {
        return $this->stream[($this->position + $lookAhead)] ?? self::EOS;
    }

    public function isEndOfStream(): bool
    {
        return $this->peek() === self::EOS;
    }

    /**
     * @throws ParseError
     */
    public function error(string $message): void
    {
        throw new ParseError($message . ' on line ' . $this->line . ':' . $this->column);
    }

    private static function removeSurrogateCodePoints(string $input): string
    {
        return \preg_replace(self::IS_SURROGATES_CODE_POINTS, Definitions::REPLACEMENT_CHARACTER, $input);
    }
}
