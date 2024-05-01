<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aniversários dos Colaboradores</title>
</head>
<body>
<?php

// Classe para utilização do API do Sheets
class GoogleSheetsAPI {
    private $api_url;

    public function __construct($api_url) {
        $this->api_url = $api_url;
    }

    public function fetchData() {
        $file = file_get_contents($this->api_url);
        return json_decode($file);
    }
}

// Classe do Colaborador

class Employee {
    private $name;
    private $birthDate;
    private $hireDate;

    public function __construct($name, $birthDate, $hireDate) {
        $this->name = $name;
        $this->birthDate = $birthDate;
        $this->hireDate = $hireDate;
    }

    public function getName() {
        return $this->name;
    }

    public function getBirthDate() {
        return $this->birthDate;
    }

    public function getHireDate() {
        return $this->hireDate;
    }

    public function calculateAge(DateTime $currentDate) {
        return $currentDate->diff(DateTime::createFromFormat('d/m/Y', $this->birthDate))->y;
    }

    public function calculateYearsOfEmployment(DateTime $currentDate) {
        return $currentDate->diff(DateTime::createFromFormat('d/m/Y', $this->hireDate))->y;
    }
}


$currentDate = new DateTime();
$api_url = "https://sheetdb.io/api/v1/e82thfp1i4kq3";

// Instanciando a classe GoogleSheetsAPI
$google_sheets_api = new GoogleSheetsAPI($api_url);

// Buscando os dados da planilha
$colaboradores = $google_sheets_api->fetchData()->values;

// Iteração sobre os colaboradores
foreach ($colaboradores as $row) {
    if (isset($row[1]) && !isset($row[3])) {
        // Criando objeto Employee com os dados do colaborador
        $employee = new Employee($row[0], $row[1], $row[2]);

        // Calculando idade e anos de empresa
        $age = $employee->calculateAge($currentDate);
        $yearsOfEmployment = $employee->calculateYearsOfEmployment($currentDate);

        // Convertendo datas para objetos DateTime
        $birthDate = DateTime::createFromFormat('d/m/Y', $employee->getBirthDate());
        $birthDate->setDate($currentDate->format('Y'), $birthDate->format('m'), $birthDate->format('d'));

        $hireDate = DateTime::createFromFormat('d/m/Y', $employee->getHireDate());
        $hireDate->setDate($currentDate->format('Y'), $hireDate->format('m'), $hireDate->format('d'));

        // Calculando diferenças de datas
        $ageDifference = $currentDate->diff($birthDate);
        $employmentDifference = $currentDate->diff($hireDate);


        // Verificando se é aniversário do colaborador ou de empresa
        if ($ageDifference->format('%R%a') == 0) {
            echo 'Hoje é o aniversário de ' . $employee->getName() . '! Ele(a) está completando ' . $age . ' anos.<br>';
        }

        if ($employmentDifference->format('%R%a') == 0) {
            echo 'Hoje é o aniversário de empresa de ' . $employee->getName() . '! Ele(a) está completando ' . $yearsOfEmployment . ' anos de empresa.<br>';
        }
    }
}
?>
</body>
</html>
