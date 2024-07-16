<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

	<?if($arResult["NavPageCount"] > 1):?>
	    <?if ($arResult["NavPageNomer"]+1 <= $arResult["nEndPage"]):?>
	        <?
	        $plus = $arResult["NavPageNomer"]+1;
	        $url = $arResult["sUrlPathParams"] . "PAGEN_".$arResult["NavNum"]."=".$plus;
	        ?>
	        <div class="load-more-items" data-url="<?=$url?>">Еще</div>
	    <?endif?>
	<?endif?>