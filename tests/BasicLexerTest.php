<?php
/*
 * This file is part of the Yosymfony\ParserUtils package.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ParserUtils\Test;

use PHPUnit\Framework\TestCase;
use Yosymfony\ParserUtils\BasicLexer;
use Yosymfony\ParserUtils\Token;

class BasicLexerTest extends TestCase
{
    public function testTokenizeMustReturnsTheListOfTokens()
    {
        $lexer = new BasicLexer([
            '/^([0-9]+)/x' => 'T_NUMBER',
            '/^(\+)/x' => 'T_PLUS',
            '/^(-)/x' => 'T_MINUS',
        ]);
        $tokens = $lexer->tokenize('1+2')->getAll();

        $this->assertEquals([
            new Token('1', 'T_NUMBER', 1),
            new Token('+', 'T_PLUS', 1),
            new Token('2', 'T_NUMBER', 1),
        ], $tokens);
    }

    public function testTokenizeMustReturnsTheListOfTokensWithoutThoseDoNotHaveParenthesizedSupatternInTerminalSymbols()
    {
        $lexer = new BasicLexer([
            '/^([0-9]+)/' => 'T_NUMBER',
            '/^(\+)/' => 'T_PLUS',
            '/^(-)/' => 'T_MINUS',
            '/^\s+/' => 'T_SPACE',
        ]);

        $tokens = $lexer->tokenize('1 + 2')->getAll();

        $this->assertEquals([
            new Token('1', 'T_NUMBER', 1),
            new Token('+', 'T_PLUS', 1),
            new Token('2', 'T_NUMBER', 1),
        ], $tokens, 'T_SPACE is not surround with (). e.g: ^(\s+)');
    }

    public function testTokenizeWithEmptyStringMustReturnsZeroTokens()
    {
        $lexer = new BasicLexer([
            '/^([0-9]+)/' => 'T_NUMBER',
            '/^(\+)/' => 'T_PLUS',
            '/^(-)/' => 'T_MINUS',
        ]);

        $tokens = $lexer->tokenize('')->getAll();

        $this->assertCount(0, $tokens);
    }
}
