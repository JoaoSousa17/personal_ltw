<?php 

require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/staticPages_elems.php");

drawHeader("Handee - Sobre Nós", ["/Styles/staticPages.css"]);
?>

<main>
    <!-- Banner Principal -->
    <?php drawBanner("Sobre a Handee", "Conheça a nossa história, missão e visão"); ?>

    <!-- Seção: A Nossa História -->
    <section class="main-section">
        <?php drawSectionHeader("A Nossa História", ""); ?>
        
        <?php 
        $historyContent = '
            <p>A Handee nasceu da visão de três estudantes de Engenharia Informática e Computação da Faculdade de Engenharia da Universidade do Porto que identificaram uma necessidade crescente no mercado: uma plataforma que conectasse prestadores de serviços qualificados com pessoas que precisam de soluções práticas e confiáveis para o seu dia a dia.</p>
            
            <p>Desenvolvida no âmbito da disciplina de Linguagens e Tecnologias Web, a nossa plataforma representa mais do que um simples projeto académico. É o resultado de meses de pesquisa, desenvolvimento e dedicação para criar uma solução que realmente faça a diferença na vida das pessoas, facilitando o acesso a serviços de qualidade de forma rápida e segura.</p>
            
            <p>O que começou como uma ideia simples - facilitar a contratação de serviços domésticos e profissionais - evoluiu para uma plataforma robusta que conecta utilizadores e prestadores de serviços numa comunidade digital confiável. Acreditamos que a tecnologia deve simplificar a vida das pessoas, e é exatamente isso que a Handee se propõe a fazer.</p>
            
            <p>Desde o início, a nossa abordagem tem sido centrada no utilizador. Cada funcionalidade foi pensada e desenvolvida com base nas necessidades reais das pessoas, garantindo uma experiência intuitiva e eficiente. A segurança, transparência e qualidade são os pilares fundamentais que orientam todos os nossos desenvolvimentos.</p>
            
            <p>Hoje, a Handee representa não apenas uma plataforma de serviços, mas sim uma ponte tecnológica que une necessidades a soluções, criando oportunidades para prestadores de serviços e facilitando a vida de quem precisa de ajuda. O nosso compromisso é continuar a evoluir e a inovar, sempre com foco na excelência e na satisfação dos nossos utilizadores.</p>
        ';
        
        drawMainContentSection("/Images/site/staticPages/LTW.png", "Logo LTW", $historyContent);
        ?>
    </section>

    <!-- Seção: Missão, Visão e Valores -->
    <section class="values-section">
        <?php drawSectionHeader("Missão, Visão e Valores", ""); ?>
        
        <div class="values-container">
            <?php 
            drawValueCard(
                "/Images/site/staticPages/target-icon.png", 
                "Missão", 
                "Conectar pessoas que precisam de serviços com profissionais qualificados, criando uma plataforma digital segura, eficiente e confiável que facilite o acesso a soluções práticas para o dia a dia, promovendo oportunidades de negócio e melhorando a qualidade de vida dos nossos utilizadores através da tecnologia.", 
                false, 
                []
            );
            
            drawValueCard(
                "/Images/site/staticPages/eye-icon.png", 
                "Visão", 
                "Ser a principal plataforma de referência em Portugal para a contratação de serviços domésticos e profissionais, reconhecida pela sua inovação tecnológica, segurança e pela qualidade das conexões que estabelece entre utilizadores e prestadores de serviços, contribuindo para uma sociedade mais colaborativa e eficiente.", 
                false, 
                []
            );
            
            drawValueCard(
                "/Images/site/staticPages/star-icon.png", 
                "Valores", 
                "", 
                true, 
                [
                    "Inovação constante na experiência do utilizador",
                    "Transparência em todas as transações",
                    "Segurança e confiança como prioridades",
                    "Qualidade nos serviços e na plataforma",
                    "Responsabilidade social e sustentabilidade"
                ]
            );
            ?>
        </div>
    </section>

    <!-- Seção: A Nossa Equipa -->
    <section>
        <?php drawSectionHeader("A Nossa Equipa", "Estudantes envolvidos no projeto"); ?>
        
        <div class="team-container">
            <?php 
            drawTeamMember("/Images/site/staticPages/avatar2.jpg", "João Sousa", "up202207285");
            drawTeamMember("/Images/site/staticPages/avatar3.png", "Gonçalo Martins", "up202306967");
            ?>
        </div>
    </section>
</main>

<script src="/Scripts/staticPages.js"></script>
<?php drawFooter(); ?>