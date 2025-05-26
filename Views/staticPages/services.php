<?php 
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/staticPages_elems.php");
drawHeader("Handee - Serviços",["/Styles/staticPages.css"])?>
<div id="common-placeholder"></div>
    <main>
        <!-- Banner Principal -->
        <?php drawBanner("Nossos Serviços", "Soluções completas para suas necessidades")?>

        <!-- Introdução aos Serviços -->
        <section class="main-section">
            <?php drawSectionHeader("O Que Oferecemos", "Conheça nossa ampla gama de serviços personalizados") ?>
            <div class="main-content">
                <div class="main-image">
                    <img src="/Images/site/staticPages/services.png" alt="">
                </div>
                <div class="main-text">
                    <p>A Handee é uma plataforma inovadora que conecta utilizadores a prestadores de serviços qualificados numa ampla variedade de categorias. Desde serviços domésticos essenciais até soluções especializadas, a nossa plataforma oferece acesso fácil e seguro a profissionais verificados que podem ajudar nas mais diversas necessidades do dia a dia.</p>
                    
                    <p>Com um sistema intuitivo de pesquisa e filtragem, os utilizadores podem encontrar rapidamente o serviço ideal, comparar preços, ler avaliações e comunicar diretamente com os prestadores. A nossa missão é simplificar a contratação de serviços, garantindo transparência, segurança e qualidade em cada transação realizada através da plataforma.</p>
                </div>
            </div>
        </section>

        <!-- Serviços Detalhados -->
        <section class="detailed-services">
            <?php drawSectionHeader("As Nossas Categorias em Detalhe", "Conheça melhor como o podemos ajudar no dia a dia") ?>

            <?php drawCategoryCard("/Images/site/categories/administrativo.jpg", "Serviços Administrativos", ["Apoio na gestão de documentos e burocracias", "Organização de arquivos e correspondência", "Assistência em processos administrativos", "Suporte em declarações e formulários", "Gestão de prazos e compromissos"], "Os nossos profissionais administrativos ajudam a simplificar as tarefas burocráticas do seu dia a dia, desde a organização de documentos até ao apoio em processos mais complexos, poupando-lhe tempo e garantindo que tudo está em ordem.", false, "/Views/categories.php") ?>
            
            <?php drawCategoryCard("/Images/site/categories/animais.jpg", "Cuidados com Animais", ["Passeios personalizados para cães", "Cuidados veterinários básicos", "Pet sitting e hospedagem", "Treino e adestramento", "Grooming e higiene"], "Especializados no bem-estar dos seus companheiros de quatro patas, os nossos prestadores oferecem cuidados dedicados e carinhosos para que os seus animais de estimação recebam toda a atenção que merecem, mesmo quando não pode estar presente.", true, "/Views/categories.php") ?>
            
            <?php drawCategoryCard("/Images/site/categories/aulas.jpg", "Aulas e Formação", ["Explicações individuais e em grupo", "Aulas de música e instrumentos", "Formação em idiomas", "Apoio escolar e universitário", "Workshops e cursos especializados"], "Aprenda algo novo ou aprimore os seus conhecimentos com os nossos educadores qualificados. Oferecemos aulas personalizadas para todas as idades e níveis, desde apoio escolar até formações profissionais especializadas.", false, "/Views/categories.php") ?>
            
            <?php drawCategoryCard("/Images/site/categories/beleza.jpg", "Beleza e Bem-estar", ["Cabeleireiro e styling", "Manicure e pedicure", "Tratamentos faciais", "Massagens relaxantes", "Maquilhagem para eventos"], "Cuide de si com os nossos profissionais de beleza e bem-estar. Desde tratamentos relaxantes até preparação para eventos especiais, garantimos que se sinta bem consigo mesmo em qualquer ocasião.", true, "/Views/categories.php") ?>
            
            <?php drawCategoryCard("/Images/site/categories/culinaria.jpg", "Serviços Culinários", ["Chef pessoal para eventos", "Aulas de culinária", "Catering personalizado", "Preparação de refeições", "Consultoria nutricional"], "Descubra os sabores da gastronomia com os nossos chefs e especialistas culinários. Desde eventos especiais até aulas práticas, proporcionamos experiências gastronómicas únicas e personalizadas ao seu gosto.", false, "/Views/categories.php") ?>
            
            <?php drawCategoryCard("/Images/site/categories/design.jpg", "Design e Criatividade", ["Design gráfico e identidade visual", "Decoração de interiores", "Fotografia profissional", "Criação de websites", "Artes visuais e ilustração"], "Transforme as suas ideias em realidade com os nossos criativos especializados. Desde a conceção visual de projetos até à decoração de espaços, oferecemos soluções criativas que refletem a sua personalidade e objetivos.", true, "/Views/categories.php") ?>
            
            <?php drawCategoryCard("/Images/site/categories/jardim.jpg", "Jardinagem e Paisagismo", ["Manutenção de jardins", "Paisagismo e design de exteriores", "Plantação e cultivo", "Sistemas de irrigação", "Poda e tratamento de plantas"], "Mantenha os seus espaços verdes sempre bonitos e saudáveis com os nossos especialistas em jardinagem. Desde a criação de jardins até à manutenção regular, cuidamos da natureza que o rodeia.", false, "/Views/categories.php") ?>
            
            <?php drawCategoryCard("/Images/site/categories/limpeza.jpg", "Limpeza e Organização", ["Limpeza doméstica regular", "Limpeza pós-obra", "Organização de espaços", "Limpeza de escritórios", "Serviços de limpeza especializada"], "Mantenha os seus espaços impecáveis com os nossos profissionais de limpeza. Oferecemos serviços regulares ou pontuais, adaptados às suas necessidades específicas, garantindo sempre os mais altos padrões de higiene e organização.", true, "/Views/categories.php") ?>
            
            <?php drawCategoryCard("/Images/site/categories/reparacoes.jpg", "Reparações e Manutenção", ["Reparações elétricas e canalizações", "Carpintaria e marcenaria", "Pintura e acabamentos", "Manutenção de eletrodomésticos", "Pequenas obras e remodelações"], "Resolva os problemas da sua casa ou escritório com os nossos técnicos especializados. Desde reparações urgentes até projetos de manutenção planeada, garantimos soluções eficazes e duradouras.", false, "/Views/categories.php") ?>
            
            <?php drawCategoryCard("/Images/site/categories/transporte.jpg", "Transporte e Logística", ["Mudanças domésticas e comerciais", "Transporte de mercadorias", "Serviços de entrega", "Motorista pessoal", "Aluguer de veículos"], "Facilite as suas deslocações e transportes com os nossos serviços logísticos. Desde mudanças completas até entregas pontuais, garantimos que tudo chega ao destino de forma segura e pontual.", true, "/Views/categories.php") ?>
        </section>

        <!-- Outras features do site -->
        <section class="features-section">
            <?php drawSectionHeader("Funcionalidades da Plataforma", "Tecnologia avançada para uma experiência superior") ?>
            
            <div class="features-items">
                <?php drawFeatureItem(1, "Painel de Administração", "Interface moderna e intuitiva para gestão completa da plataforma, permitindo controlo total sobre utilizadores, serviços e transações.") ?>
                
                <?php drawFeatureItem(2, "Sistema de Mensagens", "Comunicação direta e segura entre utilizadores e prestadores de serviços, facilitando o esclarecimento de dúvidas e negociação de detalhes.") ?>

                <?php drawFeatureItem(3, "Newsletter Mensal", "Mantenha-se atualizado com as últimas novidades, promoções especiais e dicas úteis através da nossa newsletter personalizada.") ?>

                <?php drawFeatureItem(4, "Pagamento Seguro", "Sistema de pagamento protegido com encriptação avançada, garantindo transações seguras e proteção dos dados financeiros.") ?>

                <?php drawFeatureItem(5, "Perfil de Utilizador", "Perfis personalizáveis com histórico de serviços, avaliações e preferências, criando uma experiência única para cada utilizador.") ?>

                <?php drawFeatureItem(6, "Pesquisa Avançada", "Sistema inteligente de pesquisa e filtragem que permite encontrar rapidamente o serviço ideal com base em localização, preço, avaliações e disponibilidade.") ?>
            </div>
        </section>
    </main>
<?php drawFooter() ?>