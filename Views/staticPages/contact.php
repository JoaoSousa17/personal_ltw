<?php 
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/staticPages_elems.php");
drawHeader("Handee - Contacto",["/Styles/staticPages.css"])?>
<div id="common-placeholder"></div>
    <main>
        <!-- Banner Principal -->
        <?php drawBanner("Entre em Contacto", "Estamos prontos para atendê-lo")?>

        <!-- Informações de Contacto -->
        <section class="main-section">
            <?php drawSectionHeader("Fale Connosco", "Escolha a melhor forma de entrar em contacto") ?>
            
            <div class="contact-container">
                <div class="contact-info">
                    <?php drawContactCard("/Images/site/staticPages/location-icon.png", "Endereço", "Rua Dr. Roberto Frias, s/n", "4200-465 Porto, Portugal") ?>
                    
                    <?php drawContactCard("/Images/site/staticPages/phone-icon.png", "Telefone", "+351 123 456 789", "Seg-Sex: 9h às 18h") ?>
                    
                    <?php drawContactCard("/Images/site/staticPages/email-icon.png", "Email", "geral@handee.pt", "Respondemos em até 24h úteis") ?>
                </div>
                
                <div class="contact-form">
                    <h3>Envie-nos uma mensagem</h3>
                    <form action="/Controllers/contactController.php" method="POST">
                        <div class="contact-form-group">
                            <label for="name">Nome Completo</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="contact-form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="contact-form-group">
                            <label for="phone">Telefone</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        
                        <div class="contact-form-group">
                            <label for="subject">Assunto</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        
                        <div class="contact-form-group">
                            <label for="message">Mensagem</label>
                            <textarea id="message" name="message" required></textarea>
                        </div>
                        
                        <div class="form-checkbox">
                            <input type="checkbox" id="privacy" name="privacy" required>
                            <label for="privacy">Concordo com a <a href="/Views/staticPages/privacy.php">Política de Privacidade</a></label>
                        </div>
                        
                        <button type="submit" class="contact-submit">Enviar Mensagem</button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Mapa -->
        <section class="main-section">
            <div class="section-header">
                <h2>Nossa Localização</h2>
                <p>Venha nos visitar</p>
            </div>
            
            <div class="map-container">
                <!-- Substitua o iframe abaixo pelo mapa do Google Maps da sua localização -->
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3004.456245733869!2d-8.598185223459374!3d41.17720887132762!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd2464437e91fbc1%3A0x85e95055cd2bef05!2sFaculdade%20de%20Engenharia%20da%20Universidade%20do%20Porto!5e0!3m2!1spt-PT!2spt!4v1714067981339!5m2!1spt-PT!2spt" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>

        <!-- Horário de Funcionamento -->
        <section class="main-section">
            <div class="section-header">
                <h2>Horário de Funcionamento</h2>
                <p>Quando estamos disponíveis para atendê-lo</p>
            </div>
            
            <div class="schedule-container">
                <div class="schedule-card">
                    <h3>Horário Comercial</h3>
                    <ul class="schedule-list">
                        <li><span class="day">Segunda-Feira:</span> <span class="hours">9h às 18h</span></li>
                        <li><span class="day">Terça-Feira:</span> <span class="hours">9h às 18h</span></li>
                        <li><span class="day">Quarta-Feira:</span> <span class="hours">9h às 18h</span></li>
                        <li><span class="day">Quinta-Feira:</span> <span class="hours">9h às 18h</span></li>
                        <li><span class="day">Sexta-Feira:</span> <span class="hours">9h às 18h</span></li>
                        <li><span class="day">Sábado:</span> <span class="hours">Fechado</span></li>
                        <li><span class="day">Domingo:</span> <span class="hours">Fechado</span></li>
                    </ul>
                    <p class="schedule-note">*Agendamentos fora do horário comercial podem ser disponibilizados mediante solicitação prévia.</p>
                </div>
                
                <div class="schedule-card">
                    <h3>Suporte Técnico</h3>
                    <ul class="schedule-list">
                        <li><span class="day">Segunda a Sexta:</span> <span class="hours">8h às 20h</span></li>
                        <li><span class="day">Sábado:</span> <span class="hours">9h às 13h</span></li>
                        <li><span class="day">Domingo:</span> <span class="hours">Fechado</span></li>
                    </ul>
                    <p class="schedule-info">Para clientes com contrato de suporte prioritário, oferecemos atendimento 24/7 através do email <strong>suporte@handee.pt</strong> ou pelo telefone de emergência fornecido no contrato.</p>
                </div>
            </div>
        </section>
    </main>
<?php drawFooter() ?>
