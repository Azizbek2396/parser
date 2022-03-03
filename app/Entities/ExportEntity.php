<?php
namespace App\Entities;

class ExportEntity
{
    protected $_header;
    protected $_body;

    public function __construct(array $header, array $body)
    {
        $this->_header = $header;
        $this->_body   = $body;
    }

    public function getHeader()
    {
        return $this->_header;
    }

    public function getBody()
    {
        return $this->_body;
    }
}