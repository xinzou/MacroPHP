<?php

/* /home/index.twig */
class __TwigTemplate_e8eb823f4aa241d06923e8d700e88facb19fedd58b106b1dae6765312125b82f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<script type=\"text/javascript\" src=\"http://libs.baidu.com/jquery/1.11.1/jquery.min.js\"></script>
<script type=\"text/javascript\" src=\"/js/main.js\"></script>
dsafasfds";
        // line 3
        echo twig_escape_filter($this->env, (isset($context["somevar"]) ? $context["somevar"] : null), "html", null, true);
    }

    public function getTemplateName()
    {
        return "/home/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  23 => 3,  19 => 1,);
    }
}
/* <script type="text/javascript" src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>*/
/* <script type="text/javascript" src="/js/main.js"></script>*/
/* dsafasfds{{somevar}}*/
