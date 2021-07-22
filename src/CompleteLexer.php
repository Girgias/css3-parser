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
use Girgias\CSSParser\Tokens\AtKeyword;
use Girgias\CSSParser\Tokens\BadString;
use Girgias\CSSParser\Tokens\BadUrl;
use Girgias\CSSParser\Tokens\CDC;
use Girgias\CSSParser\Tokens\CDO;
use Girgias\CSSParser\Tokens\Colon;
use Girgias\CSSParser\Tokens\Comma;
use Girgias\CSSParser\Tokens\Comment;
use Girgias\CSSParser\Tokens\Delimiter;
use Girgias\CSSParser\Tokens\Dimension;
use Girgias\CSSParser\Tokens\EOF;
use Girgias\CSSParser\Tokens\FloatNumber;
use Girgias\CSSParser\Tokens\Hash;
use Girgias\CSSParser\Tokens\Identifier;
use Girgias\CSSParser\Tokens\IntegerNumber;
use Girgias\CSSParser\Tokens\LeftCurlyBracket;
use Girgias\CSSParser\Tokens\LeftParenthesis;
use Girgias\CSSParser\Tokens\LeftSquareBracket;
use Girgias\CSSParser\Tokens\Percentage;
use Girgias\CSSParser\Tokens\RightCurlyBracket;
use Girgias\CSSParser\Tokens\RightParenthesis;
use Girgias\CSSParser\Tokens\RightSquareBracket;
use Girgias\CSSParser\Tokens\Semicolon;
use Girgias\CSSParser\Tokens\TFunction;
use Girgias\CSSParser\Tokens\TNumber;
use Girgias\CSSParser\Tokens\Token;
use Girgias\CSSParser\Tokens\TString;
use Girgias\CSSParser\Tokens\Url;
use Girgias\CSSParser\Tokens\Whitespace;

/**
 * Class CompleteLexer.
 *
 * This class is an implementation of section 4.3. of the CSS Syntax 3 specification
 */
final class CompleteLexer implements Lexer
{
    use JustDont;

    private const NUMBER_SIGN = '#';
    private const QUOTATION_MARK = '"';
    private const APOSTROPHE = "'";
    private const COMMA = ',';
    private const COLON = ':';
    private const SEMI_COLON = ';';
    private const LEFT_PARENTHESIS = '(';
    private const RIGHT_PARENTHESIS = ')';
    private const LEFT_SQUARE_BRACKET = '[';
    private const RIGHT_SQUARE_BRACKET = ']';
    private const LEFT_CURLY_BRACKET = '{';
    private const RIGHT_CURLY_BRACKET = '}';
    private const COMMERCIAL_AT = '@';
    private const REVERSE_SOLIDUS = '\\';
    private const PLUS_SIGN = '+';
    private const HYPHEN_MINUS = '-';
    private const FULL_STOP = '.';
    private const GREATER_THAN_SIGN = '>';
    private const LESS_THAN_SIGN = '<';
    private const EXCLAMATION_MARK = '!';
    private const SOLIDUS = '/';
    private const ASTERISK = '*';

    private InputStream $inputStream;

    public function __construct(InputStream $inputStream)
    {
        $this->inputStream = $inputStream;
    }

    /**
     * Core CompleteLexer loop
     * Section 4.3.1. of the CSS syntax 3 specification.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#tokenizer-algorithms
     *
     * @return Tokens\Token
     */
    public function readNext(): Token
    {
        if ($this->inputStream->isEndOfStream()) {
            return new EOF();
        }
        // Consume comment
        if (
            $this->inputStream->peek() === self::SOLIDUS
            && $this->inputStream->peek(1) === self::ASTERISK
        ) {
            return $this->consumeCommentToken();
        }

        /**
         * Technically the specification says this should be consumed but it may arise the need to reconsume therefore
         * we leave the consumption of the code point at the designed token site.
         */
        if (Definitions::isWhitespace($this->inputStream->peek())) {
            return $this->consumeWhitespace();
        }
        // This covers DIGIT, PLUS SIGN, FULL STOP and HYPHEN MINUS for numerical cases
        // Other cases are handled by default return from switch
        if ($this->is3CodePointCheckStartNumber()) {
            return $this->consumeNumericToken();
        }
        // This covers isNameStartCodePoint, HYPHEN MINUS and REVERSE SOLIDUS for identifier start
        if ($this->is3CodePointCheckStartIdentifier()) {
            return $this->consumeIndentLikeToken();
        }

        /**
         * Consume code point as per the specification.
         */
        $currentCodePoint = $this->inputStream->next();

        switch ($currentCodePoint) {
            case self::REVERSE_SOLIDUS:
                // This is a parse error as it means there is no valid escape sequence after the REVERSE SOLIDUS
                $this->inputStream->error('Bad escape sequence');

                return new Delimiter($currentCodePoint);

            case self::QUOTATION_MARK:
            case self::APOSTROPHE:
                return $this->consumeString($currentCodePoint);

            case self::NUMBER_SIGN:
                $nextCodePoint = $this->inputStream->peek();
                $secondNextCodePoint = $this->inputStream->peek(1);
                if (
                    Definitions::isNameCodePoint($nextCodePoint)
                    || $this->is2CodePointCheckValidEscape($nextCodePoint, $secondNextCodePoint)
                ) {
                    $type = Hash::UNRESTRICTED;
                    if (
                        $this->is3CodePointCheckStartIdentifier(
                            $nextCodePoint,
                            $secondNextCodePoint,
                            $this->inputStream->peek(2)
                        )
                    ) {
                        $type = Hash::ID;
                    }
                    // Consume a name, and set the <hash-token>’s value to the returned string.
                    return new Hash($this->consumeName(), $type);
                }

                return new Delimiter($currentCodePoint);

            case self::COMMA:
                return new Comma();

            case self::COLON:
                return new Colon();

            case self::SEMI_COLON:
                return new Semicolon();

            case self::LEFT_PARENTHESIS:
                return new LeftParenthesis();

            case self::RIGHT_PARENTHESIS:
                return new RightParenthesis();

            case self::LEFT_CURLY_BRACKET:
                return new LeftCurlyBracket();

            case self::RIGHT_CURLY_BRACKET:
                return new RightCurlyBracket();

            case self::LEFT_SQUARE_BRACKET:
                return new LeftSquareBracket();

            case self::RIGHT_SQUARE_BRACKET:
                return new RightSquareBracket();

            case self::LESS_THAN_SIGN:
                if (
                    $this->inputStream->peek() === self::EXCLAMATION_MARK
                    && $this->inputStream->peek(1) === self::HYPHEN_MINUS
                    && $this->inputStream->peek(2) === self::HYPHEN_MINUS
                ) {
                    $this->inputStream->next();
                    $this->inputStream->next();
                    $this->inputStream->next();

                    return new CDO();
                }

                return new Delimiter($currentCodePoint);

            case self::HYPHEN_MINUS:
                if (
                    $this->inputStream->peek() === self::HYPHEN_MINUS
                    && $this->inputStream->peek(1) === self::GREATER_THAN_SIGN
                ) {
                    $this->inputStream->next();
                    $this->inputStream->next();

                    return new CDC();
                }

                return new Delimiter($currentCodePoint);

            case self::COMMERCIAL_AT:
                if ($this->is3CodePointCheckStartIdentifier()) {
                    // Create an <at-keyword-token> with its value set to the returned value, and return it.
                    return new AtKeyword($this->consumeName());
                }

                return new Delimiter($currentCodePoint);

            default:
                return new Delimiter($currentCodePoint);
        }
    }

    /**
     * Section 4.3.2. of the CSS syntax 3 specification.
     *
     * This section describes how to consume comments from a stream of code points. It returns nothing.
     *
     * If the next two input code point are U+002F SOLIDUS (/) followed by a U+002A ASTERISK (*),
     * consume them and all following code points up to and including the first U+002A ASTERISK (*)
     * followed by a U+002F SOLIDUS (/), or up to an EOF code point. Return to the start of this step.
     *
     * If the preceding paragraph ended by consuming an EOF code point, this is a parse error.
     *
     * Return nothing.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#consume-comments
     */
    public function consumeComments(): void
    {
        $isNotSolidus = static function (string $codePoint): bool {
            if ($codePoint === self::SOLIDUS) {
                return false;
            }

            return true;
        };

        $codePointStream = '';

        do {
            $codePointStream .= $this->readWhile($isNotSolidus);
            // Consume comment
            if (
                $this->inputStream->peek() === self::SOLIDUS
                && $this->inputStream->peek(1) === self::ASTERISK
            ) {
                $this->consumeCommentToken();
            } else {
                $this->inputStream->next();
            }
        } while (!$this->inputStream->isEndOfStream());

        // To get a new Instance of the InputStream passed to the Lexer.
        $inputStreamClass = \get_class($this->inputStream);
        $this->inputStream = new $inputStreamClass($codePointStream);
    }

    private function readWhile(callable $predicate): string
    {
        $stream = '';
        while ($this->inputStream->isEndOfStream() === false && $predicate($this->inputStream->peek())) {
            $stream .= $this->inputStream->next();
        }

        return $stream;
    }

    private function consumeWhitespace(): Whitespace
    {
        return new Whitespace($this->readWhile([Definitions::class, 'isWhitespace']));
    }

    /**
     * Part of Section 4.3.2. of the CSS syntax 3 specification
     * This consumes a comment and returns an unofficial COMMENT token.
     *
     * This function assumes that the next two (2) input code point have already been
     * verified to be the start of a comment.
     *
     * If the next two input code point are U+002F SOLIDUS (/) followed by a U+002A ASTERISK (*),
     * consume them and all following code points up to and including the first U+002A ASTERISK (*)
     * followed by a U+002F SOLIDUS (/), or up to an EOF code point. Return to the start of this step.
     *
     * If the preceding paragraph ended by consuming an EOF code point, this is a parse error.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#consume-comments
     */
    private function consumeCommentToken(): Comment
    {
        $isNotAsterisk = static function (string $codePoint): bool {
            if ($codePoint === self::ASTERISK) {
                return false;
            }

            return true;
        };

        // Consume initial SOLIDUS and ASTERISK
        $comment = $this->inputStream->next() . $this->inputStream->next();

        do {
            $comment .= $this->readWhile($isNotAsterisk);

            if ($this->inputStream->isEndOfStream()) {
                $this->inputStream->error('Encountered EOF during comment parsing');

                return new Comment($comment);
            }

            // Looks like end of comment but is not
            if (
                $this->inputStream->peek(0) === self::ASTERISK
                && $this->inputStream->peek(1) !== self::SOLIDUS
            ) {
                // Consume ASTERISK
                $comment .= $this->inputStream->next();
            }
        } while (
            $this->inputStream->peek(0) !== self::ASTERISK
            || $this->inputStream->peek(1) !== self::SOLIDUS
        );

        // Consume final ASTERISK and SOLIDUS
        $comment .= $this->inputStream->next() . $this->inputStream->next();

        return new Comment($comment);
    }

    /**
     * Section 4.3.3. of the CSS syntax 3 specification.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#consume-numeric-token
     *
     * @return Dimension|Percentage|TNumber
     */
    private function consumeNumericToken(): Token
    {
        $numberToken = $this->readNumber();
        if ($this->is3CodePointCheckStartIdentifier()) {
            $unit = $this->consumeName();

            return new Dimension($numberToken, $unit);
        }

        if ($this->inputStream->peek() === '%') {
            // Consume percentage sign
            $this->inputStream->next();

            return new Percentage($numberToken);
        }

        return $numberToken;
    }

    /**
     * Section 4.3.4. of the CSS syntax 3 specification.
     *
     * This section describes how to consume an ident-like token from a stream of code points.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#consume-ident-like-token
     *
     * @return BadUrl|Identifier|TFunction|Url
     */
    private function consumeIndentLikeToken(): Token
    {
        $string = $this->consumeName();

        // Maybe URL Token
        if (\strtolower($string) === 'url' && $this->inputStream->peek() === self::LEFT_PARENTHESIS) {
            // Consume LEFT PARENTHESIS
            $this->inputStream->next();

            /**
             *  While the next two input code points are whitespace, consume the next input code point.
             */
            while (
                Definitions::isWhitespace($this->inputStream->peek())
                && Definitions::isWhitespace($this->inputStream->peek(1))
            ) {
                $this->inputStream->next();
                $this->inputStream->next();
            }
            /**
             * If the next one or two input code points are U+0022 QUOTATION MARK ("), U+0027 APOSTROPHE ('),
             * or whitespace followed by U+0022 QUOTATION MARK (") or U+0027 APOSTROPHE ('),
             * then create a <function-token> with its value set to string and return it.
             * Otherwise, consume a url token, and return it.
             */
            if (
                $this->inputStream->peek() === self::QUOTATION_MARK
                || $this->inputStream->peek() === self::APOSTROPHE
                || Definitions::isWhitespace($this->inputStream->peek()) && (
                    $this->inputStream->peek(1) === self::QUOTATION_MARK
                    || $this->inputStream->peek(1) === self::APOSTROPHE
                )
            ) {
                return new TFunction($string);
            }

            return $this->consumeUrlToken();
        }

        if ($this->inputStream->peek() === self::LEFT_PARENTHESIS) {
            // Consume LEFT PARENTHESIS
            $this->inputStream->next();

            return new TFunction($string);
        }

        return new Identifier($string);
    }

    /**
     * Section 4.3.5. of the CSS syntax 3 specification.
     *
     * This section describes how to consume a string token from a stream of code points.
     * It returns either a <string-token> or <bad-string-token>.
     *
     * This algorithm may be called with an ending code point, which denotes the code point that ends the string.
     * If an ending code point is not specified, the current input code point is used.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#consume-string-token
     *
     * @return BadString|TString
     */
    private function consumeString(?string $endingCodePoint = null): Token
    {
        $endingCodePoint ??= $this->inputStream->next();

        $isPartOfString = static function (string $codePoint) use ($endingCodePoint): bool {
            if (
                $codePoint === $endingCodePoint
                || $codePoint === Definitions::NEWLINE
                || $codePoint === self::REVERSE_SOLIDUS
            ) {
                return false;
            }

            return true;
        };

        $string = '';
        do {
            $string .= $this->readWhile($isPartOfString);

            if ($this->inputStream->isEndOfStream()) {
                $this->inputStream->error('Encountered EOF during string parsing');

                return new TString($string);
            }
            if ($this->inputStream->peek() === Definitions::NEWLINE) {
                $this->inputStream->error('Encountered newline during string parsing');

                return new BadString($string);
            }
            if ($this->inputStream->peek() === self::REVERSE_SOLIDUS) {
                // Consume REVERSE SOLIDUS
                $this->inputStream->next();
                /** If the next input code point is EOF, do nothing. */
                if ($this->inputStream->isEndOfStream()) {
                    continue;
                }
                /** Otherwise, if the next input code point is a newline, consume it. */
                if ($this->inputStream->peek() === Definitions::NEWLINE) {
                    // Consume the NEW LINE
                    $this->inputStream->next();

                    continue;
                }
                /**
                 * Otherwise, (the stream starts with a valid escape) consume an escaped code point and
                 * append the returned code point to the <string-token>’s value.
                 */
                $string .= $this->consumeEscapedCodePoint();
            }
        } while ($this->inputStream->peek() !== $endingCodePoint);

        // Consume ending code point
        $this->inputStream->next();

        return new TString($string);
    }

    /**
     * Section 4.3.6. of the CSS syntax 3 specification.
     *
     * This section describes how to consume a url token from a stream of code points.
     * It returns either a <url-token> or a <bad-url-token>.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#consume-a-url-token
     *
     * @return BadUrl|Url
     */
    private function consumeUrlToken(): Token
    {
        $this->consumeWhitespace();
        $url = '';
        $possibleBadUrl = false;

        do {
            if ($this->inputStream->isEndOfStream()) {
                $this->inputStream->error('Encountered EOF during URL parsing');

                return new Url($url);
            }
            if ($possibleBadUrl) {
                $this->consumeRemnantsOfBadUrl();

                return new BadUrl($url);
            }
            $codePoint = $this->inputStream->next();
            if (Definitions::isWhitespace($codePoint)) {
                $this->consumeWhitespace();
                $possibleBadUrl = true;

                continue;
            }
            /**
             * U+0022 QUOTATION MARK (")
             * U+0027 APOSTROPHE (')
             * U+0028 LEFT PARENTHESIS (()
             * non-printable code point
             * This is a parse error. Consume the remnants of a bad url, create a <bad-url-token>, and return it.
             */
            if (
                $codePoint === self::QUOTATION_MARK
                || $codePoint === self::APOSTROPHE
                || $codePoint === self::LEFT_PARENTHESIS
                || Definitions::isNonPrintableCodePoint($codePoint)
            ) {
                $this->consumeRemnantsOfBadUrl();
                $this->inputStream->error('Unexpected character ' . $codePoint . ' in URL');

                return new BadUrl($url);
            }

            if ($codePoint === self::REVERSE_SOLIDUS) {
                /**
                 * If the stream starts with a valid escape, consume an escaped code point
                 * and append the returned code point to the <url-token>’s value.
                 */
                if ($this->is2CodePointCheckValidEscape($codePoint, $this->inputStream->peek())) {
                    $url .= $this->consumeEscapedCodePoint();

                    continue;
                }

                /**
                 * Otherwise, this is a parse error.
                 * Consume the remnants of a bad url, create a <bad-url-token>, and return it.
                 */
                $this->consumeRemnantsOfBadUrl();
                $this->inputStream->error('Bad escape sequence in URL');

                return new BadUrl($url);
            }

            $url .= $codePoint;
        } while ($this->inputStream->peek() !== self::RIGHT_PARENTHESIS);
        /**
         * If the next input code point is U+0029 RIGHT PARENTHESIS ()) [...]
         * consume it and return the <url-token>.
         */
        $this->inputStream->next();

        return new Url($url);
    }

    /**
     * Section 4.3.7 of the CSS syntax 3 specification.
     *
     * It assumes that the U+005C REVERSE SOLIDUS (\) has already been consumed and that the next input
     * code point has already been verified to be part of a valid escape. It will return a code point.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#consume-an-escaped-code-point
     *
     * @return string A valid code point
     */
    private function consumeEscapedCodePoint(): string
    {
        // Use custom implementation of readWhile as there is a limit of six (6) hexadecimal characters to consume
        if (Definitions::isHexadecimalDigit($this->inputStream->peek())) {
            $iterator = 0;
            $hexNumber = '';
            while (
                $this->inputStream->isEndOfStream() === false
                && Definitions::isHexadecimalDigit($this->inputStream->peek())
                && $iterator < 6
            ) {
                $hexNumber .= $this->inputStream->next();
                ++$iterator;
            }
            /** As per the spec "If the next input code point is whitespace, consume it as well." */
            if (Definitions::isWhitespace($this->inputStream->peek())) {
                $this->inputStream->next();
            }

            $integerRepresentation = \hexdec($hexNumber);
            if (
                $integerRepresentation === 0
                // Surrogate pair interval
                || ($integerRepresentation >= 0xD800 && $integerRepresentation <= 0xDFFF)
                || $integerRepresentation > Definitions::MAXIMUM_ALLOWED_CODE_POINT
            ) {
                return Definitions::REPLACEMENT_CHARACTER;
            }

            $unicodeCodePoint = \mb_chr((int) $integerRepresentation, 'UTF-8');

            /**
             * Ignore coverage as this cannot happen as we are checking for valid range of mb_chr().
             */
            if ($unicodeCodePoint === false) {
                // @codeCoverageIgnoreStart
                $this->inputStream->error('mb_chr failed on hex value :[' . $hexNumber . ']');
                // @codeCoverageIgnoreEnd
            }

            return $unicodeCodePoint;
        }
        /**
         * EOF: This is a parse error. Return U+FFFD REPLACEMENT CHARACTER (�).
         */
        if ($this->inputStream->isEndOfStream()) {
            $this->inputStream->error('Cannot escape EOF');

            return Definitions::REPLACEMENT_CHARACTER;
        }
        /**
         * anything else: Return the current input code point.
         */
        return $this->inputStream->next();
    }

    /**
     * Section 4.3.8. of the CSS syntax 3 specification.
     *
     * This section describes how to check if two code points are a valid escape.
     * The algorithm described here can be called explicitly with two code points,
     * or can be called with the input stream itself.
     * In the latter case, the two code points in question are the current input code point and
     * the next input code point, in that order.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#starts-with-a-valid-escape
     */
    private function is2CodePointCheckValidEscape(
        ?string $firstCodePoint = null,
        ?string $secondCodePoint = null
    ): bool {
        $firstCodePoint ??= $this->inputStream->peek();
        $secondCodePoint ??= $this->inputStream->peek(1);

        if ($firstCodePoint !== self::REVERSE_SOLIDUS || $secondCodePoint === Definitions::NEWLINE) {
            return false;
        }

        return true;
    }

    /**
     * Section 4.3.9. of the CSS syntax 3 specification.
     *
     * This section describes how to check if three code points would start an identifier.
     * The algorithm described here can be called explicitly with three code points,
     * or can be called with the input stream itself.
     * In the latter case, the three code points in question are the current input code point and
     * the next two input code points, in that order.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#would-start-an-identifier
     */
    private function is3CodePointCheckStartIdentifier(
        ?string $firstCodePoint = null,
        ?string $secondCodePoint = null,
        ?string $thirdCodePoint = null
    ): bool {
        $firstCodePoint ??= $this->inputStream->peek();
        $secondCodePoint ??= $this->inputStream->peek(1);
        $thirdCodePoint ??= $this->inputStream->peek(2);

        if ($firstCodePoint === self::HYPHEN_MINUS) {
            if (
                Definitions::isNameStartCodePoint($secondCodePoint)
                || $this->is2CodePointCheckValidEscape($secondCodePoint, $thirdCodePoint)
                // Adding the check to insure third code point is not GREATER THAN SIGN compared to the spec.
                || ($secondCodePoint === self::HYPHEN_MINUS && $thirdCodePoint !== self::GREATER_THAN_SIGN)
            ) {
                return true;
            }

            return false;
        }
        if (Definitions::isNameStartCodePoint($firstCodePoint)) {
            return true;
        }
        if ($this->is2CodePointCheckValidEscape($firstCodePoint, $secondCodePoint)) {
            return true;
        }

        return false;
    }

    /**
     * Section 4.3.10. of the CSS syntax 3 specification.
     *
     * This section describes how to check if three code points would start a number.
     * The algorithm described here can be called explicitly with three code points,
     * or can be called with the input stream itself.
     * In the latter case, the three code points in question are the current input code point and
     * the next two input code points, in that order.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#check-if-three-code-points-would-start-a-number
     */
    private function is3CodePointCheckStartNumber(
        ?string $firstCodePoint = null,
        ?string $secondCodePoint = null,
        ?string $thirdCodePoint = null
    ): bool {
        $firstCodePoint ??= $this->inputStream->peek();
        $secondCodePoint ??= $this->inputStream->peek(1);
        $thirdCodePoint ??= $this->inputStream->peek(2);

        if ($firstCodePoint === self::PLUS_SIGN || $firstCodePoint === self::HYPHEN_MINUS) {
            if (Definitions::isDigit($secondCodePoint)) {
                return true;
            }
            if (
                $secondCodePoint === self::FULL_STOP
                && Definitions::isDigit($thirdCodePoint)
            ) {
                return true;
            }

            return false;
        }
        if ($firstCodePoint === self::FULL_STOP && Definitions::isDigit($secondCodePoint)) {
            return true;
        }
        if (Definitions::isDigit($firstCodePoint)) {
            return true;
        }

        return false;
    }

    /**
     * Section 4.3.11. of the CSS syntax 3 specification.
     *
     * This section describes how to consume a name from a stream of code points.
     * It returns a string containing the largest name that can be formed from adjacent code points in the stream,
     * starting from the first.
     */
    private function consumeName(): string
    {
        $result = '';
        while (
            !$this->inputStream->isEndOfStream()
            && (
                $this->is2CodePointCheckValidEscape()
                || Definitions::isNameCodePoint($this->inputStream->peek())
            )
        ) {
            if ($this->is2CodePointCheckValidEscape()) {
                $this->inputStream->next();
                $result .= $this->consumeEscapedCodePoint();
            }
            $result .= $this->readWhile([Definitions::class, 'isNameCodePoint']);
        }

        return $result;
    }

    /**
     * Section 4.3.12. of the CSS syntax 3 specification
     * This section describes how to consume a number from a stream of code points.
     * It returns a numeric value, and a type which is either "integer" or "number".
     *
     * @see https://www.w3.org/TR/css-syntax-3/#consume-a-number
     */
    private function readNumber(): TNumber
    {
        // Initialize default values
        $fractionalPart = '';
        $exponent = '';
        $exponentSign = '';

        $sign = '+';
        if ($this->inputStream->peek() === self::PLUS_SIGN || $this->inputStream->peek() === self::HYPHEN_MINUS) {
            $sign = $this->inputStream->next();
        }

        $integerPart = $this->readWhile([Definitions::class, 'isDigit']);

        // Check for fractional part
        if ($this->inputStream->peek() === self::FULL_STOP && Definitions::isDigit($this->inputStream->peek(1))) {
            // Consume FULL STOP
            $this->inputStream->next();
            $fractionalPart = $this->readWhile([Definitions::class, 'isDigit']);
        }

        // Check for exponential part, this corresponds to step 5.
        if (
            ($this->inputStream->peek() === 'e' || $this->inputStream->peek() === 'E')
            && Definitions::isDigit($this->inputStream->peek(1))
        ) {
            // Consume exponent marker
            $this->inputStream->next();
            $exponent = $this->readWhile([Definitions::class, 'isDigit']);
        } elseif (
            $this->inputStream->peek() === 'e' || $this->inputStream->peek() === 'E'
            && ($this->inputStream->peek(1) === self::PLUS_SIGN || $this->inputStream->peek(1) === self::HYPHEN_MINUS)
            && Definitions::isDigit($this->inputStream->peek(2))
        ) {
            // Consume exponent marker
            $this->inputStream->next();
            $exponentSign = $this->inputStream->next();
            $exponent = $this->readWhile([Definitions::class, 'isDigit']);
        }

        return $this->numericRepresentation($sign, $integerPart, $fractionalPart, $exponent, $exponentSign);
    }

    /**
     * Section 4.3.13. of the CSS syntax 3 specification.
     *
     * This section describes how to convert a string representation of a number to a number.
     * It returns a number token.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#convert-a-string-to-a-number
     */
    private function numericRepresentation(
        string $signCharacter,
        string $integerPart,
        string $fractionalPart,
        string $exponentPart,
        string $exponentSignCharacter
    ): TNumber {
        $sign = 1;
        if ($signCharacter === self::HYPHEN_MINUS) {
            $sign = -1;
        }
        $exponentSign = 1;
        if ($exponentSignCharacter === self::HYPHEN_MINUS) {
            $exponentSign = -1;
        }
        // Takes care of the empty string value = 0;
        $integer = \intval($integerPart, 10);
        $fractional = \intval($fractionalPart, 10);
        $exponent = \intval($exponentPart, 10);

        if ($fractional === 0 && $exponent === 0) {
            return new IntegerNumber($sign * $integer);
        }

        // Length of the fractional part of the number
        $d = \strlen((string) $fractional);
        $number = ($sign * ($integer + ($fractional * (10 ** (-$d)))) * (10 ** ($exponentSign * $exponent)));

        return new FloatNumber($number);
    }

    /**
     * Section 4.3.14. of the CSS syntax 3 specification.
     *
     * This section describes how to consume the remnants of a bad url from a stream of code points,
     * "cleaning up" after the tokenizer realizes that it’s in the middle of a <bad-url-token> rather
     * than a <url-token>. It returns nothing; its sole use is to consume enough of the input stream
     * to reach a recovery point where normal tokenizing can resume.
     *
     * @see https://www.w3.org/TR/css-syntax-3/#consume-remnants-of-bad-url
     */
    private function consumeRemnantsOfBadUrl(): void
    {
        $isNotRightParenthesisOrReverseSolidus = static function (string $codePoint): bool {
            if ($codePoint === self::RIGHT_PARENTHESIS || $codePoint === self::REVERSE_SOLIDUS) {
                return false;
            }

            return true;
        };

        do {
            $this->readWhile($isNotRightParenthesisOrReverseSolidus);

            if ($this->is2CodePointCheckValidEscape($this->inputStream->peek(), $this->inputStream->peek(1))) {
                // Consume the REVERSE SOLIDUS
                $this->inputStream->next();
                $this->consumeEscapedCodePoint();
            }
        } while ($this->inputStream->next() !== self::RIGHT_PARENTHESIS);
    }
}
