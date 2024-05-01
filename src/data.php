<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aniversários dos Colaboradores</title>
</head>
<body>
<?php
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
$google_sheets_api = new GoogleSheetsAPI($api_url);
$colaboradores = $google_sheets_api->fetchData()->values;

foreach ($colaboradores as $row) {
    if (isset($row[1]) && !isset($row[3])) {
        $employee = new Employee($row[0], $row[1], $row[2]);
        $age = $employee->calculateAge($currentDate);
        $yearsOfEmployment = $employee->calculateYearsOfEmployment($currentDate);

        $birthDate = DateTime::createFromFormat('d/m/Y', $employee->getBirthDate());
        $birthDate->setDate($currentDate->format('Y'), $birthDate->format('m'), $birthDate->format('d'));

        $hireDate = DateTime::createFromFormat('d/m/Y', $employee->getHireDate());
        $hireDate->setDate($currentDate->format('Y'), $hireDate->format('m'), $hireDate->format('d'));

        $ageDifference = $currentDate->diff($birthDate);
        $employmentDifference = $currentDate->diff($hireDate);

        if ($ageDifference->format('%R%a') < 0) {
            echo 'The birthday of ' . $employee->getName() . ' has already passed ' . abs($ageDifference->format('%a')) . ' days ago. He/she turned ' . $age . ' years old.<br>';
        } elseif ($ageDifference->format('%R%a') == 0) {
            echo 'Today is the birthday of ' . $employee->getName() . '! He/she is turning ' . $age . ' years old.<br>';
        } else {
            $age = $age + 1;
            echo 'The birthday of ' . $employee->getName() . ' is in ' . $ageDifference->format('%a') . ' days. He/she will be turning ' . $age . ' years old.<br>';
        }

        if ($employmentDifference->format('%R%a') < 0) {
            echo 'The employment anniversary of ' . $employee->getName() . ' has already passed. He/she has been with the company for ' . $yearsOfEmployment . ' years.<br>';
        } elseif ($employmentDifference->format('%R%a') == 0) {
            echo 'Today is the employment anniversary of ' . $employee->getName() . '! He/she has been with the company for ' . $yearsOfEmployment .  ' years.<br>';
        } else {
            $yearsOfEmployment = $yearsOfEmployment + 1;
            echo 'The employment anniversary of ' . $employee->getName() . ' is later this year! He/she will be celebrating ' . $yearsOfEmployment . ' years with the company.<br>';
        }
    }
}
?>
</body>
</html>
