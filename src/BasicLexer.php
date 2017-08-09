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

class BasicLexer implements LexerInterface
{
    protected $normalizeNewlines = true;
    protected $normalizeTabs = true;
    protected $terminals = [];
    protected $ignoredTokens = [];

    /**
     * Constructor
     *
     * @param array $terminal List of terminals
     *  e.g:
     *    [
     *      "/^([)/" => "T_BRAKET_BEGIN"
     *    ]
     */
    public function __construct(array $terminals)
    {
        $this->terminals = $terminals;
    }

    /**
     * Avoids lexer normalize CRLF to LF
     *
     * @return BasicLexer The BasicLexer itself
     */
    public function noNormalizeNewlines() : BasicLexer
    {
        $this->normalizeNewlines = false;

        return $this;
    }

    /**
     * Avoids lexer normalize tabs to spaces
     *
     * @return BasicLexer The BasicLexer itself
     */
    public function noNormalizeTabs() : BasicLexer
    {
        $this->normalizeTabs = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function tokenize(string $input) : TokenStream
    {
        $tokens = [];

        $input = $this->cleanup($input);
        $lines = explode("\n", $input);

        foreach ($lines as $number => $line) {
            $offset = 0;

            while ($offset < strlen($line)) {
                list($name, $matches) = $this->match($line, $number, $offset);

                if (isset($matches[1])) {
                    $token = new Token($matches[1], $name, $number + 1);
                    $this->processToken($token, $matches);
                    $tokens[] = $token;
                }

                $offset += strlen($matches[0]);
            }
        }

        return new TokenStream($tokens);
    }

    /**
     * Returns the first match with the list of terminals
     *
     * @return array An array with the following keys:
     *   [0] (string): name of the token
     *   [1] (array): matches of the regular expression
     *
     * @throws SyntaxErrorException If the line does not contain any token
     */
    protected function match(string $line, int $lineNumber, int $offset) : array
    {
        $restLine = substr($line, $offset);

        foreach ($this->terminals as $pattern => $name) {
            if (preg_match($pattern, $restLine, $matches)) {
                return [
                    $name,
                    $matches,
                ];
            }
        }

        throw new SyntaxErrorException(sprintf('Lexer error: unable to parse "%s" at line %s.', $line, $lineNumber));
    }

    /**
     * Applies additional actions over a token.
     *
     * Implement this method if you need to do changes after a token was found.
     * This method is invoked for each token found
     *
     * @param Token $token The token
     * @param string[] $matches Set of matches from the regular expression
     *
     * @return void
     */
    protected function processToken(Token $token, array $matches) : void
    {
    }

    /**
     * Normalizes the newlines and tab characters depeding on the lexer
     * configuration
     *
     * @see noNormalizeNewlines() To avoid normalize newlines
     * @see noNormalizeTabs() To avoid normalize tab characters
     *
     * @param string $value The text
     *
     * @return string
     */
    protected function cleanup(string $value) : string
    {
        $text = $value;

        if ($this->normalizeNewlines) {
            $text = str_replace(["\r\n", "\r"], "\n", $text);
        }

        if ($this->normalizeTabs) {
            $text = str_replace("\t", " ", $text);
        }

        return $value;
    }
}
