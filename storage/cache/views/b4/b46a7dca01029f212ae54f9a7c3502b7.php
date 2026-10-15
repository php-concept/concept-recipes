<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Sandbox\SecurityNotAllowedTestError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* contact.twig */
class __TwigTemplate_addc9700e3956b36b64454b8c2238354 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <meta name=\"description\" content=\"FormRequest demo — Concept 2\">
    <title>Contact · Concept 2</title>
    <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
    <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
    <link href=\"https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Figtree:ital,wght@0,400;0,500;0,600;1,400&display=swap\" rel=\"stylesheet\">
    <link rel=\"stylesheet\" href=\"/vendor/bootstrap/css/bootstrap.min.css\">
    <link rel=\"stylesheet\" href=\"/vendor/bootstrap-icons/bootstrap-icons.min.css\">
    <link rel=\"stylesheet\" href=\"/css/home.css\">
    <link rel=\"stylesheet\" href=\"/css/contact.css\">
</head>
<body class=\"contact-page\">
    <nav class=\"promo-nav promo-nav-static\">
        <div class=\"container-xxl d-flex align-items-center justify-content-between\">
            <a class=\"brand\" href=\"/\">Concept <span>2</span></a>
            <a class=\"btn-promo btn-promo-ghost\" href=\"/\">Home</a>
        </div>
    </nav>

    <main class=\"contact-main\">
        <div class=\"container-xxl\">
            <p class=\"section-kicker\">Step 5 · Session and CSRF</p>
            <h1 class=\"section-title\">FormRequest demo</h1>
            <p class=\"section-lead\">
                Submit the form with invalid or valid data.
                POST is protected by a CSRF token in the session.
            </p>

            ";
        // line 33
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["flashes"] ?? null), "error", [], "any", true, true, false, 33)) {
            // line 34
            yield "                <div class=\"alert alert-danger border-0\" role=\"alert\">
                    ";
            // line 35
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, ($context["flashes"] ?? null), "error", [], "any", false, false, false, 35));
            foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
                // line 36
                yield "                        <div>";
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["message"], "html", null, true);
                yield "</div>
                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['message'], $context['_parent']);
            $context = array_intersect_key($context, $_parent);
            $context += $_parent;
            // line 38
            yield "                </div>
            ";
        }
        // line 40
        yield "
            ";
        // line 41
        if ((($tmp = ($context["success"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 42
            yield "                <div class=\"alert alert-success border-0\" role=\"status\">
                    <strong>OK.</strong> Data passed validation:
                    <code>";
            // line 44
            yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["validated"] ?? null), "name", [], "any", false, false, false, 44), "html", null, true);
            yield "</code>,
                    <code>";
            // line 45
            yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["validated"] ?? null), "email", [], "any", false, false, false, 45), "html", null, true);
            yield "</code>
                </div>
            ";
        }
        // line 48
        yield "
            <form class=\"contact-form\" method=\"post\" action=\"/contact\" novalidate>
                <input type=\"hidden\" name=\"_csrf_token\" value=\"";
        // line 50
        yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["csrf_token"] ?? null), "html", null, true);
        yield "\">
                <div class=\"mb-3\">
                    <label class=\"form-label\" for=\"name\">Name</label>
                    <input
                        type=\"text\"
                        class=\"form-control";
        // line 55
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "name", [], "any", true, true, false, 55)) {
            yield " is-invalid";
        }
        yield "\"
                        id=\"name\"
                        name=\"name\"
                        value=\"";
        // line 58
        yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["old"] ?? null), "name", [], "any", true, true, false, 58)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["old"] ?? null), "name", [], "any", false, false, false, 58), "")) : ("")), "html", null, true);
        yield "\"
                        autocomplete=\"name\"
                    >
                    ";
        // line 61
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "name", [], "any", true, true, false, 61)) {
            // line 62
            yield "                        <div class=\"invalid-feedback d-block\">
                            ";
            // line 63
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "name", [], "any", false, false, false, 63));
            $context['loop'] = [
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            ];
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
                // line 64
                yield "                                ";
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["message"], "html", null, true);
                if ( !(($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 64)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    yield "<br>";
                }
                // line 65
                yield "                            ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['revindex0'], $context['loop']['revindex'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['message'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent);
            $context += $_parent;
            // line 66
            yield "                        </div>
                    ";
        }
        // line 68
        yield "                </div>

                <div class=\"mb-3\">
                    <label class=\"form-label\" for=\"email\">Email</label>
                    <input
                        type=\"email\"
                        class=\"form-control";
        // line 74
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "email", [], "any", true, true, false, 74)) {
            yield " is-invalid";
        }
        yield "\"
                        id=\"email\"
                        name=\"email\"
                        value=\"";
        // line 77
        yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["old"] ?? null), "email", [], "any", true, true, false, 77)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["old"] ?? null), "email", [], "any", false, false, false, 77), "")) : ("")), "html", null, true);
        yield "\"
                        autocomplete=\"email\"
                    >
                    ";
        // line 80
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "email", [], "any", true, true, false, 80)) {
            // line 81
            yield "                        <div class=\"invalid-feedback d-block\">
                            ";
            // line 82
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "email", [], "any", false, false, false, 82));
            $context['loop'] = [
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            ];
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
                // line 83
                yield "                                ";
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["message"], "html", null, true);
                if ( !(($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 83)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    yield "<br>";
                }
                // line 84
                yield "                            ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['revindex0'], $context['loop']['revindex'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['message'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent);
            $context += $_parent;
            // line 85
            yield "                        </div>
                    ";
        }
        // line 87
        yield "                </div>

                <div class=\"mb-4\">
                    <label class=\"form-label\" for=\"message\">Message</label>
                    <textarea
                        class=\"form-control";
        // line 92
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "message", [], "any", true, true, false, 92)) {
            yield " is-invalid";
        }
        yield "\"
                        id=\"message\"
                        name=\"message\"
                        rows=\"4\"
                    >";
        // line 96
        yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["old"] ?? null), "message", [], "any", true, true, false, 96)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["old"] ?? null), "message", [], "any", false, false, false, 96), "")) : ("")), "html", null, true);
        yield "</textarea>
                    ";
        // line 97
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "message", [], "any", true, true, false, 97)) {
            // line 98
            yield "                        <div class=\"invalid-feedback d-block\">
                            ";
            // line 99
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, ($context["errors"] ?? null), "message", [], "any", false, false, false, 99));
            $context['loop'] = [
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            ];
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
                // line 100
                yield "                                ";
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["message"], "html", null, true);
                if ( !(($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 100)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    yield "<br>";
                }
                // line 101
                yield "                            ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['revindex0'], $context['loop']['revindex'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['message'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent);
            $context += $_parent;
            // line 102
            yield "                        </div>
                    ";
        }
        // line 104
        yield "                </div>

                <button type=\"submit\" class=\"btn-promo btn-promo-primary\">Submit</button>
            </form>
        </div>
    </main>
</body>
</html>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "contact.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  328 => 104,  324 => 102,  309 => 101,  303 => 100,  286 => 99,  283 => 98,  281 => 97,  277 => 96,  268 => 92,  261 => 87,  257 => 85,  242 => 84,  236 => 83,  219 => 82,  216 => 81,  214 => 80,  208 => 77,  200 => 74,  192 => 68,  188 => 66,  173 => 65,  167 => 64,  150 => 63,  147 => 62,  145 => 61,  139 => 58,  131 => 55,  123 => 50,  119 => 48,  113 => 45,  109 => 44,  105 => 42,  103 => 41,  100 => 40,  96 => 38,  86 => 36,  82 => 35,  79 => 34,  77 => 33,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "contact.twig", "/var/www/html/resources/views/contact.twig");
    }
}
