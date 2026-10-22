<?php
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;
use Twig\Compiler;

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
 * Compiles meta node to PHP.
 * @see Ano_ZFTwig_Extension_Helper
 *
 * @package     Ano_ZFTwig
 * @subpackage  Node
 * @author      Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class Ano_ZFTwig_Node_MetaNode extends Node
{
    protected $contentKey;

    public function __construct(AbstractExpression $options, $lineno)
    {
        parent::__construct(array('options' => $options), array(), $lineno);
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
        $name = isset($options['name']) ? $options['name'] : false;
        $httpEquiv = isset($options['http-equiv']) ? $options['http-equiv'] : false;
        $content = isset($options['content']) ? $options['content'] : '';
        $offset = isset($options['offset']) ? $options['offset'] : false;

        $compiler
            ->addDebugInfo($this)
            ->write("\$this->env->getView()->headMeta()->");

        $method = '';
        $key = null;
        if (false === $offset) {
            if (false !== $name) {
                $method = ($mode == 'prepend') ? 'prependName' : 'appendName';
                $key = $name;
            }
            else if (false !== $httpEquiv) {
                $method = ($mode == 'prepend') ? 'prependHttpEquiv' : 'appendHttpEquiv';
                $key = $httpEquiv;
            }

            $compiler
                ->write("$method(");
        }
        else {
            if (false !== $name) {
                $method = 'offsetSetName';
                $key = $name;
            }
            else if (false !== $httpEquiv) {
                $method = 'offsetSetHttpEquiv';
                $key = $httpEquiv;
            }

            $compiler
                ->write("$method(")
                ->subcompile($offset)
                ->raw(', ');
        }

        if (null === $key) {
            throw new LogicException('A meta tag requires either a name or an http-equiv option.');
        }

        $compiler->subcompile($key)->raw(', ');
        if ($content instanceof Node) {
            $compiler->subcompile($content);
        } else {
            $compiler->string($content);
        }
        $compiler->raw(");\n");
    }
}
