<?php
require_once(dirname(__FILE__)."/../../Utils/session.php");
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/distancesCalculationController.php");

// Verificar permissões de administrador
requireAdminAccess();

// Obter todos os dados necessários
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
    <?php drawSectionHeader("Análise de Dados", "Estatísticas e métricas da plataforma", true); ?>

    <!-- Estatísticas Gerais -->
    <section class="stats-overview">
        <h3 class="section-subtitle">Visão Geral</h3>
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <img src="/Images/site/admin/users.png" alt="Utilizadores">
                </div>
                <div class="stat-content">
                    <h4>Utilizadores</h4>
                    <div class="stat-number"><?= $generalStats['users']['total_users'] ?></div>
                    <div class="stat-breakdown">
                        <span class="admin-count"><?= $generalStats['users']['admin_users'] ?> admins</span>
                        <span class="freelancer-count"><?= $generalStats['users']['freelancer_users'] ?> freelancers</span>
                        <span class="blocked-count"><?= $generalStats['users']['blocked_users'] ?> bloqueados</span>
                    </div>
                </div>
            </div>

            <div class="stat-card secondary">
                <div class="stat-icon">
                    <img src="/Images/site/admin/services.png" alt="Serviços">
                </div>
                <div class="stat-content">
                    <h4>Serviços</h4>
                    <div class="stat-number"><?= $generalStats['services']['total_services'] ?></div>
                    <div class="stat-detail">
                        <?= $generalStats['services']['active_services'] ?> ativos
                    </div>
                </div>
            </div>

            <div class="stat-card tertiary">
                <div class="stat-icon">
                    <img src="/Images/site/admin/categories.png" alt="Categorias">
                </div>
                <div class="stat-content">
                    <h4>Categorias</h4>
                    <div class="stat-number"><?= $generalStats['categories']['total_categories'] ?></div>
                </div>
            </div>

            <div class="stat-card quaternary">
                <div class="stat-icon">
                    <img src="/Images/site/admin/newsletter.png" alt="Newsletter">
                </div>
                <div class="stat-content">
                    <h4>Newsletter</h4>
                    <div class="stat-number"><?= $generalStats['newsletter']['total_subscriptions'] ?></div>
                    <div class="stat-detail">subscrições</div>
                </div>
            </div>

            <div class="stat-card quinary">
                <div class="stat-icon">
                    <img src="/Images/site/admin/contacts.png" alt="Contactos">
                </div>
                <div class="stat-content">
                    <h4>Contactos</h4>
                    <div class="stat-number"><?= $generalStats['contacts']['total_contacts'] ?></div>
                    <div class="stat-detail">
                        <?= $generalStats['contacts']['unread_contacts'] ?> não lidos
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Crescimento -->
    <section class="growth-section">
        <h3 class="section-subtitle">Crescimento Mensal</h3>
        <div class="growth-grid">
            <div class="growth-card">
                <h4>Novos Utilizadores</h4>
                <div class="growth-numbers">
                    <span class="current-month"><?= $growthStats['users']['current'] ?></span>
                    <span class="vs">vs</span>
                    <span class="previous-month"><?= $growthStats['users']['previous'] ?></span>
                </div>
                <div class="growth-percent <?= $growthStats['users']['growth_percent'] >= 0 ? 'positive' : 'negative' ?>">
                    <?= $growthStats['users']['growth_percent'] > 0 ? '+' : '' ?><?= $growthStats['users']['growth_percent'] ?>%
                </div>
            </div>
            
            <div class="growth-card">
                <h4>Novos Contactos</h4>
                <div class="growth-numbers">
                    <span class="current-month"><?= $growthStats['contacts']['current'] ?></span>
                    <span class="vs">vs</span>
                    <span class="previous-month"><?= $growthStats['contacts']['previous'] ?></span>
                </div>
                <div class="growth-percent <?= $growthStats['contacts']['growth_percent'] >= 0 ? 'positive' : 'negative' ?>">
                    <?= $growthStats['contacts']['growth_percent'] > 0 ? '+' : '' ?><?= $growthStats['contacts']['growth_percent'] ?>%
                </div>
            </div>
        </div>
    </section>

    <!-- Gráficos dos Últimos 7 Dias -->
    <section class="charts-section">
        <h3 class="section-subtitle">Atividade dos Últimos 7 Dias</h3>
        <div class="charts-grid">
            <!-- Gráfico de Novos Utilizadores -->
            <div class="chart-container">
                <h4>Novos Utilizadores</h4>
                <div class="chart-wrapper">
                    <canvas id="newUsersChart"></canvas>
                </div>
            </div>

            <!-- Gráfico de Contactos -->
            <div class="chart-container">
                <h4>Contactos Recebidos</h4>
                <div class="chart-wrapper">
                    <canvas id="contactsChart"></canvas>
                </div>
            </div>

            <!-- Gráfico de Complaints -->
            <div class="chart-container">
                <h4>Complaints/Disputas</h4>
                <div class="chart-wrapper">
                    <canvas id="complaintsChart"></canvas>
                </div>
            </div>

            <!-- Gráfico de Newsletter (placeholder) -->
            <div class="chart-container">
                <h4>Subscrições Newsletter</h4>
                <div class="chart-wrapper">
                    <canvas id="newsletterChart"></canvas>
                </div>
            </div>
        </div>
    </section>

    <!-- Atividade Recente -->
    <section class="recent-activity">
        <h3 class="section-subtitle">Atividade Recente</h3>
        <div class="activity-grid">
            <!-- Últimos Utilizadores -->
            <div class="activity-card">
                <h4>Últimos Utilizadores</h4>
                <div class="activity-list">
                    <?php if (!empty($recentActivity['users'])): ?>
                        <?php foreach ($recentActivity['users'] as $user): ?>
                            <div class="activity-item">
                                <span class="activity-name"><?= htmlspecialchars($user['name_']) ?></span>
                                <span class="activity-detail">@<?= htmlspecialchars($user['username']) ?></span>
                                <span class="activity-date"><?= date('d/m', strtotime($user['creation_date'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-activity">Sem atividade recente</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Últimos Contactos -->
            <div class="activity-card">
                <h4>Últimos Contactos</h4>
                <div class="activity-list">
                    <?php if (!empty($recentActivity['contacts'])): ?>
                        <?php foreach ($recentActivity['contacts'] as $contact): ?>
                            <div class="activity-item">
                                <span class="activity-name"><?= htmlspecialchars($contact['name_']) ?></span>
                                <span class="activity-detail"><?= htmlspecialchars($contact['subject']) ?></span>
                                <span class="activity-date"><?= date('d/m', strtotime($contact['created_at'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-activity">Sem contactos recentes</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Últimos Serviços -->
            <div class="activity-card">
                <h4>Últimos Serviços</h4>
                <div class="activity-list">
                    <?php if (!empty($recentActivity['services'])): ?>
                        <?php foreach ($recentActivity['services'] as $service): ?>
                            <div class="activity-item">
                                <span class="activity-name"><?= htmlspecialchars($service['name_']) ?></span>
                                <span class="activity-detail">por @<?= htmlspecialchars($service['username']) ?></span>
                                <span class="activity-price">€<?= number_format($service['price_per_hour'], 2) ?>/h</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-activity">Sem serviços recentes</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Chart.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
// Dados dos gráficos
const newUsersData = <?= json_encode($newUsersData) ?>;
const contactsData = <?= json_encode($contactsData) ?>;
const complaintsData = <?= json_encode($complaintsData) ?>;
const newsletterData = <?= json_encode($newsletterData) ?>;

// Configuração comum dos gráficos
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1
            }
        },
        x: {
            grid: {
                display: false
            }
        }
    }
};

// Gráfico de Novos Utilizadores
new Chart(document.getElementById('newUsersChart'), {
    type: 'bar',
    data: {
        labels: newUsersData.map(d => d.formatted_date),
        datasets: [{
            data: newUsersData.map(d => d.count),
            backgroundColor: 'rgba(74, 144, 226, 0.8)',
            borderColor: 'rgba(74, 144, 226, 1)',
            borderWidth: 1
        }]
    },
    options: commonOptions
});

// Gráfico de Contactos
new Chart(document.getElementById('contactsChart'), {
    type: 'bar',
    data: {
        labels: contactsData.map(d => d.formatted_date),
        datasets: [{
            data: contactsData.map(d => d.count),
            backgroundColor: 'rgba(76, 175, 80, 0.8)',
            borderColor: 'rgba(76, 175, 80, 1)',
            borderWidth: 1
        }]
    },
    options: commonOptions
});

// Gráfico de Complaints
new Chart(document.getElementById('complaintsChart'), {
    type: 'bar',
    data: {
        labels: complaintsData.map(d => d.formatted_date),
        datasets: [{
            data: complaintsData.map(d => d.count),
            backgroundColor: 'rgba(255, 193, 7, 0.8)',
            borderColor: 'rgba(255, 193, 7, 1)',
            borderWidth: 1
        }]
    },
    options: commonOptions
});

// Gráfico de Newsletter
new Chart(document.getElementById('newsletterChart'), {
    type: 'bar',
    data: {
        labels: newsletterData.map(d => d.formatted_date),
        datasets: [{
            data: newsletterData.map(d => d.count),
            backgroundColor: 'rgba(156, 39, 176, 0.8)',
            borderColor: 'rgba(156, 39, 176, 1)',
            borderWidth: 1
        }]
    },
    options: commonOptions
});
</script>

<?php drawFooter(); ?>