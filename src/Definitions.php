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

/**
 * These function represent various definitions defined in section 4.2 of the CSS Grammar specification.
 *
 * @see https://www.w3.org/TR/css-syntax-3/#tokenizer-definitions
 */
final class Definitions
{
    use JustDont;

    public const NEWLINE = "\n";
    public const MAXIMUM_ALLOWED_CODE_POINT = 0x10FFFF;
    public const REPLACEMENT_CHARACTER = "\u{FFFD}";
    private const LOW_LINE = '_';
    private const HYPHEN_MINUS = '-';
    /**
     * A code point between U+0000 NULL and U+0008 BACKSPACE inclusive, or U+000B LINE TABULATION,
     * or a code point between U+000E SHIFT OUT and U+001F INFORMATION SEPARATOR ONE inclusive,
     * or U+007F DELETE.
     */
    private const IS_NON_PRINTABLE_CODE_POINT_REGEX = '/[\x{0000}-\x{0008}]|\x{000B}|[\x{000E}-\x{001F}]|\x{007F}/u';

    /**
     * A code point between U+0030 DIGIT ZERO (0) and U+0039 DIGIT NINE (9) inclusive.
     */
    public static function isDigit(string $codePoint): bool
    {
        return \ctype_digit($codePoint);
    }

    /**
     * A digit, or a code point between U+0041 LATIN CAPITAL LETTER A (A) and U+0046 LATIN CAPITAL LETTER F (F)
     * inclusive, or a code point between U+0061 LATIN SMALL LETTER A (a) and U+0066 LATIN SMALL LETTER F (f) inclusive.
     */
    public static function isHexadecimalDigit(string $codePoint): bool
    {
        return \ctype_xdigit($codePoint);
    }

    /**
     * A code point between U+0041 LATIN CAPITAL LETTER A (A) and U+005A LATIN CAPITAL LETTER Z (Z) inclusive.
     */
    /*
    public static function isUppercaseLetter(string $codePoint): bool
    {
        return \ctype_upper($codePoint);
    }
    */

    /**
     * A code point between U+0061 LATIN SMALL LETTER A (a) and U+007A LATIN SMALL LETTER Z (z) inclusive.
     */
    /*
    public static function isLowercaseLetter(string $codePoint): bool
    {
        return \ctype_lower($codePoint);
    }
    */

    /**
     * An uppercase letter or a lowercase letter.
     */
    public static function isLetter(string $codePoint): bool
    {
        return \ctype_alpha($codePoint);
    }

    /**
     * A code point with a value equal to or greater than U+0080 <control>.
     */
    public static function isNonASCIICodePoint(string $codePoint): bool
    {
        if (\mb_ord($codePoint) >= 0x0080) {
            return true;
        }

        return false;
    }

    /**
     * A letter, a non-ASCII code point, or U+005F LOW LINE (_).
     */
    public static function isNameStartCodePoint(string $codePoint): bool
    {
        return self::isLetter($codePoint) || self::isNonASCIICodePoint($codePoint) || $codePoint === self::LOW_LINE;
    }

    /**
     * A name-start code point, a digit, or U+002D HYPHEN-MINUS (-).
     */
    public static function isNameCodePoint(string $codePoint): bool
    {
        return self::isNameStartCodePoint($codePoint) || self::isDigit($codePoint) || $codePoint === self::HYPHEN_MINUS;
    }

    /**
     * A newline, U+0009 CHARACTER TABULATION, or U+0020 SPACE.
     */
    public static function isWhitespace(string $codePoint): bool
    {
        if ($codePoint === ' ' || $codePoint === "\t" || $codePoint === self::NEWLINE) {
            return true;
        }

        return false;
    }

    public static function isNonPrintableCodePoint(string $codePoint): bool
    {
        if (\preg_match(self::IS_NON_PRINTABLE_CODE_POINT_REGEX, $codePoint) === 1) {
            return true;
        }

        return false;
    }
}
