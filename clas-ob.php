<?php
class AutoBMW
{
    public $model;
    public $vin;
    public $color;
    public $year;
    public $price;

    public function __construct($model, $vin, $color, $year, $price)
    {
        $this->model = $model;
        $this->vin = $vin;
        $this->color = $color;
        $this->year = $year;
        $this->price = $price;
    }

    public function getDiscountPrice()
    {
        $price = $this->price;

        if ($this->year < 2018) {
            $price = $this->price * 0.8;
        }

        return $price;
    }
}
echo '<br>Авто:<br>';
$bmwX3 = new AutoBMW('bmw-X3', '79898765841681168', 'red', 2016, 10000);
echo $bmwX3->model.' - '.$bmwX3->getDiscountPrice().'<br>';

$bmwX6 = new AutoBMW('bmw-X6', '888888888889999999', 'black', 2018, 20000);
echo $bmwX6->model.' - '.$bmwX6->getDiscountPrice().'<br>';


class Tv
{
    public $diagonal;
    public $brand;
    public $price;

    public function __construct($diagonal, $brand, $price)
    {
        $this->diagonal = $diagonal;
        $this->brand = $brand;
        $this->price = $price;
    }
  
    public function getDiscountPrice()
    {
        $price = $this->price;

        if ($this->diagonal > 70) {
            $price = $this->price * 1.1;
        }

        return $price;
    }
}
echo '<br>Телевизоры:<br>';
$lcdTv = new Tv(106, 'LG', 500);
echo $lcdTv->brand.' - '.$lcdTv->getDiscountPrice().'<br>';

$plasma = new Tv(21, 'sony', 100);
echo $plasma->brand.' - '.$plasma->getDiscountPrice().'<br>';


class Pen
{
    public $color;
    public $maker;
    public $price;
    public $size;
    public $available;

    public function __construct($color, $maker, $price)
    {
        $this->color = $color;
        $this->maker = $maker;
        $this->price = $price;
    }
  
    public function getExistence()
    {
        
        if ($this->color === 'red') {
            $this->available = 'Нет в наличии';
        } else {
            $this->available = 'В наличии';
        }
        return $this->available;
        
    }
}

echo '<br>Шариковые ручки:<br>';
$pen1 = new Pen('red', 'Россия', 500);
echo $pen1->maker.' - '.$pen1->price.' - '.$pen1->getExistence().'<br>';

$pen2 = new Pen('grey', 'Китай', 100);
echo $pen2->maker.' - '.$pen2->price.' - '.$pen2->getExistence().'<br>';


class Duck
{
    public $color;
    public $ageMonth;
    public $sex;
    public $food;

    public function __construct($color, $ageMonth, $sex)
    {
        $this->color = $color;
        $this->ageMonth = $ageMonth;
        $this->sex = $sex;
    }
  
    public function getFoodMode()
    {
        $sex = $this->sex;

        if ($this->ageMonth < 5) {
            $this->food = 'Кормить чаще';
        } else {
            $this->food = 'Стандартный режим питания';
        }

        return $this->food;
    }
}

echo '<br>Утки:<br>';
$duck1 = new Duck('dark', '3', 'female');
echo 'Возраст - '.$duck1->ageMonth.' мес. - '.$duck1->getFoodMode().'<br>';

$duck2 = new Duck('light', '7', 'male');
echo 'Возраст - '.$duck2->ageMonth.' мес. - '.$duck2->getFoodMode().'<br>';


class Product
{
    public $name;
    public $category;
    public $brand;
    public $manufacturer;
    public $supplier;

    public function __construct($name, $category, $brand, $manufacturer, $supplier)
    {
        $this->name = $name;
        $this->category = $category;
        $this->brand = $brand;
        $this->manufacturer = $manufacturer;
        $this->supplier = $supplier;
    }
}
echo '<br>Товар:<br>';
$tv = new Product('Телевизор', 'Бытовая техника', 'sony', 'Китай', 'ООО ТВ-Плюс');
$clothes = new Product('Куртка', 'Одежда', 'adidas', 'Китай', 'ООО Одежда оптом');

$products = [$tv, $clothes];

echo '<pre>';
print_r($products);
echo '<pre>';


?>