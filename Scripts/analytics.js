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

// Função para criar gráfico de barras
function createBarChart(canvasId, data, color) {
    return new Chart(document.getElementById(canvasId), {
        type: 'bar',
        data: {
            labels: data.map(d => d.formatted_date),
            datasets: [{
                data: data.map(d => d.count),
                backgroundColor: `rgba(${color}, 0.8)`,
                borderColor: `rgba(${color}, 1)`,
                borderWidth: 1
            }]
        },
        options: commonOptions
    });
}

// Inicializar gráficos quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se os dados estão disponíveis
    if (typeof window.chartData === 'undefined') {
        console.error('Dados dos gráficos não encontrados');
        return;
    }
    
    const { newUsersData, contactsData, complaintsData, newsletterData } = window.chartData;
    
    // Criar os gráficos
    createBarChart('newUsersChart', newUsersData, '74, 144, 226');
    createBarChart('contactsChart', contactsData, '76, 175, 80');
    createBarChart('complaintsChart', complaintsData, '255, 193, 7');
    createBarChart('newsletterChart', newsletterData, '156, 39, 176');
});
