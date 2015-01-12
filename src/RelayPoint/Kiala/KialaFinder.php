<?php

/**
 * Kiala relay point search.
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 2015-01-10
 */

namespace RelayPoint\Kiala;

use RelayPoint\Core\Address;
use RelayPoint\Core\Finder;
use RelayPoint\Core\RelayPointException;

class KialaFinder implements Finder
{
    const URL_SEARCH = 'http://locateandselect.kiala.com/kplist?dspid=#DSPID#&country=#COUNTRY#&zip=#ZIP#';
    const URL_DETAIL = 'http://locateandselect.kiala.com/kplist?dspid=#DSPID#&country=#COUNTRY#&shortID=#CODE#';

    /**
     * Finds the list of relay points.
     *
     * @param array $fields Search fields
     * @param boolean $active Turn to false if you only want active relay points
     * @return Address[]
     * @throws RelayPointException
     */
    public function search(array $fields, $active = true)
    {
        $urlParams = array(
            '#DSPID#' => $fields['dspid'],
            '#COUNTRY#' => $fields['country'],
            '#ZIP#' => $fields['zip'],
        );
        $url = str_replace(array_keys($urlParams), array_values($urlParams), self::URL_SEARCH);

        $xml = file_get_contents($url);

        try {
            $xmlElement = new \SimpleXMLElement($xml);
        } catch (\Exception $e) {
            $message = "Could not find Kiala relay points for zip " . $fields['zip'] . PHP_EOL;
            $message .= "URL : $url".PHP_EOL;
            $message .= "Response :".PHP_EOL;
            $message .= $xml;
            throw new RelayPointException($message, 0, $e);
        }

        $list = array();

        foreach ($xmlElement->kp as $relayPoint)
        {
            $address = $this->parseRelayPoint($relayPoint);
            if (!$active || ($active && $address->getField('active')))
            {
                $list[] = $address;
            }
        }

        return $list;
    }

    /**
     * Returns the details of one Kiala relay point.
     *
     * @param array $fields Search fields
     * @return Address|null
     * @throws RelayPointException
     */
    public function detail(array $fields)
    {
        $urlParams = array(
            '#DSPID#' => $fields['dspid'],
            '#COUNTRY#' => $fields['country'],
            '#CODE#' => $fields['code'],
        );
        $url = str_replace(array_keys($urlParams), array_values($urlParams), self::URL_DETAIL);

        $xml = file_get_contents($url);

        try
        {
            $xmlElement = new \SimpleXMLElement($xml);
        }
        catch (\Exception $e)
        {
            $message = "Could not find details for Kiala relay point " . $fields['code'] . PHP_EOL;
            $message .= "URL : $url".PHP_EOL;
            $message .= "Response :".PHP_EOL;
            $message .= $xml;
            throw new RelayPointException($message, 0, $e);
        }

        return $this->parseRelayPoint($xmlElement->kp);
    }

    /**
     * Parses an XML for an address and returns an Address object.
     *
     * @param \SimpleXMLElement $relayPoint XML node to parse
     * @return Address
     */
    private function parseRelayPoint(\SimpleXMLElement $relayPoint)
    {
        $active = true;
        if ($relayPoint->status['available'] == 0)
        {
            $active = false;
        }

        $days = array(
            'MON' => 'Lundi',
            'TUE' => 'Mardi',
            'WED' => 'Mercredi',
            'THU' => 'Jeudi',
            'FRI' => 'Vendredi',
            'SAT' => 'Samedi',
            'SUN' => 'Dimanche',
        );

        $fields = array(
            'code' => strval($relayPoint['shortId']),
            'name' => strval($relayPoint->name),
            'street' => strval($relayPoint->street),
            'zip' => strval($relayPoint->zip),
            'city' => strval($relayPoint->city),
            'locationHint' => strval($relayPoint->address->locationHint),
            'image' => strval($relayPoint->picture['href']),
            'latitude' => strval($relayPoint->coordinate->latitude),
            'longitude' => strval($relayPoint->coordinate->longitude),
            'active' => $active,
        );
        $address = new Address($fields);

        foreach ($relayPoint->openingHours->day as $openingDay)
        {
            $day = $days[strval($openingDay['name'])];

            if (!isset($openingDay->timespan))
            {
                $detail = 'Fermé';
            }
            else
            {
                $detail = '';
                foreach ($openingDay->timespan as $timeSpan)
                {
                    if ($detail != '')
                    {
                        $detail .= ' ';
                    }
                    $detail .= strval($timeSpan->start) . ' - ' . strval($timeSpan->end);
                }
            }

            $address->addOpeningHour($day, $detail);
        }
        return $address;
    }
}
