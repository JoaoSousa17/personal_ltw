<?php
// Verificar permissões e incluir dependências
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/distancesCalculationController.php");

// Verificar permissões de administrador
requireAdminAccess();

// Obter dados estatísticos
$generalStats = getGeneralStats();
$newUsersData = getNewUsersLast7Days();
$complaintsData = getComplaintsLast7Days();
$contactsData = getContactsLast7Days();
$newsletterData = getNewsletterSubscriptionsLast7Days();
$recentActivity = getRecentActivity();
$growthStats = getGrowthStats();

drawHeader("Handee - Análise de Dados", ["/Styles/admin.css"]);
?>

<main class="analytics-container">
    <!-- Cabeçalho da página -->
    <?php drawSectionHeader("Análise de Dados", "Estatísticas e métricas da plataforma", true); ?>

    <!-- Estatísticas gerais -->
    <?php drawGeneralStatsSection($generalStats); ?>

    <!-- Crescimento mensal -->
    <?php drawGrowthSection($growthStats); ?>

    <!-- Gráficos dos últimos 7 dias -->
    <?php drawChartsSection(); ?>

    <!-- Atividade recente -->
    <?php drawRecentActivitySection($recentActivity); ?>
</main>

<!-- Chart.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<!-- Dados para os gráficos -->
<script>
window.chartData = {
    newUsersData: <?= json_encode($newUsersData) ?>,
    contactsData: <?= json_encode($contactsData) ?>,
    complaintsData: <?= json_encode($complaintsData) ?>,
    newsletterData: <?= json_encode($newsletterData) ?>
};
</script>

<!-- Script dos gráficos -->
<script src="/Scripts/analytics.js"></script>

<?php drawFooter(); ?>