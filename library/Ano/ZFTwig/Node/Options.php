<?php
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;

class Ano_ZFTwig_Node_Options
{
    /**
     * @param Node $options
     * @return array<string, Node>
     */
    public static function fromNode(Node $options)
    {
        if (!$options instanceof ArrayExpression) {
            return array();
        }

        $namedOptions = array();
        foreach ($options->getKeyValuePairs() as $pair) {
            $key = $pair['key'];
            if ($key instanceof ConstantExpression && is_string($key->getAttribute('value'))) {
                $namedOptions[$key->getAttribute('value')] = $pair['value'];
            }
        }

        return $namedOptions;
    }
}
