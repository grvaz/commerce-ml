# commerce-ml
CommerceML builder

https://youtu.be/6m4P7flSys0 (осторожно, юмор)

Пример использования:

$cml = new \ItPoet\CommerceMl\BuildXml();

$cml->groups = require ($cml->getPath() . '/../examples/groups.php');

$cml->stores = require ($cml->getPath() . '/../examples/stores.php');

$products = require ($cml->getPath() . '/../examples/products.php');

$offers = false; // Формирование каталога товаров

$offers = true; // Формирование списка предложений (остатки и цены)

echo $cml->makeXml($products, $offers);
