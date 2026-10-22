<?php
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

/**
 * This file is part of the Ano_ZFTwig package
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @copyright  Copyright (c) 2010-2011 Benjamin Dulau <benjamin.dulau@gmail.com>
 * @license    New BSD License
 */

/**
 * Compiles stylesheet node to PHP.
 * @see Ano_ZFTwig_Extension_Helper
 *
 * @package     Ano_ZFTwig
 * @subpackage  Node
 * @author      Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class Ano_ZFTwig_Node_StylesheetNode extends Node
{
    protected $contentKey;

    public function __construct(AbstractExpression $expr, AbstractExpression $options, $lineno)
    {
        parent::__construct(array('expr' => $expr, 'options' => $options), array(), $lineno);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Compiler A Twig_Compiler instance
     */
    public function compile(Compiler $compiler)
    {
        $options = Ano_ZFTwig_Node_Options::fromNode($this->getNode('options'));
        $mode = isset($options['mode']) ? $options['mode']->getAttribute('value') : 'append';
        $media = isset($options['media']) ? $options['media']->getAttribute('value') : 'screen';
        $attrs = isset($options['attrs']) ? $options['attrs'] : false;
        $offset = isset($options['offset']) ? $options['offset'] : false;
        $conditional = isset($options['conditional']) ? $options['conditional']->getAttribute('value') : false;

        $compiler
            ->addDebugInfo($this)
            ->write("\$this->env->getView()->headLink()->");

        if (false === $offset) {
            $method = ($mode == 'prepend') ? 'prependStylesheet' : 'appendStylesheet';
            $compiler
                ->write("$method(");
        }
        else {
            $compiler
                ->write('offsetSetStylesheet(')
                ->subcompile($offset)
                ->raw(', ');
        }

        $compiler
            ->subcompile($this->getNode('expr'))
            ->raw(', ')
            ->string($media)
            ->raw(', ')
            ->string($conditional);

        if ($attrs) {
            $compiler
                ->raw(', ')
                ->subcompile($attrs);
        }

        $compiler->raw(");\n");
    }
}
