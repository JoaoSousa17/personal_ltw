<?php 

require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/staticPages_elems.php");

drawHeader("Handee - Política de Privacidade", ["/Styles/staticPages.css"]);
?>

<main>
    <!-- Banner Principal -->
    <?php drawBanner("Política de Privacidade", "Como protegemos e tratamos os seus dados"); ?>

    <!-- Seção: Política de Privacidade -->
    <section class="about-section legal-section">
        <?php drawSectionHeader("Política de Privacidade e Proteção de Dados", "Última atualização: 14 de Abril de 2025"); ?>
        
        <div class="legal-content">
            <!-- Introdução -->
            <div class="legal-intro">
                <p>A Handee está comprometida em proteger a sua privacidade. Esta Política de Privacidade explica como recolhemos, utilizamos, partilhamos e protegemos as suas informações pessoais quando utiliza os nossos serviços ou visita o nosso website. Ao utilizar os nossos serviços, concorda com as práticas descritas nesta política.</p>
            </div>
            
            <!-- Índice -->
            <?php 
            $privacySections = [
                ['id' => 'privacy-section1', 'title' => '1. Dados que Recolhemos'],
                ['id' => 'privacy-section2', 'title' => '2. Como Utilizamos os Seus Dados'],
                ['id' => 'privacy-section3', 'title' => '3. Base Legal para o Tratamento'],
                ['id' => 'privacy-section4', 'title' => '4. Partilha de Dados'],
                ['id' => 'privacy-section5', 'title' => '5. Segurança dos Dados'],
                ['id' => 'privacy-section6', 'title' => '6. Período de Conservação'],
                ['id' => 'privacy-section7', 'title' => '7. Os Seus Direitos'],
                ['id' => 'privacy-section8', 'title' => '8. Cookies e Tecnologias Semelhantes'],
                ['id' => 'privacy-section9', 'title' => '9. Transferências Internacionais'],
                ['id' => 'privacy-section10', 'title' => '10. Alterações à Política de Privacidade'],
                ['id' => 'privacy-section11', 'title' => '11. Contacto e Reclamações']
            ];
            
            drawLegalToc($privacySections);
            ?>
            
            <!-- Seção 1: Dados que Recolhemos -->
            <?php 
            $section1Content = '
                <p>Podemos recolher as seguintes categorias de informações pessoais:</p>
                <ul>
                    <li><strong>Informações de identificação:</strong> nome, endereço de email, número de telefone, endereço postal.</li>
                    <li><strong>Informações da empresa:</strong> nome da empresa, cargo, setor de atividade.</li>
                    <li><strong>Informações de faturação:</strong> dados bancários, informações de pagamento, histórico de transações.</li>
                    <li><strong>Informações de utilização:</strong> como interage com nossos serviços, incluindo o tempo de acesso, as páginas visitadas, os recursos utilizados.</li>
                    <li><strong>Informações técnicas:</strong> endereço IP, tipo de dispositivo, sistema operacional, tipo de navegador, configurações de idioma.</li>
                    <li><strong>Comunicações:</strong> mensagens que troca connosco, incluindo emails, chamadas telefónicas, comentários e feedbacks.</li>
                </ul>
                <p>Recolhemos estas informações quando:</p>
                <ul>
                    <li>Regista-se nos nossos serviços ou cria uma conta</li>
                    <li>Preenche formulários no nosso website</li>
                    <li>Comunica connosco por email, telefone ou outros meios</li>
                    <li>Subscreve a nossa newsletter</li>
                    <li>Utiliza o nosso website ou serviços</li>
                </ul>
            ';
            drawLegalSection('privacy-section1', '1. Dados que Recolhemos', $section1Content);
            ?>
            
            <!-- Seção 2: Como Utilizamos os Seus Dados -->
            <?php 
            $section2Content = '
                <p>Utilizamos as suas informações pessoais para os seguintes fins:</p>
                <ul>
                    <li><strong>Fornecer os nossos serviços:</strong> configurar e gerir a sua conta, processar pagamentos, fornecer suporte ao cliente.</li>
                    <li><strong>Melhorar os nossos serviços:</strong> analisar como utiliza os nossos serviços para desenvolver novas funcionalidades e melhorar a experiência do utilizador.</li>
                    <li><strong>Comunicar consigo:</strong> responder às suas perguntas, enviar atualizações sobre os serviços, fornecer informações técnicas e de conta.</li>
                    <li><strong>Marketing:</strong> enviar informações sobre novos serviços, promoções e eventos relevantes (se tiver consentido).</li>
                    <li><strong>Segurança:</strong> proteger os nossos serviços, prevenir fraudes e abusos.</li>
                    <li><strong>Conformidade legal:</strong> cumprir as obrigações legais e regulamentares.</li>
                </ul>
            ';
            drawLegalSection('privacy-section2', '2. Como Utilizamos os Seus Dados', $section2Content);
            ?>
            
            <!-- Seção 3: Base Legal para o Tratamento -->
            <?php 
            $section3Content = '
                <p>Tratamos os seus dados pessoais com base nas seguintes bases legais:</p>
                <ul>
                    <li><strong>Execução de contrato:</strong> quando o tratamento é necessário para a execução de um contrato com você ou para tomar medidas a seu pedido antes de celebrar um contrato.</li>
                    <li><strong>Consentimento:</strong> quando nos deu o seu consentimento explícito para processar os seus dados para um fim específico, como receber comunicações de marketing.</li>
                    <li><strong>Interesses legítimos:</strong> quando o processamento é necessário para os nossos interesses legítimos ou de terceiros, desde que os seus direitos e liberdades fundamentais não prevaleçam sobre esses interesses.</li>
                    <li><strong>Obrigação legal:</strong> quando o processamento é necessário para cumprir uma obrigação legal a que estamos sujeitos.</li>
                </ul>
            ';
            drawLegalSection('privacy-section3', '3. Base Legal para o Tratamento', $section3Content);
            ?>
            
            <!-- Seção 4: Partilha de Dados -->
            <?php 
            $section4Content = '
                <p>Podemos partilhar as suas informações pessoais com os seguintes tipos de terceiros:</p>
                <ul>
                    <li><strong>Fornecedores de serviços:</strong> empresas que nos ajudam a fornecer os nossos serviços, como processadores de pagamento, serviços de hospedagem e suporte técnico.</li>
                    <li><strong>Parceiros de negócios:</strong> empresas com as quais colaboramos para fornecer serviços ou promoções conjuntas.</li>
                    <li><strong>Autoridades legais:</strong> quando exigido por lei, ordem judicial ou processo legal.</li>
                </ul>
                <p>Não vendemos nem alugamos as suas informações pessoais a terceiros para fins de marketing.</p>
                <p>Tomamos medidas para garantir que os terceiros que têm acesso aos seus dados se comprometem a proteger a sua privacidade e segurança.</p>
            ';
            drawLegalSection('privacy-section4', '4. Partilha de Dados', $section4Content);
            ?>
            
            <!-- Seção 5: Segurança dos Dados -->
            <?php 
            $section5Content = '
                <p>Implementamos medidas técnicas e organizacionais apropriadas para proteger as suas informações pessoais contra perda acidental, acesso não autorizado, uso, alteração e divulgação. Estas medidas incluem:</p>
                <ul>
                    <li>Encriptação de dados sensíveis</li>
                    <li>Firewalls e sistemas de deteção de intrusões</li>
                    <li>Controlos de acesso rigorosos</li>
                    <li>Monitorização regular dos sistemas</li>
                    <li>Formação de segurança para os funcionários</li>
                </ul>
                <p>Embora nos esforcemos por proteger as suas informações pessoais, nenhum método de transmissão pela Internet ou método de armazenamento eletrónico é 100% seguro. Portanto, não podemos garantir a sua segurança absoluta.</p>
            ';
            drawLegalSection('privacy-section5', '5. Segurança dos Dados', $section5Content);
            ?>
            
            <!-- Seção 6: Período de Conservação -->
            <?php 
            $section6Content = '
                <p>Conservamos as suas informações pessoais apenas pelo tempo necessário para cumprir os fins para os quais foram recolhidas, incluindo para satisfazer quaisquer requisitos legais, contabilísticos ou de relatórios.</p>
                <p>Os critérios utilizados para determinar o nosso período de retenção incluem:</p>
                <ul>
                    <li>O período de tempo em que temos uma relação contínua consigo (por exemplo, enquanto utiliza os nossos serviços)</li>
                    <li>Se existe uma obrigação legal a que estamos sujeitos</li>
                    <li>Se a retenção é aconselhável à luz da nossa posição legal (como em relação a estatutos de limitações aplicáveis, litígios ou investigações regulatórias)</li>
                </ul>
            ';
            drawLegalSection('privacy-section6', '6. Período de Conservação', $section6Content);
            ?>
            
            <!-- Seção 7: Os Seus Direitos -->
            <?php 
            $section7Content = '
                <p>Dependendo da sua localização, pode ter os seguintes direitos em relação aos seus dados pessoais:</p>
                <ul>
                    <li><strong>Acesso:</strong> o direito de solicitar uma cópia das suas informações pessoais que detemos.</li>
                    <li><strong>Retificação:</strong> o direito de corrigir dados inexatos ou incompletos.</li>
                    <li><strong>Apagamento:</strong> o direito de solicitar que apaguemos os seus dados pessoais em determinadas circunstâncias.</li>
                    <li><strong>Restrição:</strong> o direito de solicitar que restrinjamos o processamento dos seus dados em determinadas circunstâncias.</li>
                    <li><strong>Portabilidade:</strong> o direito de receber os seus dados pessoais num formato estruturado, de uso comum e legível por máquina.</li>
                    <li><strong>Oposição:</strong> o direito de se opor ao processamento dos seus dados pessoais em determinadas circunstâncias.</li>
                    <li><strong>Retirar o consentimento:</strong> o direito de retirar o seu consentimento a qualquer momento quando o processamento se baseia no seu consentimento.</li>
                </ul>
                <p>Para exercer estes direitos, entre em contacto connosco através dos detalhes fornecidos na secção "Contacto e Reclamações".</p>
            ';
            drawLegalSection('privacy-section7', '7. Os Seus Direitos', $section7Content);
            ?>
            
            <!-- Seção 8: Cookies e Tecnologias Semelhantes -->
            <?php 
            $section8Content = '
                <p>Utilizamos cookies e tecnologias semelhantes (como web beacons e pixels) para melhorar a sua experiência no nosso website, analisar o uso do site e facilitar os nossos esforços de marketing.</p>
                <p>Os tipos de cookies que utilizamos incluem:</p>
                <ul>
                    <li><strong>Cookies essenciais:</strong> necessários para o funcionamento do website.</li>
                    <li><strong>Cookies de desempenho:</strong> ajudam-nos a entender como os visitantes interagem com o nosso site.</li>
                    <li><strong>Cookies de funcionalidade:</strong> permitem que o site lembre as suas escolhas (como o seu nome de utilizador ou idioma).</li>
                    <li><strong>Cookies de publicidade:</strong> utilizados para fornecer anúncios relevantes e limitar o número de vezes que vê um anúncio.</li>
                </ul>
                <p>Pode gerir as suas preferências de cookies através das configurações do seu navegador. No entanto, a desativação de certos cookies pode afetar a funcionalidade do nosso website.</p>
            ';
            drawLegalSection('privacy-section8', '8. Cookies e Tecnologias Semelhantes', $section8Content);
            ?>
            
            <!-- Seção 9: Transferências Internacionais -->
            <?php 
            $section9Content = '
                <p>As suas informações pessoais podem ser transferidas e processadas em países fora do Espaço Económico Europeu (EEE) que podem não oferecer o mesmo nível de proteção de dados que o seu país de residência.</p>
                <p>Quando transferimos os seus dados para fora do EEE, tomamos medidas para garantir que são protegidos adequadamente, incluindo:</p>
                <ul>
                    <li>Transferência para países que foram considerados como tendo proteção adequada pela Comissão Europeia</li>
                    <li>Uso de contratos aprovados pela Comissão Europeia que dão às informações pessoais a mesma proteção que têm na Europa</li>
                    <li>Transferências para organizações que subscrevem esquemas de proteção de dados reconhecidos internacionalmente, como o Privacy Shield entre a UE e os EUA</li>
                </ul>
            ';
            drawLegalSection('privacy-section9', '9. Transferências Internacionais', $section9Content);
            ?>
            
            <!-- Seção 10: Alterações à Política de Privacidade -->
            <?php 
            $section10Content = '
                <p>Podemos atualizar esta Política de Privacidade periodicamente para refletir mudanças nas nossas práticas de tratamento de dados ou alterações na legislação aplicável. Encorajamos os utilizadores a rever periodicamente esta política para se manterem informados sobre como estamos a proteger as suas informações.</p>
                <p>A data da última atualização será sempre indicada no topo desta política. As alterações entram em vigor quando publicamos a política de privacidade revista.</p>
                <p>Se fizermos alterações significativas, iremos notificá-lo através de um aviso no nosso website ou, em alguns casos, enviando-lhe um email.</p>
            ';
            drawLegalSection('privacy-section10', '10. Alterações à Política de Privacidade', $section10Content);
            ?>
            
            <!-- Seção 11: Contacto e Reclamações -->
            <?php 
            $section11Content = '
                <p>Se tiver alguma pergunta sobre esta Política de Privacidade, ou se quiser exercer os seus direitos de proteção de dados, entre em contacto connosco:</p>
                <ul class="contact-list">
                    <li>E-mail: <a href="mailto:privacidade@handee.pt">privacidade@handee.pt</a></li>
                    <li>Telefone: +351 123 456 789</li>
                    <li>Endereço: Rua Dr. Roberto Frias, s/n, 4200-465 Porto, Portugal</li>
                </ul>
                <p>Tem também o direito de apresentar uma reclamação junto da autoridade de controlo local (em Portugal, a Comissão Nacional de Proteção de Dados - CNPD) se considerar que o tratamento dos seus dados pessoais viola a legislação aplicável.</p>
            ';
            drawLegalSection('privacy-section11', '11. Contacto e Reclamações', $section11Content);
            ?>
            
            <!-- Conclusão -->
            <div class="legal-conclusion">
                <p>Ao utilizar os nossos serviços, reconhece que leu e compreendeu esta Política de Privacidade. Encorajamos o acesso regular a esta política para se manter informado sobre como protegemos os seus dados pessoais.</p>
                <p>Estamos comprometidos em proteger a sua privacidade e em garantir a segurança dos seus dados pessoais. Se tiver qualquer dúvida ou preocupação, não hesite em contactar-nos.</p>
            </div>
        </div>
    </section>
</main>

<script src="/Scripts/staticPages.js"></script>
<?php drawFooter(); ?>