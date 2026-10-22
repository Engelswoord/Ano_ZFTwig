<?php
use PHPUnit\Framework\TestCase;
use Twig\Loader\ArrayLoader;

class TwigIntegrationTest extends TestCase
{
    public function testCustomTagsCompileWithTwigThree(): void
    {
        $template = "{% headTitle 'title' %}"
            . "{% javascript 'js/app.js' %}"
            . "{% stylesheet 'css/app.css' %}"
            . "{% meta, {'name': 'description', 'content': 'sample'} %}"
            . "{% hlp 'someHelper' %}"
            . "{% layout 'content' %}"
            . "{% trans 'hello' %}"
            . "{{ url('route') }}";
        $loader = new ArrayLoader(array('integration' => $template));
        $environment = new Ano_ZFTwig_Environment(new Ano_View(), $loader, array('cache' => false));
        $environment->addExtension(new Ano_ZFTwig_Extension_HelperExtension());
        $environment->addExtension(new Ano_ZFTwig_Extension_TransExtension());

        $compiled = $environment->compile(
            $environment->parse($environment->tokenize($loader->getSourceContext('integration')))
        );

        $this->assertNotSame('', $compiled);
    }

    public function testCustomEnvironmentRendersTwigTemplate(): void
    {
        $loader = new ArrayLoader(array('integration' => 'Hello {{ name }}'));
        $environment = new Ano_ZFTwig_Environment(new Ano_View(), $loader, array('cache' => false));

        $this->assertSame('Hello PHP 8.4', $environment->render('integration', array('name' => 'PHP 8.4')));
    }
}
