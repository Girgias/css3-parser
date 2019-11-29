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

use Girgias\CSSParser\Definitions;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class DefinitionsTest extends TestCase
{
    public function testIsWhitespace(): void
    {
        self::assertTrue(Definitions::isWhitespace(' '));
        self::assertTrue(Definitions::isWhitespace("\n"));
        self::assertTrue(Definitions::isWhitespace("\t"));
        self::assertFalse(Definitions::isWhitespace('-'));
        self::assertFalse(Definitions::isWhitespace('_'));
        self::assertFalse(Definitions::isWhitespace('0'));
        self::assertFalse(Definitions::isWhitespace('1'));
        self::assertFalse(Definitions::isWhitespace('2'));
        self::assertFalse(Definitions::isWhitespace('3'));
        self::assertFalse(Definitions::isWhitespace('9'));
        self::assertFalse(Definitions::isWhitespace('a'));
        self::assertFalse(Definitions::isWhitespace('g'));
        self::assertFalse(Definitions::isWhitespace('z'));
        self::assertFalse(Definitions::isWhitespace('猫'));
        self::assertFalse(Definitions::isWhitespace('あ'));
    }

    public function testIsDigit(): void
    {
        self::assertTrue(Definitions::isDigit('0'));
        self::assertTrue(Definitions::isDigit('1'));
        self::assertTrue(Definitions::isDigit('2'));
        self::assertTrue(Definitions::isDigit('3'));
        self::assertTrue(Definitions::isDigit('4'));
        self::assertTrue(Definitions::isDigit('5'));
        self::assertTrue(Definitions::isDigit('6'));
        self::assertTrue(Definitions::isDigit('7'));
        self::assertTrue(Definitions::isDigit('8'));
        self::assertTrue(Definitions::isDigit('9'));
        self::assertFalse(Definitions::isDigit('a'));
        self::assertFalse(Definitions::isDigit('g'));
        self::assertFalse(Definitions::isDigit('z'));
        self::assertFalse(Definitions::isDigit(' '));
        self::assertFalse(Definitions::isDigit('-'));
        self::assertFalse(Definitions::isDigit('_'));
        self::assertFalse(Definitions::isDigit("\n"));
        self::assertFalse(Definitions::isDigit("\t"));
        self::assertFalse(Definitions::isDigit('猫'));
        self::assertFalse(Definitions::isDigit('あ'));
    }

    public function testIsHexadecimalDigit(): void
    {
        self::assertTrue(Definitions::isHexadecimalDigit('0'));
        self::assertTrue(Definitions::isHexadecimalDigit('1'));
        self::assertTrue(Definitions::isHexadecimalDigit('2'));
        self::assertTrue(Definitions::isHexadecimalDigit('3'));
        self::assertTrue(Definitions::isHexadecimalDigit('4'));
        self::assertTrue(Definitions::isHexadecimalDigit('5'));
        self::assertTrue(Definitions::isHexadecimalDigit('6'));
        self::assertTrue(Definitions::isHexadecimalDigit('7'));
        self::assertTrue(Definitions::isHexadecimalDigit('8'));
        self::assertTrue(Definitions::isHexadecimalDigit('9'));
        self::assertTrue(Definitions::isHexadecimalDigit('a'));
        self::assertTrue(Definitions::isHexadecimalDigit('b'));
        self::assertTrue(Definitions::isHexadecimalDigit('c'));
        self::assertTrue(Definitions::isHexadecimalDigit('d'));
        self::assertTrue(Definitions::isHexadecimalDigit('e'));
        self::assertTrue(Definitions::isHexadecimalDigit('f'));
        self::assertTrue(Definitions::isHexadecimalDigit('A'));
        self::assertTrue(Definitions::isHexadecimalDigit('B'));
        self::assertTrue(Definitions::isHexadecimalDigit('C'));
        self::assertTrue(Definitions::isHexadecimalDigit('D'));
        self::assertTrue(Definitions::isHexadecimalDigit('E'));
        self::assertTrue(Definitions::isHexadecimalDigit('F'));
        self::assertFalse(Definitions::isHexadecimalDigit('g'));
        self::assertFalse(Definitions::isHexadecimalDigit('z'));
        self::assertFalse(Definitions::isHexadecimalDigit('G'));
        self::assertFalse(Definitions::isHexadecimalDigit('Z'));
        self::assertFalse(Definitions::isHexadecimalDigit('k'));
        self::assertFalse(Definitions::isHexadecimalDigit('K'));
        self::assertFalse(Definitions::isHexadecimalDigit('m'));
        self::assertFalse(Definitions::isHexadecimalDigit('M'));
        self::assertFalse(Definitions::isHexadecimalDigit(' '));
        self::assertFalse(Definitions::isHexadecimalDigit('-'));
        self::assertFalse(Definitions::isHexadecimalDigit('_'));
        self::assertFalse(Definitions::isHexadecimalDigit("\n"));
        self::assertFalse(Definitions::isHexadecimalDigit("\t"));
        self::assertFalse(Definitions::isHexadecimalDigit('猫'));
        self::assertFalse(Definitions::isHexadecimalDigit('あ'));
    }

    public function testIsLetter(): void
    {
        self::assertTrue(Definitions::isLetter('a'));
        self::assertTrue(Definitions::isLetter('b'));
        self::assertTrue(Definitions::isLetter('c'));
        self::assertTrue(Definitions::isLetter('d'));
        self::assertTrue(Definitions::isLetter('e'));
        self::assertTrue(Definitions::isLetter('f'));
        self::assertTrue(Definitions::isLetter('g'));
        self::assertTrue(Definitions::isLetter('h'));
        self::assertTrue(Definitions::isLetter('i'));
        self::assertTrue(Definitions::isLetter('j'));
        self::assertTrue(Definitions::isLetter('k'));
        self::assertTrue(Definitions::isLetter('l'));
        self::assertTrue(Definitions::isLetter('m'));
        self::assertTrue(Definitions::isLetter('n'));
        self::assertTrue(Definitions::isLetter('o'));
        self::assertTrue(Definitions::isLetter('p'));
        self::assertTrue(Definitions::isLetter('q'));
        self::assertTrue(Definitions::isLetter('r'));
        self::assertTrue(Definitions::isLetter('s'));
        self::assertTrue(Definitions::isLetter('t'));
        self::assertTrue(Definitions::isLetter('u'));
        self::assertTrue(Definitions::isLetter('v'));
        self::assertTrue(Definitions::isLetter('w'));
        self::assertTrue(Definitions::isLetter('x'));
        self::assertTrue(Definitions::isLetter('y'));
        self::assertTrue(Definitions::isLetter('z'));
        self::assertTrue(Definitions::isLetter('A'));
        self::assertTrue(Definitions::isLetter('B'));
        self::assertTrue(Definitions::isLetter('C'));
        self::assertTrue(Definitions::isLetter('D'));
        self::assertTrue(Definitions::isLetter('E'));
        self::assertTrue(Definitions::isLetter('F'));
        self::assertTrue(Definitions::isLetter('G'));
        self::assertTrue(Definitions::isLetter('H'));
        self::assertTrue(Definitions::isLetter('I'));
        self::assertTrue(Definitions::isLetter('J'));
        self::assertTrue(Definitions::isLetter('K'));
        self::assertTrue(Definitions::isLetter('L'));
        self::assertTrue(Definitions::isLetter('M'));
        self::assertTrue(Definitions::isLetter('N'));
        self::assertTrue(Definitions::isLetter('O'));
        self::assertTrue(Definitions::isLetter('P'));
        self::assertTrue(Definitions::isLetter('Q'));
        self::assertTrue(Definitions::isLetter('R'));
        self::assertTrue(Definitions::isLetter('S'));
        self::assertTrue(Definitions::isLetter('T'));
        self::assertTrue(Definitions::isLetter('U'));
        self::assertTrue(Definitions::isLetter('V'));
        self::assertTrue(Definitions::isLetter('W'));
        self::assertTrue(Definitions::isLetter('X'));
        self::assertTrue(Definitions::isLetter('Y'));
        self::assertTrue(Definitions::isLetter('Z'));
        self::assertFalse(Definitions::isLetter('0'));
        self::assertFalse(Definitions::isLetter('1'));
        self::assertFalse(Definitions::isLetter('2'));
        self::assertFalse(Definitions::isLetter('3'));
        self::assertFalse(Definitions::isLetter('4'));
        self::assertFalse(Definitions::isLetter('5'));
        self::assertFalse(Definitions::isLetter('6'));
        self::assertFalse(Definitions::isLetter('7'));
        self::assertFalse(Definitions::isLetter('8'));
        self::assertFalse(Definitions::isLetter('9'));
        self::assertFalse(Definitions::isLetter(' '));
        self::assertFalse(Definitions::isLetter('-'));
        self::assertFalse(Definitions::isLetter('_'));
        self::assertFalse(Definitions::isLetter("\n"));
        self::assertFalse(Definitions::isLetter("\t"));
        self::assertFalse(Definitions::isLetter('猫'));
        self::assertFalse(Definitions::isLetter('あ'));
    }

    public function testIsNonASCIICodePoint(): void
    {
        self::assertTrue(Definitions::isNonASCIICodePoint("\u{0080}"));
        self::assertTrue(Definitions::isNonASCIICodePoint('¶'));
        self::assertTrue(Definitions::isNonASCIICodePoint('®'));
        self::assertTrue(Definitions::isNonASCIICodePoint('×'));
        self::assertTrue(Definitions::isNonASCIICodePoint('Ţ'));
        self::assertTrue(Definitions::isNonASCIICodePoint('ɴ'));
        self::assertTrue(Definitions::isNonASCIICodePoint('ʭ'));
        self::assertTrue(Definitions::isNonASCIICodePoint('Δ'));
        self::assertTrue(Definitions::isNonASCIICodePoint('ص'));
        self::assertTrue(Definitions::isNonASCIICodePoint('ਦ'));
        self::assertTrue(Definitions::isNonASCIICodePoint('猫'));
        self::assertTrue(Definitions::isNonASCIICodePoint('あ'));

        self::assertFalse(Definitions::isNonASCIICodePoint('V'));
        self::assertFalse(Definitions::isNonASCIICodePoint('W'));
        self::assertFalse(Definitions::isNonASCIICodePoint('X'));
        self::assertFalse(Definitions::isNonASCIICodePoint('Y'));
        self::assertFalse(Definitions::isNonASCIICodePoint('Z'));
        self::assertFalse(Definitions::isNonASCIICodePoint('0'));
        self::assertFalse(Definitions::isNonASCIICodePoint('1'));
        self::assertFalse(Definitions::isNonASCIICodePoint('2'));
        self::assertFalse(Definitions::isNonASCIICodePoint('3'));
        self::assertFalse(Definitions::isNonASCIICodePoint('4'));
        self::assertFalse(Definitions::isNonASCIICodePoint('5'));
        self::assertFalse(Definitions::isNonASCIICodePoint('6'));
        self::assertFalse(Definitions::isNonASCIICodePoint('7'));
        self::assertFalse(Definitions::isNonASCIICodePoint('8'));
        self::assertFalse(Definitions::isNonASCIICodePoint('9'));
        self::assertFalse(Definitions::isNonASCIICodePoint(' '));
        self::assertFalse(Definitions::isNonASCIICodePoint('-'));
        self::assertFalse(Definitions::isNonASCIICodePoint('_'));
        self::assertFalse(Definitions::isNonASCIICodePoint("\n"));
        self::assertFalse(Definitions::isNonASCIICodePoint("\t"));
    }

    public function testIsNameStartCodePoint(): void
    {
        self::assertTrue(Definitions::isNameStartCodePoint('a'));
        self::assertTrue(Definitions::isNameStartCodePoint('b'));
        self::assertTrue(Definitions::isNameStartCodePoint('c'));
        self::assertTrue(Definitions::isNameStartCodePoint('d'));
        self::assertTrue(Definitions::isNameStartCodePoint('e'));
        self::assertTrue(Definitions::isNameStartCodePoint('f'));
        self::assertTrue(Definitions::isNameStartCodePoint('g'));
        self::assertTrue(Definitions::isNameStartCodePoint('h'));
        self::assertTrue(Definitions::isNameStartCodePoint('i'));
        self::assertTrue(Definitions::isNameStartCodePoint('j'));
        self::assertTrue(Definitions::isNameStartCodePoint('k'));
        self::assertTrue(Definitions::isNameStartCodePoint('l'));
        self::assertTrue(Definitions::isNameStartCodePoint('m'));
        self::assertTrue(Definitions::isNameStartCodePoint('n'));
        self::assertTrue(Definitions::isNameStartCodePoint('o'));
        self::assertTrue(Definitions::isNameStartCodePoint('p'));
        self::assertTrue(Definitions::isNameStartCodePoint('q'));
        self::assertTrue(Definitions::isNameStartCodePoint('r'));
        self::assertTrue(Definitions::isNameStartCodePoint('s'));
        self::assertTrue(Definitions::isNameStartCodePoint('t'));
        self::assertTrue(Definitions::isNameStartCodePoint('u'));
        self::assertTrue(Definitions::isNameStartCodePoint('v'));
        self::assertTrue(Definitions::isNameStartCodePoint('w'));
        self::assertTrue(Definitions::isNameStartCodePoint('x'));
        self::assertTrue(Definitions::isNameStartCodePoint('y'));
        self::assertTrue(Definitions::isNameStartCodePoint('z'));
        self::assertTrue(Definitions::isNameStartCodePoint('A'));
        self::assertTrue(Definitions::isNameStartCodePoint('B'));
        self::assertTrue(Definitions::isNameStartCodePoint('C'));
        self::assertTrue(Definitions::isNameStartCodePoint('D'));
        self::assertTrue(Definitions::isNameStartCodePoint('E'));
        self::assertTrue(Definitions::isNameStartCodePoint('F'));
        self::assertTrue(Definitions::isNameStartCodePoint('G'));
        self::assertTrue(Definitions::isNameStartCodePoint('H'));
        self::assertTrue(Definitions::isNameStartCodePoint('I'));
        self::assertTrue(Definitions::isNameStartCodePoint('J'));
        self::assertTrue(Definitions::isNameStartCodePoint('K'));
        self::assertTrue(Definitions::isNameStartCodePoint('L'));
        self::assertTrue(Definitions::isNameStartCodePoint('M'));
        self::assertTrue(Definitions::isNameStartCodePoint('N'));
        self::assertTrue(Definitions::isNameStartCodePoint('O'));
        self::assertTrue(Definitions::isNameStartCodePoint('P'));
        self::assertTrue(Definitions::isNameStartCodePoint('Q'));
        self::assertTrue(Definitions::isNameStartCodePoint('R'));
        self::assertTrue(Definitions::isNameStartCodePoint('S'));
        self::assertTrue(Definitions::isNameStartCodePoint('T'));
        self::assertTrue(Definitions::isNameStartCodePoint('U'));
        self::assertTrue(Definitions::isNameStartCodePoint('V'));
        self::assertTrue(Definitions::isNameStartCodePoint('W'));
        self::assertTrue(Definitions::isNameStartCodePoint('X'));
        self::assertTrue(Definitions::isNameStartCodePoint('Y'));
        self::assertTrue(Definitions::isNameStartCodePoint('Z'));
        self::assertTrue(Definitions::isNameStartCodePoint('_'));
        self::assertTrue(Definitions::isNameStartCodePoint('猫'));
        self::assertTrue(Definitions::isNameStartCodePoint('あ'));

        self::assertFalse(Definitions::isNameStartCodePoint(' '));
        self::assertFalse(Definitions::isNameStartCodePoint('-'));
        self::assertFalse(Definitions::isNameStartCodePoint('0'));
        self::assertFalse(Definitions::isNameStartCodePoint('1'));
        self::assertFalse(Definitions::isNameStartCodePoint('2'));
        self::assertFalse(Definitions::isNameStartCodePoint('3'));
        self::assertFalse(Definitions::isNameStartCodePoint('4'));
        self::assertFalse(Definitions::isNameStartCodePoint('5'));
        self::assertFalse(Definitions::isNameStartCodePoint('6'));
        self::assertFalse(Definitions::isNameStartCodePoint('7'));
        self::assertFalse(Definitions::isNameStartCodePoint('8'));
        self::assertFalse(Definitions::isNameStartCodePoint('9'));
        self::assertFalse(Definitions::isNameStartCodePoint("\n"));
        self::assertFalse(Definitions::isNameStartCodePoint("\t"));
    }

    public function testIsNameCodePoint(): void
    {
        self::assertTrue(Definitions::isNameCodePoint('a'));
        self::assertTrue(Definitions::isNameCodePoint('b'));
        self::assertTrue(Definitions::isNameCodePoint('c'));
        self::assertTrue(Definitions::isNameCodePoint('d'));
        self::assertTrue(Definitions::isNameCodePoint('e'));
        self::assertTrue(Definitions::isNameCodePoint('f'));
        self::assertTrue(Definitions::isNameCodePoint('g'));
        self::assertTrue(Definitions::isNameCodePoint('h'));
        self::assertTrue(Definitions::isNameCodePoint('i'));
        self::assertTrue(Definitions::isNameCodePoint('j'));
        self::assertTrue(Definitions::isNameCodePoint('k'));
        self::assertTrue(Definitions::isNameCodePoint('l'));
        self::assertTrue(Definitions::isNameCodePoint('m'));
        self::assertTrue(Definitions::isNameCodePoint('n'));
        self::assertTrue(Definitions::isNameCodePoint('o'));
        self::assertTrue(Definitions::isNameCodePoint('p'));
        self::assertTrue(Definitions::isNameCodePoint('q'));
        self::assertTrue(Definitions::isNameCodePoint('r'));
        self::assertTrue(Definitions::isNameCodePoint('s'));
        self::assertTrue(Definitions::isNameCodePoint('t'));
        self::assertTrue(Definitions::isNameCodePoint('u'));
        self::assertTrue(Definitions::isNameCodePoint('v'));
        self::assertTrue(Definitions::isNameCodePoint('w'));
        self::assertTrue(Definitions::isNameCodePoint('x'));
        self::assertTrue(Definitions::isNameCodePoint('y'));
        self::assertTrue(Definitions::isNameCodePoint('z'));
        self::assertTrue(Definitions::isNameCodePoint('A'));
        self::assertTrue(Definitions::isNameCodePoint('B'));
        self::assertTrue(Definitions::isNameCodePoint('C'));
        self::assertTrue(Definitions::isNameCodePoint('D'));
        self::assertTrue(Definitions::isNameCodePoint('E'));
        self::assertTrue(Definitions::isNameCodePoint('F'));
        self::assertTrue(Definitions::isNameCodePoint('G'));
        self::assertTrue(Definitions::isNameCodePoint('H'));
        self::assertTrue(Definitions::isNameCodePoint('I'));
        self::assertTrue(Definitions::isNameCodePoint('J'));
        self::assertTrue(Definitions::isNameCodePoint('K'));
        self::assertTrue(Definitions::isNameCodePoint('L'));
        self::assertTrue(Definitions::isNameCodePoint('M'));
        self::assertTrue(Definitions::isNameCodePoint('N'));
        self::assertTrue(Definitions::isNameCodePoint('O'));
        self::assertTrue(Definitions::isNameCodePoint('P'));
        self::assertTrue(Definitions::isNameCodePoint('Q'));
        self::assertTrue(Definitions::isNameCodePoint('R'));
        self::assertTrue(Definitions::isNameCodePoint('S'));
        self::assertTrue(Definitions::isNameCodePoint('T'));
        self::assertTrue(Definitions::isNameCodePoint('U'));
        self::assertTrue(Definitions::isNameCodePoint('V'));
        self::assertTrue(Definitions::isNameCodePoint('W'));
        self::assertTrue(Definitions::isNameCodePoint('X'));
        self::assertTrue(Definitions::isNameCodePoint('Y'));
        self::assertTrue(Definitions::isNameCodePoint('Z'));
        self::assertTrue(Definitions::isNameCodePoint('0'));
        self::assertTrue(Definitions::isNameCodePoint('1'));
        self::assertTrue(Definitions::isNameCodePoint('2'));
        self::assertTrue(Definitions::isNameCodePoint('3'));
        self::assertTrue(Definitions::isNameCodePoint('4'));
        self::assertTrue(Definitions::isNameCodePoint('5'));
        self::assertTrue(Definitions::isNameCodePoint('6'));
        self::assertTrue(Definitions::isNameCodePoint('7'));
        self::assertTrue(Definitions::isNameCodePoint('8'));
        self::assertTrue(Definitions::isNameCodePoint('9'));
        self::assertTrue(Definitions::isNameCodePoint('-'));
        self::assertTrue(Definitions::isNameCodePoint('_'));
        self::assertTrue(Definitions::isNameCodePoint('猫'));
        self::assertTrue(Definitions::isNameCodePoint('あ'));
        self::assertFalse(Definitions::isNameCodePoint(' '));
        self::assertFalse(Definitions::isNameCodePoint("\n"));
        self::assertFalse(Definitions::isNameCodePoint("\t"));
    }

    public function testIsNonPrintableCodePoint(): void
    {
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0000}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0001}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0002}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0003}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0004}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0005}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0006}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0007}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0008}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{000B}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{000E}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{000F}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0010}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0011}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0010}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0012}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0013}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0014}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0015}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0016}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0017}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0018}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{0019}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{001A}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{001B}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{001C}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{001D}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{001E}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{001F}"));
        self::assertTrue(Definitions::isNonPrintableCodePoint("\u{007F}"));

        self::assertFalse(Definitions::isNonPrintableCodePoint("\u{0009}"));
        self::assertFalse(Definitions::isNonPrintableCodePoint("\u{000A}"));
        self::assertFalse(Definitions::isNonPrintableCodePoint("\u{0009}"));
        self::assertFalse(Definitions::isNonPrintableCodePoint("\u{000A}"));
        self::assertFalse(Definitions::isNonPrintableCodePoint("\u{0020}"));
        self::assertFalse(Definitions::isNonPrintableCodePoint("\u{0021}"));
        self::assertFalse(Definitions::isNonPrintableCodePoint("\u{0022}"));
        self::assertFalse(Definitions::isNonPrintableCodePoint("\u{007E}"));
        self::assertFalse(Definitions::isNonPrintableCodePoint("\u{0080}"));
    }
}
