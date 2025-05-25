<?php 
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/staticPages_elems.php");
drawHeader("Handee - Sobre Nós",["/Styles/staticPages.css"])?>
<main>
     <!-- Banner Principal -->
    <?php drawBanner("Sobre a Handee", "Conheca a nossa história, missão e visão")?>

    <!-- A Nossa História -->
    <?php drawMainSection("A Nossa História", "", "/Images/site/staticPages/LTW.png", 5) ?>

    <!-- Missão, Visão e Valores -->
    <section class="values-section">
        <?php drawSectionHeader("Missão, Visão e Valores", "") ?>
        <div class="values-container">
            <?php drawValueCard("/Images/site/staticPages/target-icon.png", "Missão", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Qui accusamus ex molestiae at error, itaque iusto obcaecati dolores consequatur hic in maxime voluptate autem similique ad perferendis illum unde est!", false, []) ?>
            <?php drawValueCard("/Images/site/staticPages/eye-icon.png", "Visão", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Qui accusamus ex molestiae at error, itaque iusto obcaecati dolores consequatur hic in maxime voluptate autem similique ad perferendis illum unde est!", false, []) ?>
            <?php drawValueCard("/Images/site/staticPages/star-icon.png", "Valores", "", true, ["Inovação constante", "Excelência em cada detalhe", "Compromisso com resultados", "Respeito às pessoas", "Responsabilidade social"]) ?>
        </div>
    </section>

        <!-- A Nossa Equipa -->
    <section>
        <?php drawSectionHeader("A Nossa Equipa", "Estudantes envolvidos no projeto") ?>
        <div class="team-container">
            <?php drawTeamMember("/Images/site/staticPages/avatar1.png", "João Sousa", "up202207285") ?>
            <?php drawTeamMember("/Images/site/staticPages/avatar2.jpg", "António Lima", "up202003386") ?>
            <?php drawTeamMember("/Images/site/staticPages/avatar3.png", "Gonçalo Martins", "up202306967") ?>
        </div>
    </section>
</main>

<?php drawFooter() ?>

