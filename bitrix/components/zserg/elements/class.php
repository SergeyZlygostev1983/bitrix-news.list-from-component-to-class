<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\Date;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class CZsergElements extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        if(!isset($arParams["CACHE_TIME"])) {
            $arParams["CACHE_TIME"] = 36000000;
        }

        $arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);

        if(!is_array($arParams["FIELD_CODE"])) {
            $arParams["FIELD_CODE"] = array();
        }
        foreach($arParams["FIELD_CODE"] as $key=>$val) {
            if(!$val) {
                unset($arParams["FIELD_CODE"][$key]);
            }
        }

        if(!is_array($arParams["PROPERTY_CODE"])) {
            $arParams["PROPERTY_CODE"] = array();
        }
        foreach($arParams["PROPERTY_CODE"] as $key=>$val) {
            if($val==="") {
                unset($arParams["PROPERTY_CODE"][$key]);
            }
        }

        $arParams['ELEMENT_COUNT'] = intval($arParams['ELEMENT_COUNT']);
        if($arParams['ELEMENT_COUNT'] <= 0) {
            $arParams['ELEMENT_COUNT'] = 3;
        }

        return $arParams;
    }

    public function executeComponent()
    {
        try {
            $this->checkModules();
            $this->getResult();
            $this->includeComponentTemplate();
        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }

    protected function checkModules()
    {
        if (!Loader::includeModule('iblock')) {
            throw new SystemException(Loc::getMessage('CPS_MODULE_NOT_INSTALLED', array('#NAME#' => 'iblock')));
        }
    }

    protected function prepareDate(&$arItem) {
        $DateFormat = $this->arParams["ACTIVE_DATE_FORMAT"];

        if (strlen($arItem["DATE_ACTIVE_FROM"]) > 0)
            $arItem["DATE_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($DateFormat, MakeTimeStamp($arItem["DATE_ACTIVE_FROM"], CSite::GetDateFormat()));
        else
            $arItem["DATE_ACTIVE_FROM"] = "";
        if (strlen($arItem["DATE_CREATE"]) > 0)
            $arItem["DATE_CREATE"] = CIBlockFormatProperties::DateFormat($DateFormat, MakeTimeStamp($arItem["DATE_CREATE"], CSite::GetDateFormat()));
        else
            $arItem["DATE_CREATE"] = "";
        if (strlen($arItem["ACTIVE_FROM"]) > 0)
            $arItem["ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($DateFormat, MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
        else
            $arItem["ACTIVE_FROM"] = "";
        if (strlen($arItem["SHOW_COUNTER_START"]) > 0)
            $arItem["SHOW_COUNTER_START"] = CIBlockFormatProperties::DateFormat($DateFormat, MakeTimeStamp($arItem["SHOW_COUNTER_START"], CSite::GetDateFormat()));
        else
            $arItem["SHOW_COUNTER_START"] = "";
        if (strlen($arItem["TIMESTAMP_X"]) > 0)
            $arItem["TIMESTAMP_X"] = CIBlockFormatProperties::DateFormat($DateFormat, MakeTimeStamp($arItem["TIMESTAMP_X"], CSite::GetDateFormat()));
        else
            $arItem["TIMESTAMP_X"] = "";
    }

    protected function getResult()
    {
        if ($this->errors)
            throw new SystemException(current($this->errors));

        $arParams = $this->arParams;

        $arNavParams = null;
        $arNavigation = false;
        if ($arParams['DISPLAY_TOP_PAGER'] || $arParams['DISPLAY_BOTTOM_PAGER']) {
            $arNavParams = array(
                'nPageSize' => $arParams['ELEMENT_COUNT'], // количество элементов на странице
                'bShowAll' => $arParams['PAGER_SHOW_ALL'], // показывать ссылку «Все элементы»?
            );
            $arNavigation = CDBResult::GetNavParams($arNavParams);
        }

        $additionalCacheID = array($arParams['CACHE_GROUPS'] ? $USER->GetGroups() : false, $arNavigation);
        if ($this->startResultCache($arParams['CACHE_TIME'], $additionalCacheID)) {
            //SELECT
            $arSelect = array_merge($arParams["FIELD_CODE"], array(
                "IBLOCK_ID",
                "ID",
                "NAME",
                "TIMESTAMP_X",
            ));
            if ($arParams['GET_EXTERNAL_ID'] == 'Y') {
                $arSelect[] = 'EXTERNAL_ID';
            }
            if ($arParams['GET_IBLOCK_SECTION_ID'] == 'Y') {
                $arSelect[] = 'IBLOCK_SECTION_ID';
            }
            foreach ($arParams["PROPERTY_CODE"] as $prop_name) {
                $arSelect[] = "PROPERTY_" . $prop_name;
            }

            //WHERE
            $arFilter = array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "IBLOCK_LID" => SITE_ID,
                "ACTIVE" => "Y",
            );

            //ORDER BY
            $arSort = array(
                $arParams["SORT_BY1"] => $arParams["SORT_ORDER1"]
            );
            if (!array_key_exists("ID", $arSort))
                $arSort["ID"] = "DESC";

            //GETLIST
            $rsElement = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
            if (!$rsElement) {
                $this->abortResultCache();
            }

            $rsElement->SetUrlTemplates($arParams["DETAIL_URL"], "", $arParams["IBLOCK_URL"]);

            while ($obElement = $rsElement->GetNextElement()) {
                $arItem = $obElement->GetFields();

                $this->prepareDate($arItem);

                $arResult["ITEMS"][] = $arItem;
            }

            $arResult['NAV_STRING'] = $rsElement->GetPageNavString(
                $arParams['PAGER_TITLE'],
                $arParams['PAGER_TEMPLATE'],
                $arParams['PAGER_SHOW_ALWAYS'],
                $this
            );

            $this->arResult = $arResult;
        }
    }
}