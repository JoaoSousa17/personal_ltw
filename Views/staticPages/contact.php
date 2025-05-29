<?php 

require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/staticPages_elems.php");

// Iniciar sessão para mensagens de feedback
session_start();

// Obter mensagens de feedback e dados do formulário
$contactError = $_SESSION['contact_error'] ?? '';
$contactSuccess = $_SESSION['contact_success'] ?? '';
$formData = $_SESSION['contact_form_data'] ?? [];

// Limpar mensagens da sessão
unset($_SESSION['contact_error'], $_SESSION['contact_success'], $_SESSION['contact_form_data']);

drawHeader("Handee - Contacto", ["/Styles/staticPages.css"]);
?>

<main>
    <!-- Banner Principal -->
    <?php drawBanner("Entre em Contacto", "Estamos prontos para atendê-lo"); ?>

    <!-- Seção: Informações de Contacto -->
    <section class="main-section">
        <?php drawSectionHeader("Fale Connosco", "Escolha a melhor forma de nos contactar"); ?>
        
        <?php 
        $contactContent = '
            <p>Na Handee, valorizamos a comunicação direta com os nossos utilizadores. Estamos sempre disponíveis para esclarecer dúvidas, receber sugestões ou resolver qualquer questão relacionada com a nossa plataforma. A sua opinião é fundamental para continuarmos a melhorar os nossos serviços.</p>
            
            <p>Utilize o formulário abaixo para nos enviar a sua mensagem, ou contacte-nos através dos meios disponibilizados. Garantimos uma resposta rápida e personalizada a todas as suas questões.</p>
        ';
        
        drawMainContentSection("/Images/site/staticPages/contact.png", "Contacto", $contactContent);
        ?>
    </section>

    <!-- Seção: Formulário de Contacto -->
    <section class="contact-form-section">
        <?php drawSectionHeader("Envie-nos uma Mensagem", "Preencha o formulário abaixo"); ?>
        
        <!-- Alertas de Feedback -->
        <?php if ($contactError): ?>
            <?php drawAlert($contactError, 'error'); ?>
        <?php endif; ?>
        
        <?php if ($contactSuccess): ?>
            <?php drawAlert($contactSuccess, 'success'); ?>
        <?php endif; ?>

        <!-- Formulário de Contacto -->
        <form class="contact-form" action="/Controllers/contactController.php" method="POST">
            <?php 
            drawFormField(
                'text', 'name', 'name', 'Nome Completo', 
                true, $formData['name'] ?? '', ''
            );
            
            drawFormField(
                'email', 'email', 'email', 'Email', 
                true, $formData['email'] ?? '', ''
            );
            
            drawFormField(
                'tel', 'phone', 'phone', 'Telefone', 
                false, $formData['phone'] ?? '', 'Opcional'
            );
            
            $subjectOptions = [
                'Suporte Técnico' => 'Suporte Técnico',
                'Dúvidas sobre Serviços' => 'Dúvidas sobre Serviços',
                'Problemas de Pagamento' => 'Problemas de Pagamento',
                'Sugestões' => 'Sugestões',
                'Parcerias' => 'Parcerias',
                'Reclamações' => 'Reclamações',
                'Outro' => 'Outro'
            ];
            
            drawFormField(
                'select', 'subject', 'subject', 'Assunto', 
                true, $formData['subject'] ?? '', 'Selecione o assunto', $subjectOptions
            );
            
            drawFormField(
                'textarea', 'message', 'message', 'Mensagem', 
                true, $formData['message'] ?? '', 'Descreva a sua questão ou sugestão em detalhe...'
            );
            ?>
            
            <div class="form-group">
                <button type="submit" class="submit-button">Enviar Mensagem</button>
            </div>
        </form>
    </section>

    <!-- Seção: Informações de Contacto Direto -->
    <section class="contact-info">
        <?php drawSectionHeader("Outras Formas de Contacto", "Informações adicionais"); ?>
        
        <div class="contact-methods contact-methods-custom">
            <?php 
            drawContactCard(
                "/Images/site/footer/mail.png", 
                "Email", 
                "suporte@handee.pt", 
                "Resposta em até 24 horas"
            );
            
            drawContactCard(
                "/Images/site/footer/phone.png", 
                "Telefone", 
                "+351 220 000 000", 
                "Segunda a Sexta: 9h - 18h"
            );
            ?>
            
            <div class="contact-item contact-item-wide">
                <img src="/Images/site/footer/home.png" alt="Localização">
                <div class="contact-detail">
                    <h4>Morada</h4>
                    <p>Rua Dr. Roberto Frias</p>
                    <p>4200-465 Porto, Portugal</p>
                    <p style="margin-top: 10px; font-size: 0.9rem; color: #666;">
                        <i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i>
                        Faculdade de Engenharia da Universidade do Porto
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção: Horários de Funcionamento -->
    <section class="schedule-section">
        <?php drawSectionHeader("Horários de Funcionamento", ""); ?>
        
        <div class="schedule-container">
            <?php 
            drawScheduleCard("Suporte Técnico", [
                "Segunda-feira: 9:00 - 18:00",
                "Terça-feira: 9:00 - 18:00", 
                "Quarta-feira: 9:00 - 18:00",
                "Quinta-feira: 9:00 - 18:00",
                "Sexta-feira: 9:00 - 18:00",
                "Sábado: 10:00 - 14:00",
                "Domingo: Encerrado"
            ]);
            ?>
        </div>
    </section>
</main>

<script src="/Scripts/staticPages.js"></script>
<?php drawFooter(); ?>
