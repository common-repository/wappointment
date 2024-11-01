<?php

namespace WappoVendor\Egulias\EmailValidator\Parser;

use WappoVendor\Egulias\EmailValidator\EmailLexer;
use WappoVendor\Egulias\EmailValidator\Result\InvalidEmail;
use WappoVendor\Egulias\EmailValidator\Result\Reason\ConsecutiveDot;
use WappoVendor\Egulias\EmailValidator\Result\Result;
use WappoVendor\Egulias\EmailValidator\Result\ValidEmail;
abstract class PartParser
{
    /**
     * @var array
     */
    protected $warnings = [];
    /**
     * @var EmailLexer
     */
    protected $lexer;
    public function __construct(EmailLexer $lexer)
    {
        $this->lexer = $lexer;
    }
    public abstract function parse() : Result;
    /**
     * @return \Egulias\EmailValidator\Warning\Warning[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }
    protected function parseFWS() : Result
    {
        $foldingWS = new FoldingWhiteSpace($this->lexer);
        $resultFWS = $foldingWS->parse();
        $this->warnings = \array_merge($this->warnings, $foldingWS->getWarnings());
        return $resultFWS;
    }
    protected function checkConsecutiveDots() : Result
    {
        if ($this->lexer->token['type'] === EmailLexer::S_DOT && $this->lexer->isNextToken(EmailLexer::S_DOT)) {
            return new InvalidEmail(new ConsecutiveDot(), $this->lexer->token['value']);
        }
        return new ValidEmail();
    }
    protected function escaped() : bool
    {
        $previous = $this->lexer->getPrevious();
        return $previous && $previous['type'] === EmailLexer::S_BACKSLASH && $this->lexer->token['type'] !== EmailLexer::GENERIC;
    }
}
