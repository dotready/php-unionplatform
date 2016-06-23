<?php
/**
 * Created by PhpStorm.
 * User: daems
 * Date: 23-06-16
 * Time: 17:11
 */

namespace phpunionplatform\library\QueryBuilder;

use phpunionplatform\exception\PhpunionplatformException;

class HttpQueryBuilder
{
    /**
     * @var string
     */
    private $modes = 'sdc';

    /**
     * HttpQueryBuilder constructor.
     */
    public function __construct() {}

    /**
     * @param $mode
     * @param array $params
     * @return string
     * @throws PhpunionplatformException
     */
    public function buildHttpQuery($mode, array $params)
    {
        if (strpos('sdc', $this->modes) === false) {
            throw new PhpunionplatformException('wrong mode');
        }

        $data = array('mode' => $mode);

        foreach ($params as $key => $param) {
            $data[$key] = $param;
        }

        return http_build_query($data);
    }
}
