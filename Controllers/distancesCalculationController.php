<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');
require_once(dirname(__FILE__) . '/../Utils/session.php');

/**
 * Controller para cálculos de distâncias e conversão de moedas
 */

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
 * Cálculo de distâncias entre localidades do Porto
 * @param string $origin Origem
 * @param string $destination Destino
 * @return float Distância em km
 */
function calculateDistance($origin, $destination) {
    $db = getDatabaseConnection();
    
    try {
        // Procurar distância direta
        $stmt = $db->prepare("SELECT distance FROM distances_Porto WHERE origin = :origin AND destiny = :destination");
        $stmt->execute([':origin' => $origin, ':destination' => $destination]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return floatval($result['distance']);
        }
        
        // Procurar distância inversa
        $stmt = $db->prepare("SELECT distance FROM distances_Porto WHERE origin = :destination AND destiny = :origin");
        $stmt->execute([':origin' => $destination, ':destination' => $origin]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return floatval($result['distance']);
        }
        
        // Se não encontrar, retornar distância padrão
        return 10.0;
        
    } catch (PDOException $e) {
        error_log("Erro ao calcular distância: " . $e->getMessage());
        return 10.0;
    }
}

/**
 * Cálculo de distâncias entre distritos
 * @param string $origin Distrito de origem
 * @param string $destination Distrito de destino
 * @return float Distância em km
 */
function calculateDistrictDistance($origin, $destination) {
    $db = getDatabaseConnection();
    
    try {
        // Procurar distância direta
        $stmt = $db->prepare("SELECT distance FROM distances_Districts WHERE origin = :origin AND destiny = :destination");
        $stmt->execute([':origin' => $origin, ':destination' => $destination]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return floatval($result['distance']);
        }
        
        // Procurar distância inversa
        $stmt = $db->prepare("SELECT distance FROM distances_Districts WHERE origin = :destination AND destiny = :origin");
        $stmt->execute([':origin' => $destination, ':destination' => $origin]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return floatval($result['distance']);
        }
        
        // Se não encontrar, retornar distância padrão
        return 50.0;
        
    } catch (PDOException $e) {
        error_log("Erro ao calcular distância entre distritos: " . $e->getMessage());
        return 50.0;
    }
}

/**
 * Calcula taxa de deslocação baseada na distância
 * @param float $distance Distância em km
 * @param float $ratePerKm Taxa por km (padrão: €0.50)
 * @param int|null $userId ID do utilizador para conversão de moeda
 * @return float Taxa de deslocação convertida
 */
function calculateTravelFee($distance, $ratePerKm = 0.50, $userId = null) {
    $baseFee = $distance * $ratePerKm;
    
    // Se userId fornecido, converter para a moeda do utilizador
    if ($userId) {
        $currency = getUserCurrency($userId);
        return convertCurrency($baseFee, $currency);
    }
    
    return $baseFee;
}

/**
 * Calcula e formata taxa de deslocação
 * @param float $distance Distância em km
 * @param float $ratePerKm Taxa por km
 * @param int|null $userId ID do utilizador
 * @return string Taxa formatada
 */
function calculateAndFormatTravelFee($distance, $ratePerKm = 0.50, $userId = null) {
    $baseFee = $distance * $ratePerKm;
    return convertAndFormatPrice($baseFee, $userId);
}

/**
 * Taxas de conversão de moeda (EUR como base)
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

/**
 * Obtém estatísticas gerais do sistema com conversão de moeda
 * 
 * @return array Array com estatísticas gerais
 */
function getGeneralStats() {
    try {
        $db = getDatabaseConnection();
        $userCurrency = getUserPreferredCurrency();
        $currencySymbol = getCurrencySymbol($userCurrency);
        
        // Estatísticas básicas
        $stats = [];
        
        // Total de utilizadores
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM User_");
        $stmt->execute();
        $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de serviços ativos
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM Service_ WHERE is_active = 1");
        $stmt->execute();
        $stats['active_services'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de categorias
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM Category");
        $stmt->execute();
        $stats['total_categories'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Preço médio dos serviços (convertido)
        $stmt = $db->prepare("SELECT AVG(price_per_hour) as avg_price FROM Service_ WHERE is_active = 1");
        $stmt->execute();
        $avgPriceEur = $stmt->fetch(PDO::FETCH_ASSOC)['avg_price'];
        $stats['average_price'] = convertCurrency($avgPriceEur ?: 0, $userCurrency);
        $stats['average_price_formatted'] = $currencySymbol . number_format($stats['average_price'], 2, ',', '');
        
        // Total de pedidos
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM Service_Data");
        $stmt->execute();
        $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Receita total (convertida)
        $stmt = $db->prepare("SELECT SUM(final_price) as total_revenue FROM Service_Data WHERE status_ = 'completed'");
        $stmt->execute();
        $totalRevenueEur = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'];
        $stats['total_revenue'] = convertCurrency($totalRevenueEur ?: 0, $userCurrency);
        $stats['total_revenue_formatted'] = $currencySymbol . number_format($stats['total_revenue'], 2, ',', '');
        
        // Informações da moeda
        $stats['currency_code'] = $userCurrency;
        $stats['currency_symbol'] = $currencySymbol;
        
        return $stats;
        
    } catch (PDOException $e) {
        error_log("Erro ao obter estatísticas gerais: " . $e->getMessage());
        return [
            'total_users' => 0,
            'active_services' => 0,
            'total_categories' => 0,
            'average_price' => 0,
            'average_price_formatted' => '€0,00',
            'total_orders' => 0,
            'total_revenue' => 0,
            'total_revenue_formatted' => '€0,00',
            'currency_code' => 'eur',
            'currency_symbol' => '€'
        ];
    }
}

/**
 * Obtém o número de novos utilizadores registados nos últimos 7 dias
 * 
 * @return array Array com dados dos novos utilizadores por dia
 */
function getNewUsersLast7Days() {
    try {
        $db = getDatabaseConnection();
        
        // Query para obter novos utilizadores dos últimos 7 dias
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
        
        // Criar array com todos os 7 dias (preenchendo com 0 se não houver dados)
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $last7Days[$date] = 0;
        }
        
        // Preencher com os dados reais
        foreach ($results as $result) {
            if (isset($last7Days[$result['date']])) {
                $last7Days[$result['date']] = (int)$result['count'];
            }
        }
        
        // Converter para formato mais útil
        $formattedData = [];
        foreach ($last7Days as $date => $count) {
            $formattedData[] = [
                'date' => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'day_name' => date('D', strtotime($date)),
                'count' => $count
            ];
        }
        
        // Calcular totais
        $totalNewUsers = array_sum($last7Days);
        $averagePerDay = $totalNewUsers > 0 ? round($totalNewUsers / 7, 1) : 0;
        
        return [
            'data' => $formattedData,
            'total_new_users' => $totalNewUsers,
            'average_per_day' => $averagePerDay,
            'period_start' => date('d/m/Y', strtotime('-6 days')),
            'period_end' => date('d/m/Y')
        ];
        
    } catch (PDOException $e) {
        error_log("Erro ao obter novos utilizadores dos últimos 7 dias: " . $e->getMessage());
        
        // Retornar dados vazios em caso de erro
        $emptyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $emptyData[] = [
                'date' => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'day_name' => date('D', strtotime($date)),
                'count' => 0
            ];
        }
        
        return [
            'data' => $emptyData,
            'total_new_users' => 0,
            'average_per_day' => 0,
            'period_start' => date('d/m/Y', strtotime('-6 days')),
            'period_end' => date('d/m/Y')
        ];
    }
}

/**
 * Obtém o número de reclamações (complaints) dos últimos 7 dias
 * 
 * @return array Array com dados das reclamações por dia
 */
function getComplaintsLast7Days() {
    try {
        $db = getDatabaseConnection();
        
        // Query para obter reclamações dos últimos 7 dias
        $stmt = $db->prepare("
            SELECT 
                DATE(date_) as date,
                COUNT(*) as count,
                COUNT(CASE WHEN is_accepted = 1 THEN 1 END) as accepted_count,
                COUNT(CASE WHEN is_accepted = 0 THEN 1 END) as pending_count
            FROM Complaint 
            WHERE date_ >= DATE('now', '-7 days')
            GROUP BY DATE(date_)
            ORDER BY date ASC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Criar array com todos os 7 dias (preenchendo com 0 se não houver dados)
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $last7Days[$date] = [
                'total' => 0,
                'accepted' => 0,
                'pending' => 0
            ];
        }
        
        // Preencher com os dados reais
        foreach ($results as $result) {
            if (isset($last7Days[$result['date']])) {
                $last7Days[$result['date']] = [
                    'total' => (int)$result['count'],
                    'accepted' => (int)$result['accepted_count'],
                    'pending' => (int)$result['pending_count']
                ];
            }
        }
        
        // Converter para formato mais útil
        $formattedData = [];
        $totalComplaints = 0;
        $totalAccepted = 0;
        $totalPending = 0;
        
        foreach ($last7Days as $date => $counts) {
            $formattedData[] = [
                'date' => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'day_name' => date('D', strtotime($date)),
                'total_count' => $counts['total'],
                'accepted_count' => $counts['accepted'],
                'pending_count' => $counts['pending']
            ];
            
            $totalComplaints += $counts['total'];
            $totalAccepted += $counts['accepted'];
            $totalPending += $counts['pending'];
        }
        
        // Calcular estatísticas
        $averagePerDay = $totalComplaints > 0 ? round($totalComplaints / 7, 1) : 0;
        $acceptanceRate = $totalComplaints > 0 ? round(($totalAccepted / $totalComplaints) * 100, 1) : 0;
        
        return [
            'data' => $formattedData,
            'total_complaints' => $totalComplaints,
            'total_accepted' => $totalAccepted,
            'total_pending' => $totalPending,
            'average_per_day' => $averagePerDay,
            'acceptance_rate' => $acceptanceRate,
            'period_start' => date('d/m/Y', strtotime('-6 days')),
            'period_end' => date('d/m/Y')
        ];
        
    } catch (PDOException $e) {
        error_log("Erro ao obter reclamações dos últimos 7 dias: " . $e->getMessage());
        
        // Retornar dados vazios em caso de erro
        $emptyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $emptyData[] = [
                'date' => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'day_name' => date('D', strtotime($date)),
                'total_count' => 0,
                'accepted_count' => 0,
                'pending_count' => 0
            ];
        }
        
        return [
            'data' => $emptyData,
            'total_complaints' => 0,
            'total_accepted' => 0,
            'total_pending' => 0,
            'average_per_day' => 0,
            'acceptance_rate' => 0,
            'period_start' => date('d/m/Y', strtotime('-6 days')),
            'period_end' => date('d/m/Y')
        ];
    }
}

/**
 * Obtém o número de mensagens de contacto dos últimos 7 dias
 * 
 * @return array Array com dados das mensagens de contacto por dia
 */
function getContactsLast7Days() {
    try {
        $db = getDatabaseConnection();
        
        // Query para obter mensagens de contacto dos últimos 7 dias
        $stmt = $db->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as count,
                COUNT(CASE WHEN is_read = 1 THEN 1 END) as read_count,
                COUNT(CASE WHEN is_read = 0 THEN 1 END) as unread_count,
                COUNT(CASE WHEN admin_response IS NOT NULL THEN 1 END) as responded_count
            FROM Contact 
            WHERE created_at >= DATE('now', '-7 days')
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Criar array com todos os 7 dias (preenchendo com 0 se não houver dados)
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $last7Days[$date] = [
                'total' => 0,
                'read' => 0,
                'unread' => 0,
                'responded' => 0
            ];
        }
        
        // Preencher com os dados reais
        foreach ($results as $result) {
            if (isset($last7Days[$result['date']])) {
                $last7Days[$result['date']] = [
                    'total' => (int)$result['count'],
                    'read' => (int)$result['read_count'],
                    'unread' => (int)$result['unread_count'],
                    'responded' => (int)$result['responded_count']
                ];
            }
        }
        
        // Converter para formato mais útil
        $formattedData = [];
        $totalContacts = 0;
        $totalRead = 0;
        $totalUnread = 0;
        $totalResponded = 0;
        
        foreach ($last7Days as $date => $counts) {
            $formattedData[] = [
                'date' => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'day_name' => date('D', strtotime($date)),
                'total_count' => $counts['total'],
                'read_count' => $counts['read'],
                'unread_count' => $counts['unread'],
                'responded_count' => $counts['responded']
            ];
            
            $totalContacts += $counts['total'];
            $totalRead += $counts['read'];
            $totalUnread += $counts['unread'];
            $totalResponded += $counts['responded'];
        }
        
        // Calcular estatísticas
        $averagePerDay = $totalContacts > 0 ? round($totalContacts / 7, 1) : 0;
        $readRate = $totalContacts > 0 ? round(($totalRead / $totalContacts) * 100, 1) : 0;
        $responseRate = $totalContacts > 0 ? round(($totalResponded / $totalContacts) * 100, 1) : 0;
        
        return [
            'data' => $formattedData,
            'total_contacts' => $totalContacts,
            'total_read' => $totalRead,
            'total_unread' => $totalUnread,
            'total_responded' => $totalResponded,
            'average_per_day' => $averagePerDay,
            'read_rate' => $readRate,
            'response_rate' => $responseRate,
            'period_start' => date('d/m/Y', strtotime('-6 days')),
            'period_end' => date('d/m/Y')
        ];
        
    } catch (PDOException $e) {
        error_log("Erro ao obter contactos dos últimos 7 dias: " . $e->getMessage());
        
        // Retornar dados vazios em caso de erro
        $emptyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $emptyData[] = [
                'date' => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'day_name' => date('D', strtotime($date)),
                'total_count' => 0,
                'read_count' => 0,
                'unread_count' => 0,
                'responded_count' => 0
            ];
        }
        
        return [
            'data' => $emptyData,
            'total_contacts' => 0,
            'total_read' => 0,
            'total_unread' => 0,
            'total_responded' => 0,
            'average_per_day' => 0,
            'read_rate' => 0,
            'response_rate' => 0,
            'period_start' => date('d/m/Y', strtotime('-6 days')),
            'period_end' => date('d/m/Y')
        ];
    }
}

/**
 * Obtém o número de subscrições da newsletter dos últimos 7 dias
 * 
 * @return array Array com dados das subscrições por dia
 */
function getNewsletterSubscriptionsLast7Days() {
    try {
        $db = getDatabaseConnection();
        
        // Como a tabela Newsletter_email não tem campo de data, vamos simular com base no ID
        // Em produção, seria ideal ter um campo created_at na tabela
        $stmt = $db->prepare("
            SELECT 
                DATE('now', '-' || (7 - ((id % 7) + 1)) || ' days') as date,
                COUNT(*) as count
            FROM Newsletter_email 
            WHERE id > (SELECT COALESCE(MAX(id), 0) - 70 FROM Newsletter_email)
            GROUP BY (id % 7)
            ORDER BY date ASC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Abordagem alternativa: distribuir as subscrições existentes pelos últimos 7 dias
        // (simulação para demonstração, pois a tabela não tem timestamps)
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM Newsletter_email");
        $stmt->execute();
        $totalSubscriptions = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Criar array com todos os 7 dias
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            // Simular distribuição (em produção seria baseado em dados reais)
            $simulatedCount = rand(0, 5); // Simulação para demonstração
            $last7Days[$date] = $simulatedCount;
        }
        
        // Converter para formato mais útil
        $formattedData = [];
        $totalNewSubscriptions = 0;
        
        foreach ($last7Days as $date => $count) {
            $formattedData[] = [
                'date' => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'day_name' => date('D', strtotime($date)),
                'count' => $count
            ];
            
            $totalNewSubscriptions += $count;
        }
        
        // Calcular estatísticas
        $averagePerDay = $totalNewSubscriptions > 0 ? round($totalNewSubscriptions / 7, 1) : 0;
        
        // Obter total geral de subscrições para contexto
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM Newsletter_email");
        $stmt->execute();
        $totalOverall = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Calcular taxa de crescimento (percentual das novas subscrições em relação ao total)
        $growthRate = $totalOverall > 0 ? round(($totalNewSubscriptions / $totalOverall) * 100, 2) : 0;
        
        return [
            'data' => $formattedData,
            'total_new_subscriptions' => $totalNewSubscriptions,
            'total_overall_subscriptions' => $totalOverall,
            'average_per_day' => $averagePerDay,
            'growth_rate' => $growthRate,
            'period_start' => date('d/m/Y', strtotime('-6 days')),
            'period_end' => date('d/m/Y'),
            'note' => 'Dados simulados - recomenda-se adicionar campo created_at à tabela Newsletter_email'
        ];
        
    } catch (PDOException $e) {
        error_log("Erro ao obter subscrições da newsletter dos últimos 7 dias: " . $e->getMessage());
        
        // Retornar dados vazios em caso de erro
        $emptyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $emptyData[] = [
                'date' => $date,
                'formatted_date' => date('d/m', strtotime($date)),
                'day_name' => date('D', strtotime($date)),
                'count' => 0
            ];
        }
        
        return [
            'data' => $emptyData,
            'total_new_subscriptions' => 0,
            'total_overall_subscriptions' => 0,
            'average_per_day' => 0,
            'growth_rate' => 0,
            'period_start' => date('d/m/Y', strtotime('-6 days')),
            'period_end' => date('d/m/Y'),
            'note' => 'Dados não disponíveis - erro na base de dados'
        ];
    }
}

/**
 * Obtém atividade recente do sistema (últimas 24-48 horas)
 * Combina várias tabelas para mostrar um feed de atividades
 * 
 * @param int $limit Número máximo de atividades a retornar (padrão: 20)
 * @return array Array com atividades recentes ordenadas por data/hora
 */
function getRecentActivity($limit = 20) {
    try {
        $db = getDatabaseConnection();
        $activities = [];
        
        // 1. Novos utilizadores (últimas 48 horas)
        $stmt = $db->prepare("
            SELECT 
                'new_user' as type,
                name_ as title,
                CONCAT('Novo utilizador registado: ', name_) as description,
                creation_date as date,
                TIME('12:00:00') as time,
                id as reference_id
            FROM User_ 
            WHERE creation_date >= DATE('now', '-2 days')
            ORDER BY creation_date DESC, id DESC
            LIMIT 5
        ");
        $stmt->execute();
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));
        
        // 2. Novos serviços criados (últimas 48 horas)
        // Como Service_ não tem campo de data, vamos usar os IDs mais recentes
        $stmt = $db->prepare("
            SELECT 
                'new_service' as type,
                s.name_ as title,
                CONCAT('Novo serviço publicado: ', s.name_, ' por ', u.name_) as description,
                DATE('now', '-' || (s.id % 2) || ' days') as date,
                TIME('14:30:00') as time,
                s.id as reference_id
            FROM Service_ s
            JOIN User_ u ON s.freelancer_id = u.id
            WHERE s.id > (SELECT COALESCE(MAX(id), 0) - 10 FROM Service_)
            AND s.is_active = 1
            ORDER BY s.id DESC
            LIMIT 5
        ");
        $stmt->execute();
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));
        
        // 3. Mensagens de contacto recentes (últimas 48 horas)
        $stmt = $db->prepare("
            SELECT 
                'new_contact' as type,
                subject as title,
                CONCAT('Nova mensagem de contacto: ', subject, ' de ', name_) as description,
                created_at as date,
                created_time as time,
                id as reference_id
            FROM Contact 
            WHERE created_at >= DATE('now', '-2 days')
            ORDER BY created_at DESC, created_time DESC
            LIMIT 5
        ");
        $stmt->execute();
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));
        
        // 4. Pedidos de serviços recentes (últimas 48 horas)
        $stmt = $db->prepare("
            SELECT 
                'new_order' as type,
                s.name_ as title,
                CONCAT('Novo pedido: ', s.name_, ' por ', u.name_) as description,
                sd.date_ as date,
                sd.time_ as time,
                sd.id as reference_id
            FROM Service_Data sd
            JOIN Service_ s ON sd.service_id = s.id
            JOIN User_ u ON sd.user_id = u.id
            WHERE sd.date_ >= DATE('now', '-2 days')
            ORDER BY sd.date_ DESC, sd.time_ DESC
            LIMIT 5
        ");
        $stmt->execute();
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));
        
        // 5. Reclamações recentes (últimas 48 horas)
        $stmt = $db->prepare("
            SELECT 
                'new_complaint' as type,
                title as title,
                CONCAT('Nova reclamação: ', title) as description,
                date_ as date,
                time_ as time,
                id as reference_id
            FROM Complaint 
            WHERE date_ >= DATE('now', '-2 days')
            ORDER BY date_ DESC, time_ DESC
            LIMIT 3
        ");
        $stmt->execute();
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));
        
        // 6. Pedidos de desbloqueio recentes (últimas 48 horas)
        $stmt = $db->prepare("
            SELECT 
                'unblock_appeal' as type,
                ua.title as title,
                CONCAT('Pedido de desbloqueio: ', ua.title, ' por ', u.name_) as description,
                ua.date_ as date,
                ua.time_ as time,
                ua.id as reference_id
            FROM Unblock_Appeal ua
            JOIN User_ u ON ua.user_id = u.id
            WHERE ua.date_ >= DATE('now', '-2 days')
            ORDER BY ua.date_ DESC, ua.time_ DESC
            LIMIT 3
        ");
        $stmt->execute();
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));
        
        // Ordenar todas as atividades por data e hora (mais recentes primeiro)
        usort($activities, function($a, $b) {
            $dateTimeA = $a['date'] . ' ' . ($a['time'] ?? '00:00:00');
            $dateTimeB = $b['date'] . ' ' . ($b['time'] ?? '00:00:00');
            return strtotime($dateTimeB) - strtotime($dateTimeA);
        });
        
        // Limitar o número de resultados
        $activities = array_slice($activities, 0, $limit);
        
        // Formatar as atividades para exibição
        $formattedActivities = [];
        foreach ($activities as $activity) {
            $datetime = $activity['date'] . ' ' . ($activity['time'] ?? '00:00:00');
            $timeAgo = getTimeAgo($datetime);
            
            $formattedActivities[] = [
                'type' => $activity['type'],
                'title' => $activity['title'],
                'description' => $activity['description'],
                'date' => $activity['date'],
                'time' => $activity['time'] ?? '00:00:00',
                'datetime' => $datetime,
                'time_ago' => $timeAgo,
                'formatted_date' => date('d/m/Y', strtotime($activity['date'])),
                'formatted_time' => date('H:i', strtotime($activity['time'] ?? '00:00:00')),
                'reference_id' => $activity['reference_id'],
                'icon' => getActivityIcon($activity['type']),
                'css_class' => getActivityCssClass($activity['type'])
            ];
        }
        
        return [
            'activities' => $formattedActivities,
            'total_count' => count($formattedActivities),
            'last_updated' => date('d/m/Y H:i:s'),
            'period_description' => 'Últimas 48 horas'
        ];
        
    } catch (PDOException $e) {
        error_log("Erro ao obter atividade recente: " . $e->getMessage());
        
        return [
            'activities' => [],
            'total_count' => 0,
            'last_updated' => date('d/m/Y H:i:s'),
            'period_description' => 'Erro ao carregar atividades',
            'error' => 'Não foi possível carregar as atividades recentes'
        ];
    }
}

/**
 * Calcula tempo decorrido em formato amigável
 * 
 * @param string $datetime Data e hora no formato Y-m-d H:i:s
 * @return string Tempo decorrido (ex: "há 2 horas", "há 1 dia")
 */
function getTimeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'há poucos segundos';
    if ($time < 3600) return 'há ' . floor($time/60) . ' minuto' . (floor($time/60) != 1 ? 's' : '');
    if ($time < 86400) return 'há ' . floor($time/3600) . ' hora' . (floor($time/3600) != 1 ? 's' : '');
    if ($time < 2592000) return 'há ' . floor($time/86400) . ' dia' . (floor($time/86400) != 1 ? 's' : '');
    
    return 'há mais de um mês';
}

/**
 * Retorna ícone apropriado para cada tipo de atividade
 * 
 * @param string $type Tipo da atividade
 * @return string Nome do ícone (FontAwesome ou similar)
 */
function getActivityIcon($type) {
    $icons = [
        'new_user' => 'fas fa-user-plus',
        'new_service' => 'fas fa-briefcase',
        'new_contact' => 'fas fa-envelope',
        'new_order' => 'fas fa-shopping-cart',
        'new_complaint' => 'fas fa-exclamation-triangle',
        'unblock_appeal' => 'fas fa-unlock-alt'
    ];
    
    return $icons[$type] ?? 'fas fa-info-circle';
}

/**
 * Retorna classe CSS apropriada para cada tipo de atividade
 * 
 * @param string $type Tipo da atividade
 * @return string Nome da classe CSS
 */
function getActivityCssClass($type) {
    $classes = [
        'new_user' => 'activity-success',
        'new_service' => 'activity-info',
        'new_contact' => 'activity-primary',
        'new_order' => 'activity-success',
        'new_complaint' => 'activity-warning',
        'unblock_appeal' => 'activity-secondary'
    ];
    
    return $classes[$type] ?? 'activity-default';
}

// Endpoint simples para requisições AJAX
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'rates':
                echo json_encode(['rates' => getExchangeRates()]);
                break;
                
            case 'convert':
                $amount = floatval($_GET['amount'] ?? 0);
                $to = $_GET['to'] ?? 'EUR';
                $converted = convertCurrency($amount, $to);
                echo json_encode(['converted' => $converted]);
                break;
                
            case 'format':
                $amount = floatval($_GET['amount'] ?? 0);
                $userId = intval($_GET['user_id'] ?? 0) ?: null;
                $formatted = convertAndFormatPrice($amount, $userId);
                echo json_encode(['formatted' => $formatted]);
                break;
                
            case 'distance':
                $origin = $_GET['origin'] ?? '';
                $destination = $_GET['destination'] ?? '';
                $distance = calculateDistance($origin, $destination);
                echo json_encode(['distance' => $distance]);
                break;
                
            case 'travel_fee':
                $distance = floatval($_GET['distance'] ?? 0);
                $rate = floatval($_GET['rate'] ?? 0.50);
                $userId = intval($_GET['user_id'] ?? 0) ?: null;
                $fee = calculateTravelFee($distance, $rate, $userId);
                $formatted = convertAndFormatPrice($fee, $userId);
                echo json_encode(['fee' => $fee, 'formatted' => $formatted]);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
    exit;
}
?>
