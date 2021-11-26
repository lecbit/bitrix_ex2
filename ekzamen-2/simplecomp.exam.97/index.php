<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент-ex2-97");
?><?$APPLICATION->IncludeComponent(
	"exam2:simplecomp.exam.97",
	"",
	Array(
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"EXAM2_PROPERTY" => "AUTHOR",
		"NEWS_IBLOCK_ID" => "1",
		"PROPERTY_UF" => "UF_AUTHOR_TYPE"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>