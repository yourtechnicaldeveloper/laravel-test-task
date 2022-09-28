<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class ExistsUserId implements Rule {

    private $where = NULL;
    private $message = NULL;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($where = null, $message = null) {
        $this->where = $where;
        $this->message = $message ?: 'Invalid User.';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $query = User::where('users.id', $value);
        if (is_callable($this->where)) {
            $where = $this->where;
            $query = $where($query);
        } else if (!blank($this->where)) {
            $query->where($this->where);
        }
        return $query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->message;
    }

}
