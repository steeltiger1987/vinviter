<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    protected $trim = true;

    public function input($key = null, $default = null)
    {
        $input = $this->getInputSource()->all() + $this->query->all();

        // trim whitespace from all input
        $input = $this->getTrimmedInput($input);

        return data_get($input, $key, $default);
    }

    /**
     * Get trimmed input
     *
     * @param array $input
     *
     * @return array
     */
    protected function getTrimmedInput(array $input)
    {
        if ($this->trim) {
            array_walk_recursive($input, function (&$item, $key) {

                if (is_string($item) && !str_contains($key, 'password')) {
                    $item = trim($item);
                }
            });
        }

        return $input;
    }
}
