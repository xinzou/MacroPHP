<?php

/* /home/index.twig */
class __TwigTemplate_cfb4ab1822fcbb371792b260223c5f60776b772575f579f2e7618358cbbb9381 extends Twig_Template
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
