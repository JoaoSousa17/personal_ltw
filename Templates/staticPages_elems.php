<!---------------------------
Usados na página de About Us
---------------------------->

<?php 
/**
 * Desenha um cartão contendo um dos valores representativos do site, com imagem ilustrativa, título e conteúdo.
 * Pode apresentar conteúdo em formato de parágrafo ou de lista, conforme o parâmetro $isList.
 *
 * @param string $imagePath Caminho para a imagem ilustrativa a ser exibida.
 * @param string $title     Título do cartão.
 * @param string $content   Texto descritivo (usado se $isList for false).
 * @param bool $isList      Define se o conteúdo será uma lista (true) ou texto (false) (por defeito: false).
 * @param array $items      Itens da lista, usados apenas se $isList for true (por defeito: []).
 */
function drawValueCard($imagePath, $title, $content, $isList = false, $items = []){ ?>
    <div class="value-card">
        <!-- Imagem -->
        <div>
            <img src="<?php echo $imagePath ?>" alt="" class = "format-icons">
        </div>

        <!-- Título -->
        <h3><?php echo $title ?></h3>

        <!-- Lista (se aplicável) -->
        <?php if ($isList){
            echo "<ul>";
            foreach ($items as $item) {
                echo "<li>$item</li>";
            }
            echo "</ul>";
        }
        else{
            echo $content;
        } ?> 
    </div>
<?php }

/**
 * Desenha o cartão de apresentação de um membro da equipa, com imagem, nome e número de estudante.
 *
 * @param string $imagePath Caminho para a foto do membro.
 * @param string $name      Nome do membro.
 * @param string $number    Número de estudante do membro.
 */
function drawTeamMember($imagePath, $name, $number){ ?>
    <div class="team-member">
        <!-- Foto -->
        <div class="member-photo">
            <img src="<?php echo $imagePath ?>" alt="Membro da equipe">
        </div>

        <!-- Nome e Número -->
        <h3><?php echo $name?></h3>
        <p class="member-role"><?php echo $number ?></p>
    </div>
<?php }

/*--------------------------
Usados na página de Services
---------------------------*/

/**
 * Desenha um cartão de uma categoria, com uma imagem, título/designação da categoria, lista de itens, parágrafo e botão de redirecionamento para página de
 * itens da presente categoria.
 * A ordem dos elementos pode ser invertida se $reverse for true (layout alternado).
 *
 * @param string $imagePath     Caminho da imagem associada à categoria.
 * @param string $title         Título/Designação da categoria.
 * @param array $li_items       Lista de exemplos de possíveis anúncios (em formato de lista).
 * @param string $paragraph     Parágrafo descritivo da categoria.
 * @param bool $reverse         Define se a imagem aparece à direita (true) ou à esquerda (false) -> Layout alternado.
 * @param string $redirectLink  Link do botão "Saiba Mais".
 */
function drawCategoryCard($imagePath, $title, $li_items, $paragraph, $reverse, $redirectLink){ ?>
    <!-- Layout Alternado -->
    <?php if ($reverse){?>
        <div class="service-item reverse">
            <!-- Imagem -->
            <div class="service-image">
                <img src="<?php echo $imagePath ?>" alt="Aplicativos Móveis">
            </div>
            <div class="service-content">
                <!-- Título -->
                <h3><?php echo $title ?></h3>

                <!-- Lista de Exemplos de possíveis anúncios -->
                <ul class="service-features">
                    <?php foreach ($li_items as $item) {
                        echo "<li>$item</li>";
                    } ?>
                </ul>

                <!-- Parágrafo Descritivo e Botão de Redirecionamento -->
                <p><?php echo $paragraph ?></p>
                <a href="<?php echo $redirectLink ?>" class="service-button">Saiba Mais</a>
            </div>
        </div>
    <?php }

    /* Layout Normal */
    else{ ?>
        <div class="service-item">
            <!-- Imagem -->
            <div class="service-image">
                <img src="<?php echo $imagePath ?>" alt="Desenvolvimento Web">
            </div>
            <div class="service-content">
                <!-- Título -->
                <h3><?php echo $title ?></h3>

                <!-- Lista de Exemplos de possíveis anúncios -->
                <ul class="service-features">
                    <?php foreach ($li_items as $item) {
                        echo "<li>$item</li>";
                    } ?>
                </ul>

                <!-- Parágrafo Descritivo e Botão de Redirecionamento -->
                <p> <?php echo $paragraph ?></p>
                <a href="<?php echo $redirectLink ?>" class="service-button">Saiba Mais</a>
            </div>
        </div>
    <?php }
}

/**
 * Desenha um cartão de destaque de uma Feature extra do site, com número (id), título/nome e descrição da feature.
 *
 * @param int|string $number    Número/id da funcionalidade.
 * @param string $header        Título da funcionalidade.
 * @param string $paragraph     Descrição da funcionalidade (ou detalhe adicional).
 */
function drawFeatureItem($number, $header, $paragraph){ ?>
    <div class="feature-item">
        <div class="feature-number"><?php echo $number ?></div>
        <h3><?php echo $header ?></h3>
        <p><?php echo $paragraph ?></p>
    </div>
<?php } ?>

<!-------------------
Usados na página FAQ
-------------------->

<?php
/**
 * Desenha um cartão de pergunta para a FAQ, com uma pergunta visível e a resposta, inicialmente oculta, 
 * podendo ser revelada interagindo com botão, usando JavaScript.
 * @param string $question Pergunta a ser exibida no topo do item.
 * @param string $answer   Resposta correspondente à pergunta.
 */
function drawFAQItem($question, $answer){ ?>
    <div class="faq-item">
        <!-- Pergunta -->
        <div class="faq-question">
            <h3><?php echo $question ?></h3>
            <span class="toggle-icon">+</span>
        </div>

        <!-- Resposta -->
        <div class="faq-answer">
            <p><?php echo $answer ?></p>
        </div>
    </div>
<?php } ?>

<!-----------------------
Usados na página Contact
------------------------>

<?php
/**
 * Desenha um cartão de contacto com ícone representativo, título e duas linhas de informação.
 * Utilizado para exibir formas de contacto como telefone, email ou morada.
 *
 * @param string $imagePath Caminho para o ícone representativo.
 * @param string $header    Título do cartão.
 * @param string $p1        Primeira linha de detalhe.
 * @param string $p2        Segunda linha de detalhe.
 */
function drawContactCard($imagePath, $header, $p1, $p2){ ?>
    <div class="contact-info-item">
        <!-- Ícone -->
        <div class="contact-icon">
            <img src="<?php echo $imagePath ?>" alt="Telefone">
        </div>

        <!-- Detalhes de Contacto (título e linhas de detalhe) -->
        <div class="contact-detail">
            <h4><?php echo $header ?></h4>
            <p><?php echo $p1 ?></p>
            <p><?php echo $p2 ?></p>
        </div>
    </div>
<?php } ?>

<?php
/**
 * Desenha um cartão de horários de funcionamento.
 *
 * @param string $title Título do cartão (ex: "Suporte Técnico").
 * @param array $schedules Lista de horários (ex: ["Segunda-feira: 9:00 - 18:00", ...]).
 */
function drawScheduleCard($title, $schedules) { ?>
    <div class="schedule-card">
        <h3><?php echo htmlspecialchars($title); ?></h3>
        <ul class="schedule-list">
            <?php foreach ($schedules as $schedule): ?>
                <?php 
                // Separar dia e horário
                $parts = explode(':', $schedule, 2);
                $day = trim($parts[0]);
                $hours = isset($parts[1]) ? trim($parts[1]) : '';
                ?>
                <li>
                    <span class="day"><?php echo htmlspecialchars($day); ?></span>
                    <span class="hours"><?php echo htmlspecialchars($hours); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="schedule-note">
            <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
            Horários sujeitos a alterações durante feriados
        </div>
    </div>
<?php }
?>
