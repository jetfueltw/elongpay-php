<?php

namespace Jetfuel\Elongpay\Traits;

trait ResultParser
{
    /**
     * Parse JSON format response to array.
     *
     * @param string $response
     * @return array
     */
    public function parseResponse($response)
    {
        return json_decode($response, true);
    }

}
