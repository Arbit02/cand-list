<?php

function GetCand($name, $birth)
{
    $url = 'http://info.vybory.pro/poisk-kandidata?label_advanced_search=0&search_fio='.$name.'&reg_el=1000&year_el=9999&dat1_el=01-01-2003&dat2_el=31-12-2026&cck_storage_location=free&lev_el=1%2C2%2C3%2C4&w_type_el=1%2C2%2C3&work__party_select=1%2C2%2C3%2C4%2C5%2C6%2C7%2C8%2C9%2C10%2C11%2C12%2C13%2C1000&work_status=0%2C1%2C2%2C3&search=list_user&task=search';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $html = curl_exec($ch);
    $dom = new DOMDocument;
// Игнорируем ошибки парсинга :)
    libxml_use_internal_errors(true);
// Загружаем HTML
    $dom->loadHTML($html);
// Очищаем Варнинги :)
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);
// Ищем все строки таблицы
    $rows = $xpath->query('//table[@class="table table_list_users"]/tbody/tr');
    $candidates = [];
    foreach ($rows as $row) {
        $fio = $xpath->query('.//div[@class="cck-clrfix lu_fio"]/a', $row);
        $date_birth = $xpath->query('.//div[@class="cck-clrfix lu_date_birth"]', $row);
        $date_elect = $xpath->query('.//div[@class="cck-clrfix lu_dat_elect"]', $row);
        $name_election = $xpath->query('.//div[@class="cck-clrfix lu_name_election"]/a', $row);
        $region = $xpath->query('.//div[@class="cck-clrfix lu_name_region_orig"]', $row);
        $level_election = $xpath->query('.//div[@class="cck-clrfix lu_level_election"]', $row);
        $party = $xpath->query('.//div[@class="cck-clrfix lu_party"]', $row);
        $status = $xpath->query('.//div[@class="cck-clrfix lu_status_activ"]', $row);
        $place_work = $xpath->query('.//div[@class="cck-clrfix lu_place_work"]', $row);
        $position = $xpath->query('.//div[@class="cck-clrfix lu_position"]', $row);
        $candidates[] = [
            'fio' => $fio->length > 0 ? trim($fio->item(0)->nodeValue) : null,
            'date_birth' => $date_birth->length > 0 ? trim($date_birth->item(0)->nodeValue) : null,
            'date_elect' => $date_elect->length > 0 ? trim($date_elect->item(0)->nodeValue) : null,
            'name_election' => $name_election->length > 0 ? trim($name_election->item(0)->nodeValue) : null,
            'region' => $region->length > 0 ? trim($region->item(0)->nodeValue) : null,
            'level_election' => $level_election->length > 0 ? trim($level_election->item(0)->nodeValue) : null,
            'party' => $party->length > 0 ? trim($party->item(0)->nodeValue) : null,
            'status' => $status->length > 0 ? trim($status->item(0)->nodeValue) : null,
            'place_work' => $place_work->length > 0 ? trim($place_work->item(0)->nodeValue) : null,
            'position' => $position->length > 0 ? trim($position->item(0)->nodeValue) : null,
        ];
    }
    curl_close($ch);
// Выводим массив кандидатов
    foreach ($candidates as $candidate) {
        if ($candidate['date_birth'] == $birth) {
            return $candidate;
        }
    }
    return null;
}
$name = ''; // имя
$name = str_replace(' ', '%20', $name);
$birth = ''; // ДР
print_r(GetCand($name, $birth));
// URL с параметрами
?>
