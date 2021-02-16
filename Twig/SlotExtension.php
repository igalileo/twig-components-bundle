<?php

namespace Olveneer\TwigComponentsBundle\Twig;

use Olveneer\TwigComponentsBundle\Service\ComponentRenderer;
use Olveneer\TwigComponentsBundle\Twig\tag\ComponentParser;
use Olveneer\TwigComponentsBundle\Twig\tag\SlotParser;
use Twig\Compiler;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;

/**
 * Class SlotExtension
 * @package Olveneer\TwigComponentsBundle\Twig
 */
class SlotExtension extends AbstractExtension
{
    /**
     * @var ComponentRenderer
     */
    private $renderer;

    /**
     * TwigComponentExtension constructor.
     * @param ComponentRenderer $componentRenderer
     */
    public function __construct(ComponentRenderer $componentRenderer)
    {
        $this->renderer = $componentRenderer;
    }

    /**
     * @return array|TokenParserInterface[]
     */
    public function getTokenParsers()
    {
        return [new ComponentParser(), new SlotParser()];
    }

    /**
     * @return ComponentRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return Compiler
     */
    public function createCompiler()
    {
        return new Compiler($this->renderer->getEnv());
    }
}
