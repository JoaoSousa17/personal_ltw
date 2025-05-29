<?php 
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/staticPages_elems.php");
drawHeader("Handee - Perguntas Frequentes",["/Styles/staticPages.css"])?>
<div id="common-placeholder"></div>
    <main>
        <!-- Banner Principal -->
        <?php drawBanner("Perguntas Frequentes", "Encontre respostas para as dúvidas mais comuns")?>

        <!-- Introdução FAQ -->
        <section>
            <?php drawSectionHeader("Como Podemos Ajudar?", "Abaixo estão as respostas para as perguntas mais frequentes sobre os nossos serviços") ?>
            
            <div class="faq-container">
                <!-- Categoria: Geral -->
                <div class="faq-category">
                    <h3 class="category-title">Informações Gerais</h3>
                    
                    <?php 
                    drawFAQItem("O que é a Handee?", "A Handee é uma empresa especializada em desenvolvimento de soluções tecnológicas para empresas de todos os tamanhos. Oferecemos serviços em desenvolvimento web, aplicativos móveis, soluções em cloud e análise de dados, ajudando nossos clientes a alcançarem seus objetivos de negócio através da tecnologia.");
                    
                    drawFAQItem("Onde está localizada a Handee?", "Nossa sede está localizada em Porto, Portugal, mas atendemos clientes em todo o país e internacionalmente. Trabalhamos com um modelo flexível que permite atender clientes remotamente, com a mesma qualidade e eficiência.");
                    
                    drawFAQItem("Como posso entrar em contato com a equipe da Handee?", "Você pode entrar em contato conosco através do e-mail geral@handee.pt, pelo telefone +351 123 456 789 ou através do formulário de contato em nosso site. Nossa equipe está disponível para atendê-lo de segunda a sexta, das 9h às 18h.");
                    ?>
                </div>
                
                <!-- Categoria: Serviços -->
                <div class="faq-category">
                    <h3 class="category-title">Serviços</h3>
                    
                    <?php
                    drawFAQItem("Quais serviços a Handee oferece?", "A Handee oferece uma ampla gama de serviços, incluindo:
                    <ul>
                        <li>Desenvolvimento de sites e aplicações web</li>
                        <li>Criação de aplicativos móveis para iOS e Android</li>
                        <li>Soluções em cloud e infraestrutura</li>
                        <li>Análise de dados e business intelligence</li>
                        <li>Consultoria em tecnologia</li>
                    </ul>
                    <p>Para mais detalhes, visite nossa <a href=\"/Views/staticPages/services.php\">página de serviços</a>.</p>");
                    
                    drawFAQItem("Como funciona o processo de desenvolvimento de um projeto?", "Nosso processo de desenvolvimento segue uma metodologia estruturada em 6 etapas:
                    <ol>
                        <li><strong>Análise e Planejamento:</strong> Entendemos suas necessidades e objetivos</li>
                        <li><strong>Design e Prototipagem:</strong> Criamos protótipos para visualização</li>
                        <li><strong>Desenvolvimento:</strong> Implementamos a solução com as melhores tecnologias</li>
                        <li><strong>Testes:</strong> Garantimos a qualidade com testes rigorosos</li>
                        <li><strong>Implementação:</strong> Lançamos sua solução com suporte completo</li>
                        <li><strong>Suporte:</strong> Oferecemos manutenção contínua</li>
                    </ol>");
                    
                    drawFAQItem("Quanto tempo leva para desenvolver um projeto?", "O tempo de desenvolvimento varia de acordo com a complexidade e escopo de cada projeto. Um website simples pode levar de 2 a 4 semanas, enquanto aplicações mais complexas podem levar de 3 a 6 meses. Durante nossa consulta inicial, forneceremos uma estimativa de tempo mais precisa para seu projeto específico.");
                    ?>
                </div>
                
                <!-- Categoria: Orçamentos -->
                <div class="faq-category">
                    <h3 class="category-title">Orçamentos e Pagamentos</h3>
                    
                    <?php
                    drawFAQItem("Como solicitar um orçamento?", "Você pode solicitar um orçamento através do nosso formulário de contato, por e-mail ou telefone. Após receber sua solicitação, agendaremos uma reunião para entender melhor suas necessidades e, em seguida, enviaremos uma proposta detalhada com valores e prazos.");
                    
                    drawFAQItem("Quais são as formas de pagamento aceitas?", "Aceitamos transferência bancária, cartão de crédito e débito. Para projetos maiores, oferecemos a possibilidade de parcelamento em etapas do projeto. As condições de pagamento serão detalhadas na proposta comercial.");
                    
                    drawFAQItem("Oferecem suporte após a conclusão do projeto?", "Sim, oferecemos suporte contínuo após a entrega do projeto. Temos diferentes planos de manutenção e suporte que podem ser contratados de acordo com suas necessidades. Todos os projetos incluem um período de garantia de 30 dias para correção de eventuais bugs ou problemas.");
                    ?>
                </div>
                
                <!-- Categoria: Tecnologia -->
                <div class="faq-category">
                    <h3 class="category-title">Tecnologia</h3>
                    
                    <?php
                    drawFAQItem("Quais tecnologias a Handee utiliza?", "Trabalhamos com diversas tecnologias modernas e robustas, incluindo:
                    <ul>
                        <li>Frontend: React, Angular, Vue.js, HTML5, CSS3, JavaScript</li>
                        <li>Backend: PHP, Node.js, Python, Java, .NET</li>
                        <li>Mobile: React Native, Flutter, Swift, Kotlin</li>
                        <li>Banco de dados: MySQL, PostgreSQL, MongoDB, SQL Server</li>
                        <li>Cloud: AWS, Google Cloud, Microsoft Azure</li>
                    </ul>
                    <p>Selecionamos as tecnologias mais adequadas para cada projeto, considerando suas necessidades específicas e objetivos de longo prazo.</p>");
                    
                    drawFAQItem("O site ou aplicativo será responsivo?", "Sim, todos os nossos projetos web são desenvolvidos com design responsivo, garantindo uma experiência otimizada em todos os dispositivos (desktops, tablets e smartphones). Consideramos a adaptação para diferentes tamanhos de tela desde o início do projeto.");
                    
                    drawFAQItem("Vocês desenvolvem soluções personalizadas ou usam templates?", "Desenvolvemos soluções totalmente personalizadas de acordo com as necessidades específicas de cada cliente. Embora possamos utilizar frameworks e bibliotecas para acelerar o desenvolvimento, não utilizamos templates pré-prontos. Isso garante que sua solução seja única, otimizada e alinhada perfeitamente com seus objetivos de negócio.");
                    ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Script para funcionalidade de acordeão do FAQ -->
    <script src='/Scripts/faqToggle.js'></script>
<?php drawFooter() ?>
