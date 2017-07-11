<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$_SERVER['DOCUMENT_ROOT'] = realpath('');

require 'vendor/autoload.php';
require 'application/providers/routes/autoload.php';

use PHPUnit\Framework\TestCase;

class RenderTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        
    }

    public function testGetTemplate()
    {
        $template = Mira\Render::getTemplate('mira', 'home');
        $this->assertEquals($template, true);
        $template = Mira\Render::getTemplate('mira', 'base');
        $this->assertEquals($template, true);
    }

    public function testPatternMatcher()
    {
        $this->assertEquals(Mira\Render::matcher('if'), '/(\s*)@if(\s*\(.*\))/');
    }

    public function testHeaderFooterWithTemplateAndSubdomain()
    {
        $_SERVER['HTTP_HOST'] = "mira.domainName.com";
        $config[] = $config['header'] = "mira.base";
        $this->assertEquals(Mira\Render::getHeader($config), true);

        $_SERVER['HTTP_HOST'] = "mira.domainName.com";
        $config[] = $config['footer'] = "mira.footer";
        $this->assertEquals(Mira\Render::getFooter($config), true);
    }

    public function testHeaderFooterWithTemplateWithoutSubdomain()
    {
        $config[] = $config['header'] = "mira.base";
        $this->assertEquals(Mira\Render::getHeader($config), true);
        $config[] = $config['footer'] = "mira.footer";
        $this->assertEquals(Mira\Render::getFooter($config), true);
    }

    public function testHeaderFooterWithTemplateWithoutHeaderAndFooter()
    {
        $config['header'] = "";
        $this->assertEquals(Mira\Render::getHeader($config), false);
        $config['footer'] = "";
        $this->assertEquals(Mira\Render::getFooter($config), false);
    }

    public function testTemplateExtends()
    {
        $this->assertEquals(Mira\Render::templateExtends('mira.home'), true);
    }

    public function testRegister()
    {
        $output = "output of a file";
        $this->assertEquals(Mira\Render::register('/output/', '', $output), ' of a file');
    }

    public function testGetSubdomain()
    {
        $_SERVER['HTTP_HOST'] = "domain.domainName.com";
        $this->assertEquals(Mira\Render::getSubdomain(), 'domain');
    }

    public function testMultiTenancy()
    {
        $_SERVER['HTTP_HOST'] = "domain.domainName.com";
        $this->assertEquals(Mira\Render::multiTenancy(), true);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRedirect()
    {
        $this->assertEquals(Mira\Render::redirect('/'), true);
    }

    public function testGetConfigWithSplitParamters()
    {
        $this->assertEquals(Mira\Render::getConfig('mira.home'), []);
    }

    public function testGetConfigWithoutSplitParamters()
    {
        $this->assertEquals(Mira\Render::getConfig('mira'), []);
    }

    public function testGetConfigWithAppThatDoesntExist()
    {
        $this->assertTrue(is_array(Mira\Render::getConfig('noneexistantapp')));
    }

    public function testCompileMustache()
    {
        $output = "{{ 'testing' }}";
        $this->assertEquals(Mira\Render::compileMustache($output), "<?= 'testing' ?>");
    }

    public function testCompileIfStatements()
    {
        $output = "@if (1) @endif";
        $this->assertEquals(Mira\Render::compileIfStatements($output), "<?php if (1): ?> <?php endif; ?>");
    }

    public function testCompileComments()
    {
        $output = "@comment hey @endcomment";
        $this->assertEquals(Mira\Render::compileComments($output), "<?php if (0): ?> hey<?php endif; ?>");
    }

    public function testCompileUnless()
    {
        $output = "@unless (1) @endunless";
        $this->assertEquals(Mira\Render::compileUnless($output), "<?php if ( ! ( (1))): ?><?php endif; ?>");
    }

    public function testCompileUse()
    {
        $output = "@use Mira\Testing";
        $this->assertEquals(Mira\Render::compileUse($output), "<?php use  Mira\Testing; ?>");
    }

    public function testGetTemplateTags()
    {
        $output = "@use Mira\Testing";
        $this->assertEquals(Mira\Render::getTemplateTags($output), "<?php use  Mira\Testing; ?>");
    }

    public function testGetTemplateEngine()
    {
        $template = "mira.home";
        $template = explode(".", $template);
        $this->assertEquals(Mira\Render::templateEngine($template, []), "");
    }

    public function testGetTemplateEngineWithMultiTenancy()
    {
        $_SERVER['HTTP_HOST'] = "mira.domainName.com";
        $template = "mira.home";
        $template = explode(".", $template);
        $this->assertEquals(Mira\Render::templateEngine($template, []), "");
    }

    public function testGetTemplateEngineWrongTemplate()
    {
        $_SERVER['HTTP_HOST'] = "mira.domainName.com";
        $template = "mira.wrongTemplate";
        $template = explode(".", $template);
        $this->assertEquals(Mira\Render::templateEngine($template, []), "");
    }

    public function testRenderViewWithMultiTenancy(){
        $_SERVER['HTTP_HOST'] = "mira.domainName.com";
        $template = "mira.home";
        $this->assertEquals(Mira\Render::view($template, []), true);
    }

    public function testRenderViewWithoutMultiTenancy(){
        $template = "mira.home";
        $this->assertEquals(Mira\Render::view($template, []), true);
    }

    public function testRenderViewWithIncorrectTemplate(){
        $template = "mira.wrongtemplate";
        $this->assertEquals(Mira\Render::view($template, []), true);
    }

    public function testRenderViewWithIncorrectApp()
    {
        $template = "wrongApp.wrongtemplate";
        $this->assertEquals(Mira\Render::view($template, []), true);
    }

    public function testRenderViewWithNoApp()
    {
        $template = "";
        $this->assertEquals(Mira\Render::view($template, []), false);
    }












}
