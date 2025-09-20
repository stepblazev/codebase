<?
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Mail;

$response = [
    'success' => true,
    'message' => null,
    'data' => null
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['success'] = false;
    $response['message'] = 'Неверный метод запроса';

    http_response_code(400);
    echo json_encode($response);

    die();
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($phone) || empty($email)) {
    $response['success'] = false;
    $response['message'] = 'Переданы не все обязательные параметры';

    http_response_code(422);
    echo json_encode($response);

    die();
}

// отправка лида в Битрикс24
$webhookUrl = "https://domain.bitrix24.ru/rest/1234/secretkeyforbitrix/crm.lead.add.json";

$queryData = [
    'fields' => [
        'TITLE'   => 'Заявка',
        'NAME'    => $name,
        'PHONE'   => [['VALUE' => $phone, 'VALUE_TYPE' => 'WORK']],
        'EMAIL'   => [['VALUE' => $email, 'VALUE_TYPE' => 'WORK']],
        'COMMENTS' => $message,
        'ASSIGNED_BY_ID' => 1,
        'SOURCE_ID' => 'WEB',
    ],
    'params' => ['REGISTER_SONET_EVENT' => 'Y']
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $webhookUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($queryData),
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
]);

$b24result = curl_exec($ch);
curl_close($ch);

$b24result = json_decode($b24result, true);
$leadId = $b24result['result'];

// отправка письма на почту
$eventResult = CEvent::Send(
    "PROMO_FEEDBACK",
    SITE_ID,
    [
        "NAME"    => $name,
        "PHONE"   => $phone,
        "EMAIL"   => $email,
        "MESSAGE" => $message,
        "LEAD_ID" => $leadId
    ]
);

if (!empty($eventResult)) {
    $response['message'] = "Заявка успешно отправлена";
}

echo json_encode($response);
?>