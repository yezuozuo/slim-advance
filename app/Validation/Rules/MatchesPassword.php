<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

/**
 * Class MatchesPassword
 *
 * @package App\Validation\Rules
 */
class MatchesPassword extends AbstractRule {

    /**
     * @var
     */
    protected $password;

    /**
     * MatchesPassword constructor.
     *
     * @param $password
     */
    public function __construct($password) {
        $this->password = $password;
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input) {
        return password_verify($input, $this->password);
    }
}