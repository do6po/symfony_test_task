<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 25.02.19
 * Time: 13:24
 */

namespace Tests\AppBundle\Validations;


use AppBundle\Entity\UserGroup;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\TraceableValidator;

class UserGroupValidationTest extends KernelTestCase
{
    /**
     * @var UserGroup
     */
    private $entity;

    /**
     * @var TraceableValidator
     */
    private $validator;

    public function setUp()
    {
        parent::setUp();

        $this->entity = new UserGroup();

        $this->validator = self::bootKernel()->getContainer()->get('validator');
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->user = null;

        $this->validator = null;
    }

    /**
     * @param $setter
     * @param $attribute
     * @param $value
     * @param $expected
     *
     * @dataProvider userValidationDataProvider
     */
    public function testUserValidation($setter, $attribute, $value, $expected)
    {
        $this->entity->$setter($value);
        $errors = $this->validator->validateProperty($this->entity, $attribute);
        $this->assertEquals($expected, $errors->count() === 0);
    }

    public function userValidationDataProvider()
    {
        return [
            'name [empty]' => ['setName', 'name', '', false],
            'name [to short]' => ['setName', 'name', 'na', false],
            'name [normal string]' => ['setName', 'name', 'name', true],
        ];
    }
}