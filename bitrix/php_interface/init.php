<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Web\HttpClient;

function importJsonData($id = null, $elCnt = 0) {
    global $USER;

    Loader::includeModule('iblock');

    $url = 'https://jsonplaceholder.typicode.com/posts';
    $iBlockId = intval($id);
    $limit = intval($elCnt);

    $httpClient = new HttpClient($options);
    $httpClient->get($url);
    $res = $httpClient->getResult();

    try {
        $arRes = json_decode($res, true);
    } catch (\Exception $e){
        echo 'Ошибка: ',  $e->getMessage(), '\n';
    }

    if($iBlockId !== null && $limit !== 0) {

        foreach ($arRes as $cnt => $arItem) {
            if($cnt > $limit - 1) break;

            $el = new CIBlockElement;

            $PROPS = array();
            $PROPS['USER_ID'] = $arItem['userId'];

            $arPosts = Array(
                'MODIFIED_BY' => $USER->GetID(),
                'IBLOCK_SECTION_ID' => false,
                'IBLOCK_ID' => $iBlockId,
                'PROPERTY_VALUES' => $PROPS,
                'NAME' => $arItem['title'],
                'PREVIEW_TEXT' => $arItem['body'],
                'DETAIL_TEXT' => '',
                'ACTIVE' => 'Y',
            );

            if($post_ID = $el->Add($arPosts)) {
                echo 'Новый элемент с ID ' . $post_ID . ' добавлен!<br>';
            } else {
                echo 'Ошибка: ' . $el->LAST_ERROR;
            }
        }

    } else {
        echo 'Введите ID инфоблока и количество элементов для вставки';
    }
}