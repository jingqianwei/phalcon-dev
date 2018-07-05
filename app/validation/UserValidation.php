<?php
/**
 * Created by PhpStorm.
 * User: zhangchaozheng
 * Date: 2018/5/7
 * Time: 11:44
 */
namespace SbDaValidation;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class UserValidation extends Validation
{
    public function signInValidate($data = [])
    {
        if(empty($data)) {
            return 10016;
        }

        $this->add(
            'username',
            new PresenceOf(
                [
                    'message' => 10016,
                ]
            )
        );

        $this->add(
            'password',
            new PresenceOf(
                [
                    'message' => 10016,
                ]
            )
        );

        $messages = $this->validate($data);

        if (count($messages)) {
            foreach ($messages as $message) {
                return $message->getMessage();
            }
        }
    }
}