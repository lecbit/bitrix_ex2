<?
if ($arParams["SPECIAL_DATE"] == "Y") {
    $arResult["DATA_FIRST_NEWS"] = $arResult["ITEMS"][0]["ACTIVE_FROM"];
    $this->__component->SetResultCacheKeys(array("DATA_FIRST_NEWS"));
}