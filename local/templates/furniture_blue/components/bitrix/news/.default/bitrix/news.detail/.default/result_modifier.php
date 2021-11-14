<?
if (!empty($arParams["ID_BLOCK_CANONICAL"])) {
    $arSelect = array(
        "ID",
        "IBLOCK_ID",
        "NAME",
        "PROPERTY_NEW"
    );
    $arFilter = array(
        "IBLOCK_ID" => $arParams["ID_BLOCK_CANONICAL"],
        "PROPERTY_NEW" => $arResult["ID"],
        "ACTIVE" => "Y"
    );
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arResult["CANONICAL_LINK"] = $arFields["NAME"];
        $this->__component->SetResultCacheKeys(array("CANONICAL_LINK"));
    }
}
