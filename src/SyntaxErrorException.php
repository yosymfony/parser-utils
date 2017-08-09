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

/**
 * Exception thrown when an error occurs during parsing or tokenizing
 */
class SyntaxErrorException extends \RuntimeException
{
    protected $token;
    protected $rawMessage;

    /**
     * Constructor
     *
     * @param string $message The error messsage
     * @param Token|null $token The token
     * @param \Exception|null $previous The previous exceptio
     */
    public function __construct(string $message, Token $token = null, \Exception $previous = null)
    {
        $this->rawMessage = $message;
        $this->updateMessage();

        parent::__construct($this->message, 0, $previous);
    }

    /**
     * Sets the token associated to the exception
     *
     * @param Token $token The token
     */
    public function setToken(Token $token) : void
    {
        $this->token = $token;
    }

    /**
     * Returns the token associated to the exception
     *
     * @return Token
     */
    public function getToken() : Token
    {
        return $this->token;
    }

    /**
     * Updates the messsage of the exception.
     *
     * Inspired by ParseException from Symfony Yaml component
     *
     * @see https://github.com/symfony/symfony/blob/master/src/Symfony/Component/Yaml/Exception/ParseException.php
     */
    protected function updateMessage() : void
    {
        $dot = false;
        $this->message = $this->rawMessage;

        if ('.' === substr($this->message, -1)) {
            $this->message = substr($this->message, 0, -1);
            $dot = true;
        }

        if ($this->token) {
            $this->message .= sprintf(" token: %s", $this->token);
        }

        if ($dot) {
            $this->message .= '.';
        }
    }
}
