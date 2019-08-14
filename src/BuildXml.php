<?php

namespace ItPoet\CommerceMl;

class BuildXml
{    
    private $groupsXml = '';
    public $groups = [];
    public $stores = [];
    public $imgsDir = '';
    public $imgsDirFrom = '';

    public function makeXml($products, $offers = false)
    {
    	if ($offers) {
        	return $this->getHead() . "\n</Классификатор>" .
        	'<ПакетПредложений СодержитТолькоИзменения="false">' . $this->getRequis(false, true) . 
        	$this->getOffers($products) . '</ПакетПредложений></КоммерческаяИнформация>';
    	} else {
    		if (!empty($this->imgsDir)) {
    			self::deleteDir($this->imgsDir);
    			mkdir($this->imgsDir);
    			mkdir($this->imgsDir . '/catalog');
    			mkdir($this->imgsDir . '/catalog/import_files');
    		}
    		return $this->getHead() . $this->getGroups() . $this->getProps() .
        	'</Классификатор>' . $this->getCatalog($products) . '</КоммерческаяИнформация>';
    	}
    }

    private function exportImg($imgId, $ext, $productId)
    {    	
    	$origImg = $this->imgsDirFrom . '/orig_' . $productId . '_' . $imgId . '.' . $ext;
    	$img = $imgId . '.' . $ext;
    	if (file_exists($origImg)) {
	    	copy($origImg, $this->imgsDir . '/catalog/import_files/' . $img);
    	}
    	return $img;

    }

    private function getHead()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
    <КоммерческаяИнформация xmlns="urn:1C.ru:commerceml_2" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ВерсияСхемы="2.07" ДатаФормирования="' . date('Y-m-d') . 'T' . date('H:i:s') . '">
	<Классификатор>' . "\n" . $this->getRequis(false);
    }

    private function getRequis($catalog, $offers = false)
    {
    	if ($offers) {
    		return '<Ид>0922c466-15b2-49ed-a7b1-6a3f9924221e#</Ид>
			<Наименование>БК экспорт</Наименование>
			<ИдКаталога>0922c466-15b2-49ed-a7b1-6a3f9924221e</ИдКаталога>
			<ИдКлассификатора>0922c466-15b2-49ed-a7b1-6a3f9924221e</ИдКлассификатора>'. $this->getOwner()
			. $this->getPriceTypes() . $this->getStores();
		} else {
			return '<Ид>0922c466-15b2-49ed-a7b1-6a3f9924221e</Ид>' .
	        ($catalog ? '<ИдКлассификатора>0922c466-15b2-49ed-a7b1-6a3f9924221e</ИдКлассификатора>' : '') .
			'<Наименование>БК экспорт</Наименование>
			' . $this->getOwner();
		}
    }

    private function getOwner()
    {
        return '
		<Владелец>
			<Ид>337174f8-5342-11e2-a52f-000c29cd4212</Ид>
			<Наименование>БК</Наименование>
			<ОфициальноеНаименование>БК</ОфициальноеНаименование>
			<КПП>100000001</КПП>
			<ОКПО/>
		</Владелец>';
    }

    private function getOffers($products)
    {
        $offers = '';
        foreach ($products as $product) {
        	$product = self::arrToObj($product);
        	$offers .= '
        	<Предложение>
				<Ид>aaaaaa02-' . $product->id .'</Ид>
				<Артикул>' . htmlspecialchars($product->article, ENT_XML1, 'UTF-8') . '</Артикул>
				<Наименование>' . htmlspecialchars($product->name1, ENT_XML1, 'UTF-8') . '</Наименование>
				<БазоваяЕдиница Код="796 " НаименованиеПолное="Штука" МеждународноеСокращение="PCE">
					<Пересчет>
						<Единица>796</Единица>
						<Коэффициент>1</Коэффициент>
					</Пересчет>
				</БазоваяЕдиница>
				<Цены>
					<Цена>
						<Представление> ' . self::twoDigitsFloat($product->price) . ' RUB за PCE</Представление>
						<ИдТипаЦены>9f0eb2e7-0b9b-11e6-80eb-000c290473e7</ИдТипаЦены>
						<ЦенаЗаЕдиницу>' . self::twoDigitsFloat($product->price) . '</ЦенаЗаЕдиницу>
						<Валюта>RUB</Валюта>
						<Единица>PCE</Единица>
						<Коэффициент>1</Коэффициент>
					</Цена>
					<Цена>
						<Представление> ' . self::twoDigitsFloat($product->price) . ' RUB за PCE</Представление>
						<ИдТипаЦены>1c724522-71bc-11e2-9c98-000c2954e91f</ИдТипаЦены>
						<ЦенаЗаЕдиницу>' . self::twoDigitsFloat($product->price) . '</ЦенаЗаЕдиницу>
						<Валюта>RUB</Валюта>
						<Единица>PCE</Единица>
						<Коэффициент>1</Коэффициент>
					</Цена>
				</Цены>
				<Количество>' . (int)$product->count_goods . '</Количество>
			</Предложение>
        	';
        }
		
		return '<Предложения>' . $offers .'</Предложения>';
    }

    private function getPriceTypes()
    {
        return '
		<ТипыЦен>
			<ТипЦены>
				<Ид>9f0eb2e7-0b9b-11e6-80eb-000c290473e7</Ид>
				<Наименование>ДЛЯ САЙТА_Интернет-магазин</Наименование>
				<Валюта>RUB</Валюта>
				<Налог>
					<Наименование>НДС</Наименование>
					<УчтеноВСумме>true</УчтеноВСумме>
					<Акциз>false</Акциз>
				</Налог>
			</ТипЦены>
			<ТипЦены>
				<Ид>1c724522-71bc-11e2-9c98-000c2954e91f</Ид>
				<Наименование>Интернет-магазин</Наименование>
				<Валюта>RUB</Валюта>
				<Налог>
					<Наименование>НДС</Наименование>
					<УчтеноВСумме>true</УчтеноВСумме>
					<Акциз>false</Акциз>
				</Налог>
			</ТипЦены>
		</ТипыЦен>';
    }

    private function getStores()
    {
    	$stores = '';
    	foreach ($this->stores as $store) {
    		$store = self::arrToObj($store);
    		$stores .= '
    		<Склад>
				<Ид>aaaaaa04-' . $store->id . '</Ид>
				<Наименование>' . htmlspecialchars($store->name, ENT_XML1, 'UTF-8') . '</Наименование>
			</Склад>';
    	}
        return '<Склады>'
        . $stores .			
		'</Склады>';
    }

    private function getGroups($id = 0)
    {
        if ($id == 0)
            $this->groupsXml = '';
        if (!isset($this->groups[$id]) or !count($this->groups[$id]))
            return '';
        $this->groupsXml .=  '<Группы>';
        foreach ($this->groups[$id] as $cat) {
        	$cat = self::arrToObj($cat);
            $this->groupsXml .=
                '<Группа>
				    <Ид>aaaaaa01-' . $cat->id . '</Ид>' .
                    '<Наименование>' . htmlspecialchars($cat->name1, ENT_XML1, 'UTF-8') . '</Наименование>';
            $this->getGroups($cat->id);
            $this->groupsXml .= '</Группа>';
        }
        $this->groupsXml .= '</Группы>';
        if ($id == 0)
            return  $this->groupsXml ;
    }

    private function getProps()
    {
        return '<Свойства>	
			<Свойство>
				<Ид>aaaaaa03-0001</Ид>
				<Наименование>Код</Наименование>
				<ТипЗначений>Строка</ТипЗначений>
			</Свойство>				
			<Свойство>
				<Ид>aaaaaa03-0002</Ид>
				<Наименование>Вес, кг</Наименование>
				<ТипЗначений>Строка</ТипЗначений>
			</Свойство>		
			<Свойство>
				<Ид>aaaaaa03-0003</Ид>
				<Наименование>Страна</Наименование>
				<ТипЗначений>Строка</ТипЗначений>
			</Свойство>	
			<Свойство>
				<Ид>aaaaaa03-0004</Ид>
				<Наименование>Объём, куб. м.</Наименование>
				<ТипЗначений>Строка</ТипЗначений>
			</Свойство>
			<Свойство>
				<Ид>aaaaaa03-0005</Ид>
				<Наименование>Срок гарантии</Наименование>
				<ТипЗначений>Строка</ТипЗначений>
			</Свойство>
		</Свойства>';
    }

    private function getCatalog($products)
    {
        $xml = '<Каталог СодержитТолькоИзменения="false">' . $this->getRequis(true) .
            '<Товары>';

        foreach ($products as $product) {
        	$product = self::arrToObj($product);
        	$img = $this->exportImg($product->img_id, $product->ext, $product->id);
            $xml .= '
            <Товар>
                <Ид>aaaaaa02-' . $product->id . '</Ид>
				<Артикул>' . htmlspecialchars($product->article, ENT_XML1, 'UTF-8') . '</Артикул>
				<Наименование>' . htmlspecialchars($product->name1, ENT_XML1, 'UTF-8') . '</Наименование>
				<БазоваяЕдиница Код="796 " НаименованиеПолное="Штука" МеждународноеСокращение="PCE">
					<Пересчет>
						<Единица>796</Единица>
						<Коэффициент>1</Коэффициент>
					</Пересчет>
				</БазоваяЕдиница>
				<Группы>
					<Ид>aaaaaa01-' . $product->cat_id . '</Ид>
				</Группы>
				<Описание>' . htmlspecialchars($product->opisanie, ENT_XML1, 'UTF-8') . '</Описание>' .
				($img != '.' ? ('<Картинка>import_files/' . $img . '</Картинка>') : '') .
				'<Изготовитель>
					<Ид>d8cc25a3-531e-11e2-a52f-000c29cd4212</Ид>
					<Наименование>PRORAB</Наименование>
				</Изготовитель>
				<ЗначенияСвойств>
					<ЗначенияСвойства>
						<Ид>aaaaaa03-0001</Ид>
						<Значение>' . htmlspecialchars($product->tov_code, ENT_XML1, 'UTF-8') . '</Значение>
					</ЗначенияСвойства>	
					<ЗначенияСвойства>
						<Ид>aaaaaa03-0002</Ид>
						<Значение>' . htmlspecialchars($product->weight, ENT_XML1, 'UTF-8') . '</Значение>
					</ЗначенияСвойства>
					<ЗначенияСвойства>
						<Ид>aaaaaa03-0003</Ид>
						<Значение>' . htmlspecialchars($product->country, ENT_XML1, 'UTF-8') . '</Значение>
					</ЗначенияСвойства>
					<ЗначенияСвойства>
						<Ид>aaaaaa03-0004</Ид>
						<Значение>' . htmlspecialchars($product->volume, ENT_XML1, 'UTF-8') . '</Значение>
					</ЗначенияСвойства>
					<ЗначенияСвойства>
						<Ид>aaaaaa03-0005</Ид>
						<Значение>' . htmlspecialchars($product->garant_time, ENT_XML1, 'UTF-8') . '</Значение>
					</ЗначенияСвойства>
				</ЗначенияСвойств>	
            </Товар>';
        }

        $xml .= '</Товары>
        </Каталог>';
        return $xml;
    }

    public function getPath()
    {
    	return __DIR__;
    }

    private static function twoDigitsFloat($number)
    {
        return number_format((float)$number, 2, '.', '');
    }

    private static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            return;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    private static function arrToObj($var)
    {
    	if (is_array($var))
    		return (object) $var;
    	return $var;
    }

}