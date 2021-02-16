<?php

namespace Olveneer\TwigComponentsBundle\Twig\tag;

use Olveneer\TwigComponentsBundle\Exception\GetSyntaxException;
use Twig\Compiler;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;
use Twig\Node\NodeOutputInterface;

/**
 * Class SlotNode
 * @package Olveneer\TwigComponentsBundle\Slot
 */
class ComponentNode extends Node implements NodeOutputInterface
{
    /**
     * @var array
     */
    private $slotted;

    /**
     * ComponentNode constructor.
     *
     * @param NameExpression       $expr
     * @param ArrayExpression|null $variables
     * @param int                  $lineno
     * @param array                $slotted
     * @param null                 $tag
     */
    public function __construct(
        NameExpression $expr,
        ?ArrayExpression
        $variables,
        int $lineno,
        $slotted = [],
        $tag = null
    ) {
        $nodes = array('expr' => $expr);

        if (null !== $variables) {
            $nodes['variables'] = $variables;
        }

        $this->slotted = $slotted;

        parent::__construct($nodes, [], $lineno, $tag);
    }

    /**
     * @param Compiler $compiler
     * @throws GetSyntaxException
     */
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $exprNode = $this->getNode('expr');

        if (!$exprNode instanceof NameExpression) {
            throw new GetSyntaxException("Use unquoted strings for the {% get %} tag.");
        }

        $componentName = $exprNode->getAttribute('name');

        $compiler->write('$props = ');

        if ($this->hasNode('variables')) {
            $compiler->subcompile($this->getNode('variables'));
        } else {
            $compiler->raw('[]');
        }

        $compiler->write(';')
            ->raw(PHP_EOL);;

        $compiler
            ->raw('$renderer = $this->extensions[')
            ->string("Olveneer\TwigComponentsBundle\Twig\SlotExtension")
            ->write(']->getRenderer();')->raw(PHP_EOL);

        $compiler
            ->write('$renderer->openTarget(')
            ->string($componentName)
            ->raw(',')
            ->string(serialize($this->slotted))
            ->raw(', $context')
            ->raw(');')
            ->raw(PHP_EOL);

        $compiler
            ->raw('echo ')
            ->write('$renderer->renderComponent(')
            ->string($componentName)
            ->raw(', $props')
            ->raw("); ")
            ->raw(PHP_EOL);

        $compiler
            ->write('$renderer->closeTarget(); ')->raw(PHP_EOL);
    }
}
