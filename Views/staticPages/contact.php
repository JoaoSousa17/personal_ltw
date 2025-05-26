<?php 
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/staticPages_elems.php");

// Iniciar sessão para mensagens de feedback
session_start();

// Obter mensagens de feedback
$contactError = $_SESSION['contact_error'] ?? '';
$contactSuccess = $_SESSION['contact_success'] ?? '';
$formData = $_SESSION['contact_form_data'] ?? [];

// Limpar mensagens da sessão
unset($_SESSION['contact_error'], $_SESSION['contact_success'], $_SESSION['contact_form_data']);

drawHeader("Handee - Contacto",["/Styles/staticPages.css"])?>
<div id="common-placeholder"></div>
    <main>
        <!-- Banner Principal -->
        <?php drawBanner("Entre em Contacto", "Estamos prontos para atendê-lo")?>

        <!-- Informações de Contacto -->
        <section class="main-section">
            <?php drawSectionHeader("Fale Connosco", "Escolha a melhor forma de nos contactar") ?>
            <div class="main-content">
                <div class="main-image">
                    <img src="/Images/site/staticPages/contact.png" alt="">
                </div>
                <div class="main-text">
                    <p>Na Handee, valorizamos a comunicação direta com os nossos utilizadores. Estamos sempre disponíveis para esclarecer dúvidas, receber sugestões ou resolver qualquer questão relacionada com a nossa plataforma. A sua opinião é fundamental para continuarmos a melhorar os nossos serviços.</p>
                    
                    <p>Utilize o formulário abaixo para nos enviar a sua mensagem, ou contacte-nos através dos meios disponibilizados. Garantimos uma resposta rápida e personalizada a todas as suas questões.</p>
                </div>
            </div>
        </section>

        <!-- Formulário de Contacto -->
        <section class="contact-form-section">
            <?php drawSectionHeader("Envie-nos uma Mensagem", "Preencha o formulário abaixo") ?>
            
            <?php if ($contactError): ?>
                <div class="alert alert-error">
                    <?php echo $contactError; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($contactSuccess): ?>
                <div class="alert alert-success">
                    <?php echo $contactSuccess; ?>
                </div>
            <?php endif; ?>

            <form class="contact-form" action="/Controllers/contactController.php" method="POST">
                <div class="form-group">
                    <label for="name">Nome Completo *</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Telefone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>" placeholder="Opcional">
                </div>

                <div class="form-group">
                    <label for="subject">Assunto *</label>
                    <select id="subject" name="subject" required>
                        <option value="">Selecione o assunto</option>
                        <option value="Suporte Técnico" <?php echo (($formData['subject'] ?? '') === 'Suporte Técnico') ? 'selected' : ''; ?>>Suporte Técnico</option>
                        <option value="Dúvidas sobre Serviços" <?php echo (($formData['subject'] ?? '') === 'Dúvidas sobre Serviços') ? 'selected' : ''; ?>>Dúvidas sobre Serviços</option>
                        <option value="Problemas de Pagamento" <?php echo (($formData['subject'] ?? '') === 'Problemas de Pagamento') ? 'selected' : ''; ?>>Problemas de Pagamento</option>
                        <option value="Sugestões" <?php echo (($formData['subject'] ?? '') === 'Sugestões') ? 'selected' : ''; ?>>Sugestões</option>
                        <option value="Parcerias" <?php echo (($formData['subject'] ?? '') === 'Parcerias') ? 'selected' : ''; ?>>Parcerias</option>
                        <option value="Reclamações" <?php echo (($formData['subject'] ?? '') === 'Reclamações') ? 'selected' : ''; ?>>Reclamações</option>
                        <option value="Outro" <?php echo (($formData['subject'] ?? '') === 'Outro') ? 'selected' : ''; ?>>Outro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Mensagem *</label>
                    <textarea id="message" name="message" rows="6" placeholder="Descreva a sua questão ou sugestão em detalhe..." required><?php echo htmlspecialchars($formData['message'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="submit-button">Enviar Mensagem</button>
                </div>
            </form>
        </section>

        <!-- Informações de Contacto Direto -->
        <section class="contact-info">
            <?php drawSectionHeader("Outras Formas de Contacto", "Informações adicionais") ?>
            <div class="contact-methods">
                <div class="contact-item">
                    <img src="/Images/site/staticPages/email-icon.png" alt="Email">
                    <h3>Email</h3>
                    <p>suporte@handee.pt</p>
                    <p>Resposta em até 24 horas</p>
                </div>
                
                <div class="contact-item">
                    <img src="/Images/site/staticPages/phone-icon.png" alt="Telefone">
                    <h3>Telefone</h3>
                    <p>+351 220 000 000</p>
                    <p>Segunda a Sexta: 9h - 18h</p>
                </div>
                
                <div class="contact-item">
                    <img src="/Images/site/staticPages/location-icon.png" alt="Localização">
                    <h3>Morada</h3>
                    <p>Rua Dr. Roberto Frias</p>
                    <p>4200-465 Porto, Portugal</p>
                </div>
            </div>
        </section>

        <!-- Horários de Funcionamento -->
        <section class="schedule-section">
            <?php drawSectionHeader("Horários de Funcionamento", "") ?>
            <div class="schedule-container">
                <?php drawScheduleCard("Suporte Técnico", [
                    "Segunda-feira: 9:00 - 18:00",
                    "Terça-feira: 9:00 - 18:00", 
                    "Quarta-feira: 9:00 - 18:00",
                    "Quinta-feira: 9:00 - 18:00",
                    "Sexta-feira: 9:00 - 18:00",
                    "Sábado: 10:00 - 14:00",
                    "Domingo: Encerrado"
                ]) ?>
            </div>
        </section>

        <!-- FAQ Rápido -->
        <section class="quick-faq">
            <?php drawSectionHeader("Perguntas Frequentes", "Respostas rápidas às dúvidas mais comuns") ?>
            <div class="faq-items">
                <div class="faq-item">
                    <h3>Como posso cancelar um serviço?</h3>
                    <p>Pode cancelar um serviço através do seu perfil, na secção "Os Meus Serviços", até 24 horas antes do agendamento.</p>
                </div>
                
                <div class="faq-item">
                    <h3>Como funciona o sistema de pagamentos?</h3>
                    <p>Os pagamentos são processados de forma segura através da nossa plataforma, com confirmação por email após cada transação.</p>
                </div>
                
                <div class="faq-item">
                    <h3>Que garantias tenho sobre os prestadores?</h3>
                    <p>Todos os prestadores são verificados e avaliados pelos utilizadores. Oferecemos também um sistema de resolução de conflitos.</p>
                </div>
            </div>
        </section>
    </main>
<?php drawFooter() ?>