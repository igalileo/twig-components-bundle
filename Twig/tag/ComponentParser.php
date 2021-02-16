<?php

namespace Olveneer\TwigComponentsBundle\Twig\tag;

use Twig\Error\SyntaxError;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Class SlotTokenParser
 *
 * @package Olveneer\TwigComponentsBundle\Slot
 */
class ComponentParser extends AbstractTokenParser
{
    /**
     * @var string
     */
    private $endTag = 'endget';

    /**
     * @param Token $token
     * @return ComponentNode
     * @throws SyntaxError
     */
    public function parse(Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        list($variables, $slotted) = $this->parseArguments();

        return new ComponentNode($expr, $variables, $token->getLine(), $slotted, $this->getTag());
    }

    /**
     * @return array
     * @throws SyntaxError
     */
    protected function parseArguments()
    {
        $stream = $this->parser->getStream();

        $variables = null;

        if ($stream->nextIf(/* Twig_Token::NAME_TYPE */ 5, 'with')) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(/* Twig_Token::BLOCK_END_TYPE */ 3);

        $body = $this->parser->subparse(array($this, 'decideComponentFork'));

        $slotted = [];
        $end = false;
        while (!$end) {
            switch ($stream->next()->getValue()) {
                case 'slot':
                    $name = $stream->getCurrent()->getValue();
                    $stream->expect(Token::NAME_TYPE);

                    $stream->expect(/* Twig_Token::BLOCK_END_TYPE */ 3);
                    $slotNodes = $this->parser->subparse(array($this, 'decideComponentFork'));

                    $slotted[$name] = $slotNodes;
                    break;

                case 'endslot':
                    $stream->expect(/* Twig_Token::BLOCK_END_TYPE */ 3);
                    $body = $this->parser->subparse(array($this, 'decideComponentFork'));
                    break;

                case $this->endTag:
                    $end = true;
                    break;

                default:
                    throw new SyntaxError(sprintf('Unexpected end of template. Twig was looking for the following tag "else", "elseif", or "endif" to close the "if" block started at line %d).', $lineno), $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
        }

        $stream->expect(/* Twig_Token::BLOCK_END_TYPE */ 3);

        return [$variables, $slotted];
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return 'get';
    }

    /**
     * Callback called at each tag name when subparsing, must return
     * true when the expected end tag is reached.
     *
     * @param Token $token
     * @return bool
     */
    public function decideComponentEnd(Token $token)
    {
        return $token->test([$this->endTag]);
    }

    /**
     * Callback called at each tag name when subparsing, must return
     * true when the expected end tag is reached.
     *
     * @param Token $token
     * @return bool
     */
    public function decideComponentFork(Token $token)
    {
        return $token->test(['slot', 'endslot', $this->endTag]);
    }
}
