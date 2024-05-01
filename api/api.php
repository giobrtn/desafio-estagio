<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

// Setup google sheets client API
$client = new Client();
$client->setApplicationName('Aniversário dos Colaboradores');
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);
$client->setAuthConfig('./projeto-carefy-a62be44524fc.json');
$service = new Sheets($client);

$spreadsheetId = '1-fWXw-8tuKHiDdaSmNIUnNcRrcah2D8Bw_zaME0HxpM';
$range = '202402 - DADOS!A1:D20';

$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

// Processar dados
if (!empty($values)) {
    foreach ($values as $row) {
        $dataNascimento = DateTime::createFromFormat('d/m/Y', $row[1]);
        $dataEntrada = DateTime::createFromFormat('d/m/Y', $row[2]);
        $dataSaida = isset($row[3]) ? DateTime::createFromFormat('d/m/Y', $row[3]) : null;
        
        // Verifica se o colaborador está na empresa e é o aniversário de hoje ou aniversário na empresa
        if ($dataSaida === null && ($dataNascimento->format('d/m') === date('d/m') || $dataEntrada->format('d/m') === date('d/m'))) {
            if ($dataNascimento->format('d/m') === date('d/m')) {
                echo "{$row[0]} está fazendo aniversário hoje!<br>";
            } elseif ($dataEntrada->format('d/m') === date('d/m')) {
                echo "{$row[0]} está comemorando aniversário na empresa hoje!<br>";
            }
        }
    }
} else {
    echo 'Nenhum dado encontrado.\n';
}

?>