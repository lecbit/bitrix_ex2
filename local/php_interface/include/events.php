<?
IncludeModuleLangFile(__FILE__);

// файл /bitrix/php_interface/init.php
// регистрируем обработчик
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", array("Ex2", "Ex2_50"));

class Ex2
{
    // создаем обработчик события "OnBeforeIBlockElementUpdate"
    function Ex2_50(&$arFields)
    {
        if ($arFields["IBLOCK_ID"] == ID_BLOCK_CATALOG) {
            if ($arFields["ACTIVE"] == "N") {
                $arSelect = array(
                    "ID",
                    "IBLOCK_ID",
                    "NAME",
                    "SHOW_COUNTER"
                );
                $arFilter = array(
                    "IBLOCK_ID" => ID_BLOCK_CATALOG,
                    "ID" => $arFields["ID"]
                );
                $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
                $arItems = $res->Fetch();

                if ($arItems["SHOW_COUNTER"] > MAX_COUNT) {
                    global $APPLICATION;
                    $sText = GetMessage("EX2_50_text", array("#COUNT#" => $arItems["SHOW_COUNTER"]));
                    $APPLICATION->throwException($sText);
                    return false;
                }
            }
        }
    }
}
