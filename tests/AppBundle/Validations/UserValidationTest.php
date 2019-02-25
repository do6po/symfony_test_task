<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 25.02.19
 * Time: 12:27
 */

namespace Tests\AppBundle\Validations;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\TraceableValidator;


class UserValidationTest extends KernelTestCase
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var TraceableValidator
     */
    private $validator;

    public function setUp()
    {
        parent::setUp();

        $this->user = new User();

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
        $this->user->$setter($value);
        $errors = $this->validator->validateProperty($this->user, $attribute);
        $this->assertEquals($expected, $errors->count() === 0);
    }

    public function userValidationDataProvider()
    {
        return [
            'name [empty]' => ['setName', 'name', '', false],
            'name [to short]' => ['setName', 'name', 'na', false],
            'name [normal string]' => ['setName', 'name', 'name', true],
            'email [empty]' => ['setEmail', 'email', '', false],
            'email [not valid]' => ['setEmail', 'email', 'email@email', false],
            'email [valid]' => ['setEmail', 'email', 'email@example.com', true],
        ];
    }
}