<?php

/**
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocoder\Provider;

use Geocoder\Exception\NoResultException;
use Geocoder\Exception\UnsupportedException;

/**
 * @author Nicolas Chaulet <nchaulet.fr@gmail.com>
 */
class DataScienceToolkitProvider extends AbstractProvider implements ProviderInterface
{
	/**
     * @var string
     */
    const API_URL = 'http://www.datasciencetoolkit.org/ip2coordinates/%s';

    /**
     * {@inheritDoc}
     */
    public function getGeocodedData($address)
    {
        if (!filter_var($address, FILTER_VALIDATE_IP)) {
            throw new UnsupportedException('The DataScienceToolkitProvider does not support Street addresses.');
        }

        if (in_array($address, array('127.0.0.1', '::1'))) {
            return $this->getLocalhostDefaults();
        }

        $query = sprintf(self::API_URL, $address);

      	return $this->executeQuery($query);
    }

    /**
     * {@inheritDoc}
     */
    public function getReversedData(array $coordinates)
    {
        throw new UnsupportedException('The DataScienceToolkitProvider is not able to do reverse geocoding.');
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'data_science_toolkit';
    }

    /**
    * @param  string $query
    * @return array
    */
    protected function executeQuery($query) {
    	$content = $this->getAdapter()->getContent($query);

    	$result = json_decode($content, true);

    	if (!$result) {
    		throw new NoResultException(sprintf('Could not execute query'));
    	}

    	$result = array_shift($result);

    	return array(
    		'latitude'    => $result['latitude'],
            'longitude'   => $result['longitude'],
            'city'        => $result['locality'],
            'country'     => $result['country_name'],
            'countryCode' => $result['country_code'],
            'zipcode'	  => $result['postal_code']
    	);

    }

}