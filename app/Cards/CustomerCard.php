<?php

namespace App\Cards;

use Sanjab\Cards\Card;
use stdClass;

class CustomerCard extends Card
{
    public function init()
    {
        $this->tag('customer-card');
        $this->cols(12);
    }

    protected function modifyResponse(stdClass $response)
    {
        if (is_callable($this->property("value"))) {
            $response->data = $this->property("value")();
        } else {
            $response->data = $this->property("value");
        }
    }
}
