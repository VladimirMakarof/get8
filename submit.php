<?php
require_once __DIR__ . '/vendor/autoload.php';

// Функция для логирования сообщений
function logMessage($message)
{
  $logFile = 'log.txt';
  $logMessage = date("Y-m-d H:i:s") . " " . $message . "\n";
  file_put_contents($logFile, $logMessage, FILE_APPEND);
}

$client = new Google\Client();
$client->setApplicationName('Your Application Name');
$client->setScopes(Google_Service_Sheets::SPREADSHEETS);
$client->setAccessType('offline');
$client->setAuthConfig('credentials.json'); // Путь JSON-файлу с учетными данными

$service = new Google_Service_Sheets($client);
$spreadsheetId = '1ZW8p03NBTfucjBOKhYw0-7onmUhIT82K0t5gLxRC_4s'; // Идентификатор Google Таблицы

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];
  $time = $_POST['time'];
  $errors = [];

  // Проверка валидности email
  if (!preg_match('/^[a-zA-Zа-яА-Я0-9._-]+@[a-zA-Zа-яА-Я0-9.-]+\.[a-zA-Zа-яА-Я]{2,}$/', $email)) {
    $errors['email'] = "Неверный формат email.";
    logMessage("Невалидные данные: email");
  }

  // Проверка валидности телефона
  if (!preg_match('/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone)) {
    $errors['phone'] = "Неверный формат телефона.";
    logMessage("Невалидные данные: phone");
  }


  // Проверка валидности имени
  if (!preg_match('/^[а-яА-Я ]+$/u', $name)) {
    $errors['name'] = "Имя должно содержать только буквы кириллицы.";
    logMessage("Невалидные данные: name");
  }

  if (empty($errors)) {
    // Данные для записи в таблицу
    $values = [
      [$name, $phone, $email, $time]
    ];

    // Диапазон ячеек для записи
    $range = 'Лист1!A2:D2';

    // Запись данных в Google Таблицу
    $body = new Google_Service_Sheets_ValueRange([
      'values' => $values
    ]);
    $params = [
      'valueInputOption' => 'RAW'
    ];

    $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
    if ($result) {
      logMessage("Данные успешно отправлены в Google Таблицу");
      echo "Данные успешно отправлены в Google Таблицу!";
    } else {
      logMessage("Произошла ошибка при отправке данных в Google Таблицу");
      echo "Произошла ошибка при отправке данных в Google Таблицу.";
    }
  } else {
    http_response_code(400); // Устанавливаем статус ответа 400 Bad Request
    echo json_encode($errors); // Отправляем ошибки обратно в форму в формате JSON
    foreach ($errors as $key => $value) {

      logMessage("Ошибки валидации: $key: $value");
    }
  }
}
