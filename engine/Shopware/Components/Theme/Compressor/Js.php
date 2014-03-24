<?php
/**
 * Shopware 4
 * Copyright © shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Components\Theme\Compressor;

/**
 * Javascript compressor for the frontend themes.
 * Used to compress theme and plugin javascript files.
 *
 * @category  Shopware
 * @package   Shopware\Components\Theme\Compressor
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Js implements CompressorInterface
{
    /**
     * @var \JSMin
     */
    private $compressor;

    /**
     * @param \JSMin $compressor
     */
    function __construct(\JSMin $compressor)
    {
        $this->compressor = $compressor;
    }

    /**
     * Compress the passed content and returns
     * the compressed content.
     *
     * @param string $content
     * @return string
     */
    public function compress($content)
    {
        return $this->compressor->minify($content);
    }
}