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
use Girgias\CSSParser\Lexer;
use Girgias\CSSParser\SpecificationCompliantInputStream;
use Girgias\CSSParser\SpecificationCompliantLexer;
use Girgias\CSSParser\Tokens\Delimiter;
use Girgias\CSSParser\Tokens\EOF;
use Girgias\CSSParser\Tokens\Identifier;
use Girgias\CSSParser\Tokens\LeftSquareBracket;
use Girgias\CSSParser\Tokens\RightSquareBracket;
use Girgias\CSSParser\Tokens\TString;
use Girgias\CSSParser\Tokens\Whitespace;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class SpecificationCompliantLexerTest extends TestCase
{
    public function testCommentRemoval(): void
    {
        $lexer = $this->getLexer('/* Comment */');
        self::assertInstanceOf(EOF::class, $lexer->readNext());
    }

    public function test2CommentsSeparatedByAnotherNonWhitespaceToken(): void
    {
        $lexer = $this->getLexer('/* Comment */ identifier /* Comment2 */ ');
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
        self::assertInstanceOf(Identifier::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    public function test2DirectCommentsAreRemove(): void
    {
        $lexer = $this->getLexer('/* Comment *//* Comment2 */ ');
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    public function testCommentRemoveInMiddleOfStream(): void
    {
        $lexer = $this->getLexer(' identifier /* Comment2 */ identifier ');
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
        self::assertInstanceOf(Identifier::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
        self::assertInstanceOf(Identifier::class, $lexer->readNext());
        self::assertInstanceOf(Whitespace::class, $lexer->readNext());
    }

    public function testCommentRemovalInMiddleOfIdentifier(): void
    {
        $lexer = $this->getLexer('ident/* Comment2 */ifier');
        $identifier = $lexer->readNext();
        self::assertInstanceOf(Identifier::class, $identifier);
        self::assertSame('identifier', $identifier->getValue());
    }

    public function testCommentRemovalWhenForwardSlashIsPresentAndCommentIsNotPresent(): void
    {
        $lexer = $this->getLexer('[href="http://www"]');
        self::assertInstanceOf(LeftSquareBracket::class, $lexer->readNext());
        self::assertInstanceOf(Identifier::class, $lexer->readNext());
        self::assertInstanceOf(Delimiter::class, $lexer->readNext());
        self::assertInstanceOf(TString::class, $lexer->readNext());
        self::assertInstanceOf(RightSquareBracket::class, $lexer->readNext());
    }

    private function getLexer(string $input): Lexer
    {
        return new SpecificationCompliantLexer(new CompleteLexer(new SpecificationCompliantInputStream($input)));
    }
}
