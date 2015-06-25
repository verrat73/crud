<?php
//Для создания массива элементов разных классов и обращения к ним как к элементам одного класса
abstract class AbstractQuery{
    function GetQueryString(){}
}
//Наследование от абстрактного класса(метод SELECT)
class SelectQuery extends AbstractQuery
{
    //Массив полей над которыми выполняется запрос SELECT
    private $fields = array();

    public function SetQueryFields($fArray)
    {
        $this->fields = $fArray;
        return $this;
    }
    //Установка названия таблицы запроса SELECT
    private $table = '';

    public function SetTable($tName)
    {
        $this->table = $tName;
        return $this;
    }
    //Установка блока условий WHERE
    private $where = '';

    public function SetWhere($whereExpression){
        $this->where=$whereExpression;
        return $this;
    }
    //Установка блока ORDER BY(хранит массив объектов сортировки класса OrderByItem
    private $orderBy = array();

    public function SetOrderBy($orderByExpressions){
        $this->orderBy=$orderByExpressions;
        return $this;
    }
    //Установка блока LIMIT
    private $offset=0;
    private $take=0;

    public function SetLimit($offset, $take){
        $this->offset=$offset;
        $this->take=$take;
        return $this;
    }
    //Функция формирования списка полей
    private function getFieldsString()
    {
        if (count($this->fields) == 0) {
            return '*';
        } else {
            return join(',', $this->fields);
        }
    }
    //WHERE
    private function getWhereBlock(){
        return $this->where;
    }
    //Функция формирования списка полей сортировки ORDER BY(TODO: добавить проверку на количество элементов ORDER BY)
    private function getOrderByBlock(){
        return ' ORDER BY '.join(',', $this->orderBy);
    }
    //Функция формирования блока LIMIT
    private function getLimitBlock(){
        if ($this->take <=0 || $this->offset<0){
            return '';
        }
        return ' LIMIT '.$this->offset.','.$this->take;
    }
    //Функция формирования общего SQL запроса
    public function GetQueryString()
    {
        return 'SELECT ' . $this->getFieldsString() . ' FROM ' . $this->table.$this->getWhereBlock().$this->getOrderByBlock().$this->getLimitBlock();
    }
}

//Класс объектов сортировки ORDER BY
class OrderByItem{
    private $filedName = '';
    private $direction = '';
//Инициализация объектов класса
    public function __construct($fName, $sortDirection){
        $this->direction=$sortDirection;
        $this->filedName=$fName;
    }
//Функция преобразования объекта в строковое представление для работы функций join и т.д
    public function __toString(){
        if($this->filedName == '' || $this->direction=='' ){
            return '';
        }
        return $this->filedName.' '.$this->direction;
    }
}


$sl = new SelectQuery();
$idDescSorting = new OrderByItem('id','desc');
$nameAscSorting = new OrderByItem('name','asc');
$sl->SetQueryFields(array('id','name'))->SetTable('Users')->SetLimit(0,10)->SetOrderBy(array($idDescSorting,$nameAscSorting));
//$sl->SetWhere("WHERE id >= 10 and name = 'Руслан'");


var_dump( $sl->GetQueryString());
