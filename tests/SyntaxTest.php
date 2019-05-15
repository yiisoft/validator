<?php


namespace yii\tests\framework\validators;


use PHPUnit\Framework\TestCase;
use Yiisoft\Validators\Boolean;
use Yiisoft\Validators\Length;
use Yiisoft\Validators\String;
use Yiisoft\Validators\Validator;

class SyntaxTest extends TestCase
{
    public function testSyntax()
    {
        $validator = new Validator();
        $result = $validator->validateValue('1', [
            (new Boolean())->falseValue('0')->trueValue('1')
        ]);

        if (!$result->isValid())
        {
            foreach ($result->getErrors() as $error) {

            }
        }
    }
}