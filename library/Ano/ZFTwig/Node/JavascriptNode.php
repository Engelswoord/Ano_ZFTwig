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
 * Compiles javascripts node to PHP.
 * @see Ano_ZFTwig_Extension_Helper
 *
 * @package     Ano_ZFTwig
 * @subpackage  Node
 * @author      Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class Ano_ZFTwig_Node_JavascriptNode extends Node
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
        $source = isset($options['source']) ? $options['source']->getAttribute('value') : 'file';
        $source = ucfirst(strtolower($source));
        $type = isset($options['type']) ? $options['type']->getAttribute('value') : 'text/javascript';
        $attrs = isset($options['attrs']) ? $options['attrs'] : false;
        $offset = isset($options['offset']) ? $options['offset'] : false;

        $compiler
            ->addDebugInfo($this)
            ->write("\$this->env->getView()->headScript()->");

        if (false === $offset) {
            $method = (($mode == 'prepend') ? 'prepend' : 'append') . $source;
            $compiler
                ->write("$method(");
        }
        else {
            $compiler
                ->write('offsetSet' . $source . '(')
                ->subcompile($offset)
                ->raw(', ');
        }
//
        $compiler
            ->subcompile($this->getNode('expr'))
            ->raw(', ')
            ->string($type);

        if ($attrs) {
            $compiler
                ->raw(', ')
                ->subcompile($attrs);
        }

        $compiler->raw(");\n");
    }
}
