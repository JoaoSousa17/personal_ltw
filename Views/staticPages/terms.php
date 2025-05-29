<?php 

require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/staticPages_elems.php");

drawHeader("Handee - Termos e Condições", ["/Styles/staticPages.css"]);
?>

<main>
    <!-- Banner Principal -->
    <?php drawBanner("Termos e Condições", "Informações sobre o uso dos nossos serviços"); ?>

    <!-- Seção: Termos de Utilização -->
    <section class="about-section legal-section">
        <?php drawSectionHeader("Termos de Utilização", "Última atualização: 14 de Abril de 2025"); ?>
        
        <div class="legal-content">
            <!-- Introdução -->
            <div class="legal-intro">
                <p>Bem-vindo aos Termos e Condições da Handee. Este documento é um contrato legal entre você e a Handee que rege o uso dos nossos serviços. Ao utilizar nossos serviços, você concorda com estes termos. Por favor, leia-os cuidadosamente.</p>
            </div>
            
            <!-- Índice -->
            <?php 
            $termsSections = [
                ['id' => 'section1', 'title' => '1. Definições'],
                ['id' => 'section2', 'title' => '2. Aceitação dos Termos'],
                ['id' => 'section3', 'title' => '3. Descrição dos Serviços'],
                ['id' => 'section4', 'title' => '4. Registro e Contas'],
                ['id' => 'section5', 'title' => '5. Responsabilidades do Utilizador'],
                ['id' => 'section6', 'title' => '6. Direitos de Propriedade Intelectual'],
                ['id' => 'section7', 'title' => '7. Limitação de Responsabilidade'],
                ['id' => 'section8', 'title' => '8. Pagamentos e Faturação'],
                ['id' => 'section9', 'title' => '9. Cancelamento e Reembolso'],
                ['id' => 'section10', 'title' => '10. Alterações aos Termos'],
                ['id' => 'section11', 'title' => '11. Lei Aplicável'],
                ['id' => 'section12', 'title' => '12. Contacto']
            ];
            
            drawLegalToc($termsSections);
            ?>
            
            <!-- Seção 1: Definições -->
            <?php 
            $section1Content = '
                <p>Para efeitos destes Termos e Condições, aplicam-se as seguintes definições:</p>
                <ul>
                    <li><strong>"Handee"</strong>, "nós", "nosso" refere-se à empresa Handee, com sede em Rua Dr. Roberto Frias, s/n, 4200-465 Porto, Portugal.</li>
                    <li><strong>"Serviços"</strong> refere-se a todos os produtos, serviços, conteúdos, funcionalidades, tecnologias ou funções oferecidas pela Handee.</li>
                    <li><strong>"Utilizador"</strong>, "você", "seu" refere-se a qualquer pessoa que aceda ou utilize os nossos Serviços.</li>
                    <li><strong>"Website"</strong> refere-se ao site da Handee, acessível através do endereço www.handee.pt.</li>
                    <li><strong>"Conteúdo"</strong> refere-se a qualquer informação, dados, texto, fotografias, vídeos, software, scripts, gráficos e recursos interativos gerados, fornecidos ou disponibilizados pela Handee ou pelos seus utilizadores.</li>
                </ul>
            ';
            drawLegalSection('section1', '1. Definições', $section1Content);
            ?>
            
            <!-- Seção 2: Aceitação dos Termos -->
            <?php 
            $section2Content = '
                <p>Ao aceder ou utilizar os nossos Serviços, você declara que leu, compreendeu e concorda com estes Termos e Condições. Se não concordar com qualquer parte destes termos, não deverá utilizar os nossos Serviços.</p>
                <p>Estes Termos constituem um acordo juridicamente vinculativo entre você e a Handee. É da sua responsabilidade verificar periodicamente se houve alterações a estes Termos.</p>
            ';
            drawLegalSection('section2', '2. Aceitação dos Termos', $section2Content);
            ?>
            
            <!-- Seção 3: Descrição dos Serviços -->
            <?php 
            $section3Content = '
                <p>A Handee fornece serviços de tecnologia, incluindo mas não limitado a:</p>
                <ul>
                    <li>Desenvolvimento de websites e aplicações web</li>
                    <li>Desenvolvimento de aplicações móveis</li>
                    <li>Soluções em cloud</li>
                    <li>Análise de dados</li>
                    <li>Consultoria em tecnologia</li>
                </ul>
                <p>A Handee reserva-se o direito de modificar, suspender ou descontinuar qualquer aspecto dos Serviços a qualquer momento, incluindo a disponibilidade de qualquer funcionalidade, base de dados ou conteúdo. A Handee também pode impor limites a certas funcionalidades ou restringir o acesso a partes ou à totalidade dos Serviços sem aviso prévio ou responsabilidade.</p>
            ';
            drawLegalSection('section3', '3. Descrição dos Serviços', $section3Content);
            ?>
            
            <!-- Seção 4: Registro e Contas -->
            <?php 
            $section4Content = '
                <p>Para utilizar certos aspectos dos nossos Serviços, pode ser necessário criar uma conta. Ao criar uma conta, você concorda em:</p>
                <ul>
                    <li>Fornecer informações precisas, atuais e completas</li>
                    <li>Manter e atualizar prontamente suas informações</li>
                    <li>Manter a segurança e confidencialidade da sua senha e de qualquer identificação fornecida para acesso ao serviço</li>
                    <li>Notificar imediatamente a Handee sobre qualquer violação de segurança ou uso não autorizado da sua conta</li>
                </ul>
                <p>Você é totalmente responsável por todas as atividades que ocorram sob sua conta. A Handee não será responsável por qualquer perda ou dano resultante do seu não cumprimento desta obrigação de segurança.</p>
            ';
            drawLegalSection('section4', '4. Registro e Contas', $section4Content);
            ?>
            
            <!-- Seção 5: Responsabilidades do Utilizador -->
            <?php 
            $section5Content = '
                <p>Ao utilizar os nossos Serviços, você concorda em não:</p>
                <ul>
                    <li>Violar quaisquer leis, direitos de terceiros ou nossas políticas</li>
                    <li>Usar nossos Serviços para fins ilegais, fraudulentos ou não autorizados</li>
                    <li>Interferir ou interromper a integridade ou o desempenho dos Serviços</li>
                    <li>Tentar obter acesso não autorizado aos nossos Serviços ou sistemas</li>
                    <li>Fazer engenharia reversa, descompilar ou desmontar qualquer parte dos Serviços</li>
                    <li>Utilizar robôs, spiders ou outros meios automáticos para acessar os Serviços</li>
                    <li>Publicar ou transmitir conteúdo que seja ilegal, ofensivo, difamatório, pornográfico ou que viole direitos de terceiros</li>
                </ul>
            ';
            drawLegalSection('section5', '5. Responsabilidades do Utilizador', $section5Content);
            ?>
            
            <!-- Seção 6: Direitos de Propriedade Intelectual -->
            <?php 
            $section6Content = '
                <p>Os Serviços e todo o conteúdo associado são propriedade da Handee ou de seus licenciadores e são protegidos por leis de direitos autorais, marcas registradas e outras leis de propriedade intelectual de Portugal e outros países.</p>
                <p>Mediante o pagamento integral dos serviços contratados, os clientes receberão uma licença para usar o trabalho entregue conforme especificado no contrato. A Handee manterá os direitos de propriedade intelectual subjacentes, a menos que seja especificamente acordado em contrário por escrito.</p>
                <p>Qualquer feedback, comentários ou sugestões fornecidos à Handee sobre os Serviços é inteiramente voluntário e a Handee será livre para usar tais comentários, sugestões ou ideias como bem entender, sem qualquer obrigação para com você.</p>
            ';
            drawLegalSection('section6', '6. Direitos de Propriedade Intelectual', $section6Content);
            ?>
            
            <!-- Seção 7: Limitação de Responsabilidade -->
            <?php 
            $section7Content = '
                <p>Na máxima extensão permitida por lei, a Handee não será responsável por quaisquer danos indiretos, incidentais, especiais, consequenciais ou punitivos, ou qualquer perda de lucros ou receitas, seja incorrida direta ou indiretamente, ou qualquer perda de dados, uso, boa vontade, ou outras perdas intangíveis, resultantes de:</p>
                <ul>
                    <li>Seu acesso ou uso ou incapacidade de acessar ou usar os Serviços</li>
                    <li>Qualquer conduta ou conteúdo de terceiros nos Serviços</li>
                    <li>Conteúdo obtido a partir dos Serviços</li>
                    <li>Acesso não autorizado, uso ou alteração de suas transmissões ou conteúdo</li>
                </ul>
                <p>A responsabilidade da Handee nunca excederá o valor pago pelo cliente pelos serviços em questão nos 12 meses anteriores ao evento que deu origem à responsabilidade.</p>
            ';
            drawLegalSection('section7', '7. Limitação de Responsabilidade', $section7Content);
            ?>
            
            <!-- Seção 8: Pagamentos e Faturação -->
            <?php 
            $section8Content = '
                <p>Os preços dos Serviços serão os indicados no site ou conforme acordado no momento da contratação dos serviços. A Handee reserva-se o direito de alterar os preços a qualquer momento, mas as alterações não afetarão os contratos já em vigor.</p>
                <p>As condições de pagamento serão especificadas em cada contrato individual. Geralmente, exigimos um pagamento inicial antes do início do trabalho, seguido de pagamentos adicionais conforme o projeto avança.</p>
                <p>Em caso de não pagamento, a Handee reserva-se o direito de suspender ou terminar os serviços até que o pagamento seja recebido.</p>
            ';
            drawLegalSection('section8', '8. Pagamentos e Faturação', $section8Content);
            ?>
            
            <!-- Seção 9: Cancelamento e Reembolso -->
            <?php 
            $section9Content = '
                <p>As políticas de cancelamento e reembolso dependerão do tipo de serviço contratado e serão especificadas no contrato de serviço.</p>
                <p>Para serviços contínuos, como manutenção, pode ser necessário um aviso prévio para cancelamento, conforme especificado no contrato.</p>
                <p>Para projetos de desenvolvimento, os pagamentos feitos por trabalho já realizado não são geralmente reembolsáveis. Se um projeto for cancelado antes da conclusão, serão cobrados os serviços já prestados e o trabalho já realizado.</p>
            ';
            drawLegalSection('section9', '9. Cancelamento e Reembolso', $section9Content);
            ?>
            
            <!-- Seção 10: Alterações aos Termos -->
            <?php 
            $section10Content = '
                <p>A Handee reserva-se o direito de modificar estes Termos a qualquer momento. Publicaremos as alterações no nosso website e atualizaremos a data de "Última atualização" no topo destes Termos.</p>
                <p>É sua responsabilidade verificar periodicamente se houve alterações. O uso contínuo dos Serviços após a publicação de quaisquer alterações a estes Termos constitui a aceitação dessas alterações.</p>
            ';
            drawLegalSection('section10', '10. Alterações aos Termos', $section10Content);
            ?>
            
            <!-- Seção 11: Lei Aplicável -->
            <?php 
            $section11Content = '
                <p>Estes Termos serão regidos e interpretados de acordo com as leis de Portugal, sem consideração aos princípios de conflitos de leis.</p>
                <p>Qualquer disputa decorrente ou relacionada com estes Termos ou os Serviços será submetida à jurisdição exclusiva dos tribunais da comarca do Porto, Portugal.</p>
            ';
            drawLegalSection('section11', '11. Lei Aplicável', $section11Content);
            ?>
            
            <!-- Seção 12: Contacto -->
            <?php 
            $section12Content = '
                <p>Se tiver alguma dúvida sobre estes Termos, por favor contacte-nos:</p>
                <ul class="contact-list">
                    <li>E-mail: <a href="mailto:legal@handee.pt">legal@handee.pt</a></li>
                    <li>Telefone: +351 123 456 789</li>
                    <li>Endereço: Rua Dr. Roberto Frias, s/n, 4200-465 Porto, Portugal</li>
                </ul>
            ';
            drawLegalSection('section12', '12. Contacto', $section12Content);
            ?>
        </div>
    </section>
</main>

<script src="/Scripts/staticPages.js"></script>
<?php drawFooter(); ?>