<?php 
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/staticPages_elems.php");
drawHeader("Handee - Serviços",["/Styles/staticPages.css"])?>
<div id="common-placeholder"></div>
    <main>
        <!-- Banner Principal -->
        <?php drawBanner("Nossos Serviços", "Soluções completas para suas necessidades")?>

        <!-- Introdução aos Serviços -->
        <?php drawMainSection("O Que Oferecemos", "Conheça nossa ampla gama de serviços personalizados", "/Images/site/staticPages/services.png", 2) ?>

        <!-- Serviços Detalhados -->
        <section class="detailed-services">
            <?php drawSectionHeader("As Nossas Categorias em Detalhe", "Conheça melhor como o podemos ajudar no dia a dia") ?>

            <?php drawCategoryCard("/Images/site/staticPages/web-service.png", "Desenvolvimento Web", ["Sites institucionais e corporativos", "Lojas virtuais e e-commerce", "Sistemas web personalizados", "Portais e intranets", "Otimização para SEO"], "Nosso processo de desenvolvimento web é focado em entregar soluções que não apenas atendem às necessidades estéticas, mas também são funcionais, seguras e escaláveis.", 0, "redirect-link") ?>
            
            <?php drawCategoryCard("/Images/site/staticPages/mobile-service.png", "Aplicativos Móveis", ["Aplicativos nativos (iOS e Android)", "Aplicativos híbridos", "Aplicativos corporativos", "Integração com APIs e sistemas", "Manutenção e suporte contínuo"], "Desenvolvemos aplicativos móveis intuitivos e de alto desempenho que proporcionam experiências excepcionais aos usuários e ajudam a impulsionar o engajamento com sua marca.", 1, "redirect-link") ?>
            
            <?php drawCategoryCard("/Images/site/staticPages/cloud-service.png", "Soluções em Cloud", ["Migração para a nuvem", "Arquitetura de nuvem otimizada", "Implementação de DevOps", "Segurança na nuvem", "Monitoramento e gerenciamento"], "Ajudamos empresas a aproveitar todo o potencial da computação em nuvem, oferecendo soluções personalizadas que melhoram a eficiência operacional e reduzem custos.", 0, "redirect-link") ?>
            
            <?php drawCategoryCard("/Images/site/staticPages/data-service.png", "Análise de Dados", ["Aplicativos nativos (iOS e Android)", "Aplicativos híbridos", "Aplicativos corporativos", "Integração com APIs e sistemas", "Manutenção e suporte contínuo"], "Transformamos dados brutos em insights valiosos que ajudam a tomar decisões mais inteligentes e estratégicas para o crescimento do seu negócio.", 1, "redirect-link") ?>
        </section>

        <!-- Outras features do site -->
        <section class="features-section">
            <?php drawSectionHeader("Outras Features do Site", "De que outro modo podemos melhorar a sua experiência") ?>
            
            <div class="features-items">
                <?php drawFeatureItem(1, "Análise e Planejamento", "Entendemos profundamente suas necessidades e objetivos para criar um plano estratégico detalhado.") ?>
                
                <?php drawFeatureItem(2, "Design e Prototipagem", "Criamos protótipos e designs funcionais que alinham-se com sua visão e requisitos.") ?>

                <?php drawFeatureItem(3, "Desenvolvimento", "Implementamos soluções utilizando as melhores tecnologias e práticas de desenvolvimento.") ?>

                <?php drawFeatureItem(4, "Testes e Qualidade", "Realizamos testes rigorosos para garantir que tudo funcione perfeitamente antes da entrega.") ?>

                <?php drawFeatureItem(5, "Implementação", "Lançamos sua solução com suporte completo para garantir uma transição tranquila.") ?>

                <?php drawFeatureItem(6, "Suporte Contínuo", "Oferecemos manutenção e suporte contínuo para garantir o sucesso a longo prazo.") ?>
            </div>
        </section>
    </main>
<?php drawFooter() ?>