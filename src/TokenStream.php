<?php
/*
 * This file is part of the Yosymfony\ParserUtils package.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yosymfony\ParserUtils;

class TokenStream
{
    protected $tokens;
    protected $index = -1;

    /**
     * Constructor
     *
     * @param Token[] List of tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Moves the pointer one token forward
     *
     * @return Token|null The token or null if there are not more tokens
     */
    public function moveNext() : ?Token
    {
        return $this->tokens[++$this->index] ?? null;
    }

    /**
     * Matches the next token. This method moves the pointer one token forward
     * if an error does not occur
     *
     * @param string $tokenName The name of the token
     *
     * @return string The value of the token
     *
     * @throws SyntaxErrorException If the next token does not match
     */
    public function matchNext(string $tokenName) : string
    {
        $token = $this->moveNext();
        --$this->index;

        if ($token->getName() == $tokenName) {
            return $this->moveNext()->getValue();
        }

        throw new SyntaxErrorException(sprintf(
            'Syntax error: expected token with name "%s" instead of "%s" at line %s.',
            $tokenName,
            $token->getName(),
            $token->getLine()));
    }

    /**
     * Checks if the next token matches with the token name passed as argument
     *
     * @param string $tokenName The name of the token
     *
     * @return bool
     */
    public function isNext(string $tokenName) : bool
    {
        $token = $this->moveNext();
        --$this->index;

        if ($token === null) {
            return false;
        }

        return $token->getName() == $tokenName;
    }

    /**
     * Checks if the following tokens in the stream match with the sequence of tokens
     *
     * @param string[] $tokenNames Sequence of token names
     *
     * @return bool
     */
    public function isNextSequence(array $tokenNames) : bool
    {
        $result = true;
        $currentIndex = $this->index;

        foreach ($tokenNames as $tokenName) {
            $token = $this->moveNext();

            if ($token === null || $token->getName() != $tokenName) {
                $result = false;

                break;
            }
        }

        $this->index = $currentIndex;

        return $result;
    }

    /**
     * Checks if one of the tokens passed as argument is the next token
     *
     * @param string[] $tokenNames List of token names. e.g: 'T_PLUS', 'T_SUB'
     *
     * @return bool
     */
    public function isNextAny(array $tokenNames) : bool
    {
        $token = $this->moveNext();
        --$this->index;

        if ($token === null) {
            return false;
        }

        foreach ($tokenNames as $tokenName) {
            if ($tokenName === $token->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns all tokens
     *
     * @return token[] List of tokens
     */
    public function getAll() : array
    {
        return $this->tokens;
    }

    /**
     * Has pending tokens?
     *
     * @return bool
     */
    public function hasPendingTokens() :bool
    {
        $tokenCount = count($this->tokens);

        if ($tokenCount == 0) {
            return false;
        }

        return $this->index < ($tokenCount - 1);
    }

    /**
     * Resets the stream to the beginning
     */
    public function reset() : void
    {
        $this->index = -1;
    }
}
