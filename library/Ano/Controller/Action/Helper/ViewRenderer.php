<?php
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
 * ViewRenderer
 * Supports template engine switching by annotation system
 * Syntax : @render php
 * 
 *
 * @package    Ano_Controller_Action
 * @subpackage Helper
 * @author     Mickael Perraud <perraud.mickael@orange.fr>
 */

class Ano_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer
{

    /**
     * Choose and affect template engine to use
     * @see Zend_Controller_Action_Helper_Abstract::preDispatch()
     */
    public function preDispatch()
    {
        $controller  = $this->getActionController();
        $request     = $this->getRequest();
        $action      = $request->getActionName();
        $rendererName = $this->getRendererSwitcher($action);

        if (is_object($this->view) && method_exists($this->view, 'setTemplateEngine')) {
            $this->view->setTemplateEngine($rendererName);
        }
    }

    /**
     * Search in annotations for template engine
     * @param string $action
     */
    public function getRendererSwitcher($action)
    {
        $controller = $this->getActionController();
        $dispatcher = $this->getFrontController()->getDispatcher();
        $action     = (string) $dispatcher->formatActionName($action);

        if (!is_object($controller) || !method_exists($controller, $action)) {
            return 'php';
        }

        try {
            $method = new ReflectionMethod($controller, $action);
            $docComment = $method->getDocComment();
        } catch (ReflectionException $e) {
            return 'php';
        }

        return $this->parseDocComment($docComment, 'render', 'php');
    }

    /**
     * Parse PHPDoc to find a special tag and associated value
     * @param string|false $string String to parse
     * @param string $tag Tag to search
     * @param string $default Default value if no tag found
     */
    public function parseDocComment($string, $tag, $default = '')
    {
        if (!is_string($string) || $string === '') {
            return $default;
        }

        $pattern = '/@' . preg_quote((string) $tag, '/') . '(?::)?\s+([^\r\n]+)/';
        if (preg_match($pattern, $string, $matches) !== 1) {
            return $default;
        }

        return trim(preg_replace('/\s*\*\/\s*$/', '', $matches[1]));
    }
}
