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

/* index.twig */
class __TwigTemplate_e576890f2cdfbaea4888a1ee7b4c042d extends Template
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
    <meta name=\"description\" content=\"Concept — Core, Extensions, and Stack for PHP 8.4+\">
    <title>Concept — Core · Extensions · Stack</title>
    <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
    <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
    <link href=\"https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Figtree:ital,wght@0,400;0,500;0,600;1,400&display=swap\" rel=\"stylesheet\">
    <link rel=\"stylesheet\" href=\"/vendor/bootstrap/css/bootstrap.min.css\">
    <link rel=\"stylesheet\" href=\"/vendor/bootstrap-icons/bootstrap-icons.min.css\">
    <link rel=\"stylesheet\" href=\"/css/home.css\">
</head>
<body>
    <nav class=\"promo-nav\">
        <div class=\"container-xxl d-flex align-items-center justify-content-between\">
            <a class=\"brand\" href=\"/\">Concept <span>2</span></a>
            <div class=\"d-flex gap-2\">
                <a class=\"btn-promo btn-promo-ghost\" href=\"/contact\">Validation</a>
                <a class=\"btn-promo btn-promo-ghost\" href=\"#trinity\">Trinity</a>
            </div>
        </div>
    </nav>

    <header class=\"hero\">
        <div class=\"hero-atmosphere\" aria-hidden=\"true\"></div>
        <div class=\"hero-grid\" aria-hidden=\"true\"></div>

        <div class=\"container-xxl position-relative w-100\">
            <div class=\"hero-copy\">
                <p class=\"hero-brand\">Concept<em>2</em></p>
                <h1 class=\"hero-headline\">Three layers. One app.</h1>
                <p class=\"hero-lead\">
                    Strict-typed PHP&nbsp;8.4+ on PSR: Core owns the runtime,
                    Extensions provide capabilities, Stack wires them with a fluent API.
                </p>
                <div class=\"hero-cta\">
                    <a class=\"btn-promo btn-promo-primary\" href=\"#trinity\">How it works</a>
                    <a class=\"btn-promo btn-promo-ghost\" href=\"/contact\">Validation demo</a>
                </div>
            </div>

            <div class=\"hero-visual\" aria-hidden=\"true\">
                <div class=\"layer-plane layer-stack\">Stack <span>assembly</span></div>
                <div class=\"layer-plane layer-extensions\">Extensions <span>capabilities</span></div>
                <div class=\"layer-plane layer-core\">Core <span>runtime</span></div>
            </div>
        </div>
    </header>

    <section class=\"trinity\" id=\"trinity\">
        <div class=\"container-xxl\">
            <p class=\"section-kicker\">Trinity</p>
            <h2 class=\"section-title\">Core · Extensions · Stack</h2>
            <p class=\"section-lead\">
                Not a batteries-included monolith — a platform with clear boundaries:
                enable only what the app needs.
            </p>

            <div class=\"trinity-row\">
                <article class=\"trinity-item\" data-layer=\"core\">
                    <h3 class=\"trinity-name\"><i class=\"trinity-dot\" aria-hidden=\"true\"></i> Core</h3>
                    <p class=\"trinity-pkg\">php-concept/core-2</p>
                    <p class=\"trinity-text\">
                        A thin core: container, routing, dispatch.
                        No config magic and no dependency on extensions.
                    </p>
                </article>
                <article class=\"trinity-item\" data-layer=\"ext\">
                    <h3 class=\"trinity-name\"><i class=\"trinity-dot\" aria-hidden=\"true\"></i> Extensions</h3>
                    <p class=\"trinity-pkg\">php-concept/extension-*</p>
                    <p class=\"trinity-text\">
                        Separate packages: HTTP, View, Validation, Database, Logging…
                        Glue passes them explicit parameters.
                    </p>
                </article>
                <article class=\"trinity-item\" data-layer=\"stack\">
                    <h3 class=\"trinity-name\"><i class=\"trinity-dot\" aria-hidden=\"true\"></i> Stack</h3>
                    <p class=\"trinity-pkg\">php-concept/stack</p>
                    <p class=\"trinity-text\">
                        Fluent bricks such as <code>addHttp()</code> and <code>addView()</code> —
                        they assemble the chosen providers for the container.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <section class=\"flow\">
        <div class=\"container-xxl\">
            <p class=\"section-kicker\">Flow</p>
            <h2 class=\"section-title mb-4\">From request to response</h2>
            <div class=\"flow-line\">
                <span class=\"flow-chip core\">Core</span>
                <span class=\"flow-arrow\">·</span>
                <span class=\"flow-chip ext\">Extensions</span>
                <span class=\"flow-arrow\">·</span>
                <span class=\"flow-chip stack\">Stack</span>
                <span class=\"flow-arrow\">→</span>
                <span>providers() · handle()</span>
            </div>
        </div>
    </section>

    <footer class=\"promo-footer\">
        <div class=\"container-xxl\">
            <strong>Concept 2</strong> — runnable recipe surface for the documentation.
        </div>
    </footer>

    <script src=\"/js/home.js\" defer></script>
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
        return "index.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "index.twig", "/var/www/html/resources/views/index.twig");
    }
}
