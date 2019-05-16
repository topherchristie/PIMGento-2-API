<?php

namespace Pimgento\Api\Helper\Import;

/**
 * Class FamilyVariant
 *
 * @category  Class
 * @package   Pimgento\Api\Helper\Import
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class FamilyVariant extends Entities
{
    /**
     * Get columns from the api result
     *
     * @param array $result
     *
     * @return array
     */
    protected function getColumnsFromResult(array $result)
    {
        /** @var array $columns */
        $columns = [];
        /**
         * @var string $key
         * @var mixed $value
         */
        foreach ($result as $key => $value) {
            if (in_array($key, static::EXCLUDED_COLUMNS)) {
                continue;
            }
            $columns[$key] = $value;

            if (!is_array($value)) {
                continue;
            }
            if (empty($value)) {
                $columns[$key] = null;

                continue;
            }
            unset($columns[$key]);
            /**
             * @var string|int $local
             * @var string|array $data
             */
            foreach ($value as $local => $data) {
                if ($key == 'variant_attribute_sets') {
                    $columns['variant-axes_'.$data['level']]       = join(',', $data['axes']);
                    $columns['variant-attributes_'.$data['level']] = join(',', $data['attributes']);

                    continue;
                }
                if (!is_numeric($local)) {
                    if (is_array($data)) {
                        $data = join(',', $data);
                    }
                    $columns[$key.'-'.$local] = $data;
                } else {
                    $columns[$key] = join(',', $value);
                }
            }
        }
        $columns = $this->prefixToLowerCase($columns);

        return $columns;
    }

    /**
     * Set prefix to lower case
     * to avoid problems with values import
     *
     * @param string[] $values
     *
     * @return string[]
     */
    public function prefixToLowerCase($values)
    {
        /** @var string[] $newValues */
        $newValues = [];
        foreach ($values as $key => $data) {
            /** @var string[] $keyParts */
            $keyParts    = explode('-', $key, 2);
            $keyParts[0] = strtolower($keyParts[0]);

            if (count($keyParts) > 1) {
                $newValues[$keyParts[0].'-'.$keyParts[1]] = $data;
            } else {
                $newValues[$keyParts[0]] = $data;
            }
        }

        return $newValues;
    }
}
