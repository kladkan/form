<?php
/*
$products = [
    ['name' => 'Galaxy', 'category' => 'Смартфон', 'price' => 200],  // вместо этого массива создаем класс и описываем его.
    ['name' => 'iphone', 'category' => 'Смартфон', 'price' => 400],
];
*/

class Product
{
    public $name;
    public $category; //конструкция шаблон из которого будем создавать объекты
    public $price;
    //создадим функцию, которая будет возвращать цену товара
    public function getDiscountPrice()
    {
        $price = $this->price;

        if ($this->$category === 'Смартфон') { //если категория равна Смартфон
            $price = $this->price * 0.9; //то же самое что $price = $price - $price * 0.1
        }

        return $price;
    }
}

$galaxy = new Product(); //объект на основе класса
//присваиваем значения свойствам объекта
$galaxy->name = 'Galaxy';
$galaxy->category = 'Смартфон';
$galaxy->price = 200;
echo $galaxy->getDiscountPrice();

$iphone = new Product();
$iphone->name = 'Iphone';
$iphone->category = 'Смартфон';
$iphone->price = 300;
echo $iphone->getDiscountPrice();

$products = [$galaxy, $iphone];

?>