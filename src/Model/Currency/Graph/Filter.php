<?php


namespace App\Model\Currency\Graph;


class Filter
{
    public $num_code;
    public $date_from;
    public $date_till;

    public function __construct()
    {
        $this->date_from = new \DateTime('-1 month');
        $this->date_till = new \DateTime('now');
    }
}