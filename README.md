# commerce-ml
CommerceML XML builder

Пример использования:

$cml = new \ItPoet\CommerceMl\BuildXml();

$cml->groups = require ($cml->getPath() . '/../examples/groups.php');

$cml->stores = require ($cml->getPath() . '/../examples/stores.php');

$products = require ($cml->getPath() . '/../examples/products.php');

$offers = false; // Формирование каталога товаров

$offers = true; // Формирование списка предложений (остатки и цены)

echo $cml->makeXml($products, $offers);
