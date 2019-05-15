<?php


namespace Yiisoft\Validator;


final class ResultSet
{
    private $results = [];

    public function addResult($attribute, Result $result)
    {
        $this->results[$attribute] = $result;
    }
}