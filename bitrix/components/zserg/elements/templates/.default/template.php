<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<? CJSCore::Init(array("jquery3")); ?>

<div class="elements__list" data-elements>

	<?foreach($arResult["ITEMS"] as $arItem):?>

		<?
		// echo '<pre>';
		// print_r($arItem);
		// exit;
		?>

		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<div class="elements__item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<div class="elements__info">
				<?if($arItem["NAME"]):?>
					<div class="elements__title"><?echo $arItem["NAME"]?></div>
				<?endif;?>
				<?if($arItem["PREVIEW_TEXT"]):?>
					<div class="elements__desc"><?echo $arItem["PREVIEW_TEXT"];?></div>
				<?endif;?>
				<?if($arItem["TIMESTAMP_X"]):?>
					<div class="elements__date">
						Элемент изменен - <? echo FormatDate("d M", MakeTimeStamp($arItem['TIMESTAMP_X'])) ?>
					</div>
				<?endif;?>
			</div>
		</div>
	<?endforeach;?>

	<div id="pagination">
    	<?=$arResult["NAV_STRING"]?>
	</div>
</div>