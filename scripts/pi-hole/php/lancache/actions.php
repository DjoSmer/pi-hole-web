<?php
/**
 * DjoSmer, 2024.02
 */

require_once '../password.php';
require_once 'CacheDomains.php';

if (!$auth) {
    exit('Not authorized');
}

$cacheDomains = new Lancache\CacheDomains();

function updateCacheDomains(Lancache\CacheDomains $cacheDomains) {
    ob_end_flush();
    ini_set('output_buffering', '0');
    ob_implicit_flush(true);
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');

    $domains = isset($_GET['domains']) ? explode(',', $_GET['domains']) : [];
    $ip = $_GET['ip'] ?? '';

    $cacheDomains->updateCacheDomains($domains, $ip);
}


switch ($_REQUEST['action']) {
    case 'get':
        echo json_encode($cacheDomains->getCacheDomains($_POST['url']));
        break;

    case 'update':
        updateCacheDomains($cacheDomains);
        break;

    default:
        exit('Wrong action');
}
