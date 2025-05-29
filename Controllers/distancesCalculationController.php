<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');
require_once(dirname(__FILE__) . '/../Utils/session.php');

/**
 * Calcula a distância entre dois locais baseado nos municípios e distritos
 * 
 * @param string $municipality1 Município de origem
 * @param string $municipality2 Município de destino
 * @param string $district1 Distrito de origem
 * @param string $district2 Distrito de destino
 * @return int Distância em quilómetros (retorna 55 em caso de erro)
 */
function calculateDistance($municipality1, $municipality2, $district1, $district2) {
    try {
        $db = getDatabaseConnection();
        
        // Verificar se ambos os distritos são Porto
        if (strtolower(trim($district1)) === 'porto' && strtolower(trim($district2)) === 'porto') {
            // Consultar tabela distances_Porto usando os municípios
            $stmt = $db->prepare("
                SELECT distance 
                FROM distances_Porto 
                WHERE origin = :origin AND destiny = :destiny
            ");
            
            $stmt->bindParam(':origin', $municipality1, PDO::PARAM_STR);
            $stmt->bindParam(':destiny', $municipality2, PDO::PARAM_STR);
        } else {
            // Consultar tabela distances_Districts usando os distritos
            $stmt = $db->prepare("
                SELECT distance 
                FROM distances_Districts 
                WHERE origin = :origin AND destiny = :destiny
            ");
            
            $stmt->bindParam(':origin', $district1, PDO::PARAM_STR);
            $stmt->bindParam(':destiny', $district2, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Se encontrou resultado, retorna a distância
        if ($result && isset($result['distance'])) {
            return (int) $result['distance'];
        }
        
        // Se não encontrou resultado, retorna valor padrão
        return 55;
        
    } catch (PDOException $e) {
        // Em caso de erro de base de dados, retorna valor padrão
        error_log("Erro ao calcular distância: " . $e->getMessage());
        return 55;
    } catch (Exception $e) {
        // Em caso de qualquer outro erro, retorna valor padrão
        error_log("Erro geral ao calcular distância: " . $e->getMessage());
        return 55;
    }
}

/**
 * Obtém estatísticas gerais da plataforma.
 * 
 * @return array Array com contagens gerais do sistema
 */
function getGeneralStats() {
    try {
        $db = getDatabaseConnection();
        
        // Contagem de utilizadores
        $userStmt = $db->prepare("
            SELECT 
                COUNT(*) as total_users,
                COUNT(CASE WHEN is_admin = 1 THEN 1 END) as admin_users,
                COUNT(CASE WHEN is_freelancer = 1 THEN 1 END) as freelancer_users,
                COUNT(CASE WHEN is_blocked = 1 THEN 1 END) as blocked_users
            FROM User_
        ");
        $userStmt->execute();
        $userStats = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        // Contagem de serviços
        $serviceStmt = $db->prepare("
            SELECT 
                COUNT(*) as total_services,
                COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_services
            FROM Service_
        ");
        $serviceStmt->execute();
        $serviceStats = $serviceStmt->fetch(PDO::FETCH_ASSOC);
        
        // Contagem de categorias
        $categoryStmt = $db->prepare("SELECT COUNT(*) as total_categories FROM Category");
        $categoryStmt->execute();
        $categoryStats = $categoryStmt->fetch(PDO::FETCH_ASSOC);
        
        // Contagem de subscrições da newsletter
        $newsletterStmt = $db->prepare("SELECT COUNT(*) as total_subscriptions FROM Newsletter_email");
        $newsletterStmt->execute();
        $newsletterStats = $newsletterStmt->fetch(PDO::FETCH_ASSOC);
        
        // Contagem de contactos
        $contactStmt = $db->prepare("
            SELECT 
                COUNT(*) as total_contacts,
                COUNT(CASE WHEN is_read = 0 THEN 1 END) as unread_contacts
            FROM Contact
        ");
        $contactStmt->execute();
        $contactStats = $contactStmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'users' => $userStats,
            'services' => $serviceStats,
            'categories' => $categoryStats,
            'newsletter' => $newsletterStats,
            'contacts' => $contactStats
        ];
        
    } catch (PDOException $e) {
        error_log("Erro ao obter estatísticas gerais: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtém dados de registo de novos utilizadores nos últimos 7 dias.
 * 
 * @return array Array com dados para o gráfico
 */
function getNewUsersLast7Days() {
    try {
        $db = getDatabaseConnection();
        
        $stmt = $db->prepare("
            SELECT 
                DATE(creation_date) as date,
                COUNT(*) as count
            FROM User_
            WHERE creation_date >= DATE('now', '-7 days')
            GROUP BY DATE(creation_date)
            ORDER BY date ASC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Preencher os últimos 7 dias, mesmo que não tenham registos
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $count = 0;
            
            foreach ($results as $result) {
                if ($result['date'] === $date) {
                    $count = (int)$result['count'];
                    break;
                }
            }
            
            $data[] = [
                'date' => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'count' => $count
            ];
        }
        
        return $data;
        
    } catch (PDOException $e) {
        error_log("Erro ao obter dados de novos utilizadores: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtém dados de complaints nos últimos 7 dias.
 * 
 * @return array Array com dados para o gráfico
 */
function getComplaintsLast7Days() {
    try {
        $db = getDatabaseConnection();
        
        $stmt = $db->prepare("
            SELECT 
                DATE(date_) as date,
                COUNT(*) as count
            FROM Complaint
            WHERE date_ >= DATE('now', '-7 days')
            GROUP BY DATE(date_)
            ORDER BY date ASC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Preencher os últimos 7 dias
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $count = 0;
            
            foreach ($results as $result) {
                if ($result['date'] === $date) {
                    $count = (int)$result['count'];
                    break;
                }
            }
            
            $data[] = [
                'date' => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'count' => $count
            ];
        }
        
        return $data;
        
    } catch (PDOException $e) {
        error_log("Erro ao obter dados de complaints: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtém dados de contactos nos últimos 7 dias.
 * 
 * @return array Array com dados para o gráfico
 */
function getContactsLast7Days() {
    try {
        $db = getDatabaseConnection();
        
        $stmt = $db->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as count
            FROM Contact
            WHERE created_at >= DATE('now', '-7 days')
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Preencher os últimos 7 dias
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $count = 0;
            
            foreach ($results as $result) {
                if ($result['date'] === $date) {
                    $count = (int)$result['count'];
                    break;
                }
            }
            
            $data[] = [
                'date' => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'count' => $count
            ];
        }
        
        return $data;
        
    } catch (PDOException $e) {
        error_log("Erro ao obter dados de contactos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtém dados de subscrições à newsletter nos últimos 7 dias.
 * Nota: A tabela Newsletter_email não tem campo de data, então esta função
 * pode retornar dados vazios ou pode ser adaptada conforme necessário.
 * 
 * @return array Array com dados para o gráfico
 */
function getNewsletterSubscriptionsLast7Days() {
    try {
        // Como a tabela Newsletter_email não tem campo de data,
        // vamos simular dados ou retornar array vazio
        // Esta função pode ser adaptada se a estrutura da tabela for alterada
        
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $data[] = [
                'date' => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'count' => 0 // Sem dados de data na tabela atual
            ];
        }
        
        return $data;
        
    } catch (Exception $e) {
        error_log("Erro ao obter dados de subscrições: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtém os dados mais recentes de atividade da plataforma.
 * 
 * @return array Array com atividades recentes
 */
function getRecentActivity() {
    try {
        $db = getDatabaseConnection();
        
        // Últimos utilizadores registados
        $recentUsersStmt = $db->prepare("
            SELECT name_, username, creation_date
            FROM User_
            ORDER BY creation_date DESC
            LIMIT 5
        ");
        $recentUsersStmt->execute();
        $recentUsers = $recentUsersStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Últimos contactos
        $recentContactsStmt = $db->prepare("
            SELECT name_, subject, created_at
            FROM Contact
            ORDER BY created_at DESC, created_time DESC
            LIMIT 5
        ");
        $recentContactsStmt->execute();
        $recentContacts = $recentContactsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Últimos serviços criados
        $recentServicesStmt = $db->prepare("
            SELECT s.name_, u.username, s.price_per_hour
            FROM Service_ s
            JOIN User_ u ON s.freelancer_id = u.id
            ORDER BY s.id DESC
            LIMIT 5
        ");
        $recentServicesStmt->execute();
        $recentServices = $recentServicesStmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'users' => $recentUsers,
            'contacts' => $recentContacts,
            'services' => $recentServices
        ];
        
    } catch (PDOException $e) {
        error_log("Erro ao obter atividade recente: " . $e->getMessage());
        return [
            'users' => [],
            'contacts' => [],
            'services' => []
        ];
    }
}

/**
 * Obtém estatísticas de crescimento em relação ao mês anterior.
 * 
 * @return array Array com dados de crescimento
 */
function getGrowthStats() {
    try {
        $db = getDatabaseConnection();
        
        // Crescimento de utilizadores (este mês vs mês anterior)
        $userGrowthStmt = $db->prepare("
            SELECT 
                COUNT(CASE WHEN creation_date >= DATE('now', 'start of month') THEN 1 END) as current_month,
                COUNT(CASE WHEN creation_date >= DATE('now', 'start of month', '-1 month') 
                           AND creation_date < DATE('now', 'start of month') THEN 1 END) as previous_month
            FROM User_
        ");
        $userGrowthStmt->execute();
        $userGrowth = $userGrowthStmt->fetch(PDO::FETCH_ASSOC);
        
        // Crescimento de contactos
        $contactGrowthStmt = $db->prepare("
            SELECT 
                COUNT(CASE WHEN created_at >= DATE('now', 'start of month') THEN 1 END) as current_month,
                COUNT(CASE WHEN created_at >= DATE('now', 'start of month', '-1 month') 
                           AND created_at < DATE('now', 'start of month') THEN 1 END) as previous_month
            FROM Contact
        ");
        $contactGrowthStmt->execute();
        $contactGrowth = $contactGrowthStmt->fetch(PDO::FETCH_ASSOC);
        
        // Calcular percentagens de crescimento
        $userGrowthPercent = calculateGrowthPercentage($userGrowth['current_month'], $userGrowth['previous_month']);
        $contactGrowthPercent = calculateGrowthPercentage($contactGrowth['current_month'], $contactGrowth['previous_month']);
        
        return [
            'users' => [
                'current' => (int)$userGrowth['current_month'],
                'previous' => (int)$userGrowth['previous_month'],
                'growth_percent' => $userGrowthPercent
            ],
            'contacts' => [
                'current' => (int)$contactGrowth['current_month'],
                'previous' => (int)$contactGrowth['previous_month'],
                'growth_percent' => $contactGrowthPercent
            ]
        ];
        
    } catch (PDOException $e) {
        error_log("Erro ao obter estatísticas de crescimento: " . $e->getMessage());
        return [
            'users' => ['current' => 0, 'previous' => 0, 'growth_percent' => 0],
            'contacts' => ['current' => 0, 'previous' => 0, 'growth_percent' => 0]
        ];
    }
}

/**
 * Calcula a percentagem de crescimento entre dois valores.
 * 
 * @param int $current Valor atual
 * @param int $previous Valor anterior
 * @return float Percentagem de crescimento
 */
function calculateGrowthPercentage($current, $previous) {
    if ($previous == 0) {
        return $current > 0 ? 100 : 0;
    }
    
    return round((($current - $previous) / $previous) * 100, 1);
}

/**
 * Taxas de câmbio fixas (EUR como base)
 * @return array Taxas de câmbio
 */
function getExchangeRates() {
    return [
        'EUR' => 1.00,
        'USD' => 1.10,
        'GBP' => 0.85,
        'BRL' => 6.20
    ];
}

/**
 * Converte um valor de EUR para a moeda especificada
 * @param float $value Valor em EUR
 * @param string $targetCurrency Código da moeda de destino (EUR, USD, GBP, BRL)
 * @return float Valor convertido
 */
function convertCurrency($value, $targetCurrency = 'EUR') {
    // Se a moeda de destino for EUR, retornar o valor original
    if (strtoupper($targetCurrency) === 'EUR') {
        return $value;
    }
    
    // Obter taxas de câmbio
    $rates = getExchangeRates();
    
    if (!isset($rates[strtoupper($targetCurrency)])) {
        // Se não encontrar a taxa, retornar o valor original em EUR
        return $value;
    }
    
    $rate = $rates[strtoupper($targetCurrency)];
    return $value * $rate;
}

/**
 * Converte e formata um preço para exibição, baseado na moeda do utilizador
 * @param float $price Preço em EUR
 * @param int|null $userId ID do utilizador (se null, usa o utilizador atual da sessão)
 * @param int $decimals Número de decimais (padrão: 2)
 * @return string Preço formatado com símbolo da moeda
 */
function convertAndFormatPrice($price, $userId = null, $decimals = 2) {
    // Se não foi fornecido userId, tentar obter da sessão
    if ($userId === null) {
        $userId = getCurrentUserId();
    }
    
    // Se não há utilizador logado, usar EUR como padrão
    if (!$userId) {
        $currency = 'EUR';
    } else {
        // Obter a moeda preferida do utilizador
        $currency = getUserCurrency($userId);
    }
    
    // Converter o preço
    $convertedPrice = convertCurrency($price, $currency);
    
    // Obter símbolo da moeda
    $symbol = getCurrencySymbol($currency);
    
    // Formatar o preço
    return formatPrice($convertedPrice, $symbol, $decimals);
}

/**
 * Obtém a moeda preferida de um utilizador
 * @param int $userId ID do utilizador
 * @return string Código da moeda (EUR por padrão)
 */
function getUserCurrency($userId) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT currency FROM User_ WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? strtoupper($result['currency']) : 'EUR';
    } catch (PDOException $e) {
        error_log("Erro ao obter moeda do utilizador: " . $e->getMessage());
        return 'EUR';
    }
}

/**
 * Obtém o símbolo da moeda
 * @param string $currencyCode Código da moeda
 * @return string Símbolo da moeda
 */
function getCurrencySymbol($currencyCode) {
    $symbols = [
        'EUR' => '€',
        'USD' => '$',
        'GBP' => '£',
        'BRL' => 'R$'
    ];
    
    return $symbols[strtoupper($currencyCode)] ?? '€';
}

/**
 * Formata um preço com o símbolo da moeda
 * @param float $price Preço a formatar
 * @param string $symbol Símbolo da moeda
 * @param int $decimals Número de decimais
 * @return string Preço formatado
 */
function formatPrice($price, $symbol, $decimals = 2) {
    // Configurações de formatação por moeda
    $formatConfig = [
        '€' => ['position' => 'after', 'separator' => ',', 'thousands' => '.'],
        '$' => ['position' => 'before', 'separator' => '.', 'thousands' => ','],
        '£' => ['position' => 'before', 'separator' => '.', 'thousands' => ','],
        'R$' => ['position' => 'before', 'separator' => ',', 'thousands' => '.']
    ];
    
    $config = $formatConfig[$symbol] ?? $formatConfig['€'];
    
    // Formatar o número
    $formattedPrice = number_format($price, $decimals, $config['separator'], $config['thousands']);
    
    // Adicionar símbolo na posição correta
    if ($config['position'] === 'before') {
        return $symbol . $formattedPrice;
    } else {
        return $formattedPrice . $symbol;
    }
}

/**
 * Converte múltiplos preços de uma vez (útil para listas de produtos)
 * @param array $prices Array de preços em EUR
 * @param string $targetCurrency Moeda de destino
 * @return array Array de preços convertidos
 */
function convertMultiplePrices($prices, $targetCurrency = 'EUR') {
    if (strtoupper($targetCurrency) === 'EUR') {
        return $prices;
    }
    
    $rates = getExchangeRates();
    if (!isset($rates[strtoupper($targetCurrency)])) {
        return $prices;
    }
    
    $rate = $rates[strtoupper($targetCurrency)];
    return array_map(function($price) use ($rate) {
        return $price * $rate;
    }, $prices);
}

/**
 * Obtém informações completas sobre uma moeda
 * @param string $currencyCode Código da moeda
 * @return array Informações da moeda
 */
function getCurrencyInfo($currencyCode) {
    $currencies = [
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'position' => 'after',
            'decimals' => 2
        ],
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'position' => 'before',
            'decimals' => 2
        ],
        'GBP' => [
            'name' => 'British Pound',
            'symbol' => '£',
            'position' => 'before',
            'decimals' => 2
        ],
        'BRL' => [
            'name' => 'Brazilian Real',
            'symbol' => 'R$',
            'position' => 'before',
            'decimals' => 2
        ]
    ];
    
    return $currencies[strtoupper($currencyCode)] ?? $currencies['EUR'];
}

/**
 * Taxas de conversão de moeda (EUR como base) - Função duplicada
 * Em produção, estas taxas devem ser obtidas de uma API de câmbio
 */
function getCurrencyRates() {
    return [
        'eur' => 1.0,      // Euro (base)
        'usd' => 1.08,     // Dólar Americano
        'gbp' => 0.86,     // Libra Esterlina
        'brl' => 5.45      // Real Brasileiro
    ];
}

/**
 * Obtém a moeda preferida do utilizador atual
 * 
 * @return string Código da moeda (padrão: 'eur')
 */
function getUserPreferredCurrency() {
    if (!isUserLoggedIn()) {
        return 'eur'; // Moeda padrão para utilizadores não logados
    }
    
    $userId = getCurrentUserId();
    if (!$userId) {
        return 'eur';
    }
    
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT currency FROM User_ WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? strtolower($result['currency']) : 'eur';
    } catch (PDOException $e) {
        error_log("Erro ao obter moeda do utilizador: " . $e->getMessage());
        return 'eur';
    }
}

/**
 * Converte um preço para a moeda do utilizador atual
 * 
 * @param float $priceInEur Preço em EUR
 * @return array Array com 'amount' e 'symbol'
 */
function convertPriceForUser($priceInEur) {
    $userCurrency = getUserPreferredCurrency();
    $convertedAmount = convertCurrency($priceInEur, $userCurrency);
    $symbol = getCurrencySymbol($userCurrency);
    
    return [
        'amount' => $convertedAmount,
        'symbol' => $symbol,
        'currency' => $userCurrency
    ];
}

/**
 * Formata um preço convertido para exibição
 * 
 * @param float $priceInEur Preço em EUR
 * @param int $decimals Número de casas decimais (padrão: 2)
 * @return string Preço formatado com símbolo da moeda
 */
function formatPriceForUser($priceInEur, $decimals = 2) {
    $converted = convertPriceForUser($priceInEur);
    
    // Ajustar decimais baseado na moeda
    if ($converted['currency'] === 'brl') {
        $decimals = 2; // Real sempre com 2 decimais
    } elseif ($converted['currency'] === 'eur' || $converted['currency'] === 'usd') {
        $decimals = 2; // Euro e Dólar com 2 decimais
    } elseif ($converted['currency'] === 'gbp') {
        $decimals = 2; // Libra com 2 decimais
    }
    
    return $converted['symbol'] . number_format($converted['amount'], $decimals, ',', '');
}

/**
 * Obtém informações completas da moeda do utilizador
 * 
 * @return array Array com informações da moeda
 */
function getUserCurrencyInfo() {
    $userCurrency = getUserPreferredCurrency();
    $symbol = getCurrencySymbol($userCurrency);
    
    $names = [
        'eur' => 'Euro',
        'usd' => 'Dólar Americano',
        'gbp' => 'Libra Esterlina',
        'brl' => 'Real Brasileiro'
    ];
    
    return [
        'code' => $userCurrency,
        'symbol' => $symbol,
        'name' => $names[$userCurrency] ?? 'Euro'
    ];
}

/**
 * Aplica conversão de moeda a um array de serviços
 * 
 * @param array $services Array de serviços
 * @return array Serviços com preços convertidos
 */
function convertServicesPrice($services) {
    if (empty($services)) {
        return $services;
    }
    
    $userCurrency = getUserPreferredCurrency();
    $symbol = getCurrencySymbol($userCurrency);
    
    foreach ($services as &$service) {
        if (isset($service['price_per_hour'])) {
            $originalPrice = $service['price_per_hour'];
            $convertedPrice = convertCurrency($originalPrice, $userCurrency);
            
            // Manter preço original e adicionar convertido
            $service['price_per_hour_original'] = $originalPrice;
            $service['price_per_hour_converted'] = $convertedPrice;
            $service['currency_symbol'] = $symbol;
            $service['currency_code'] = $userCurrency;
            
            // Converter também preços com desconto se existirem
            if (isset($service['promotion']) && $service['promotion'] > 0) {
                $discountedPrice = $originalPrice * (1 - $service['promotion'] / 100);
                $service['discounted_price_converted'] = convertCurrency($discountedPrice, $userCurrency);
            }
        }
    }
    
    return $services;
}
?>
