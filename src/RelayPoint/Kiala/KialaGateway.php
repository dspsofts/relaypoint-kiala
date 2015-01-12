<?php

namespace RelayPoint\Kiala;

class KialaGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Kiala';
    }

    public function getDefaultParameters()
    {
        return array(
            'dspid' => '',
            'country' => 'FR',
        );
    }

    public function getDspid()
    {
        return $this->getParameter('dspid');
    }

    public function setDspid($value)
    {
        $this->setParameter('dspid', $value);
    }

    public function getCountry()
    {
        return $this->getParameter('country');
    }

    public function setCountry($value)
    {
        $this->setParameter('country', $value);
    }
}