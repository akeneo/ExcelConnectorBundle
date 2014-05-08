<?php

namespace Pim\Bundle\ExcelConnectorBundle\Excel\Reader;

/**
 * Transforms Excel dates in DateTime objects
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DateTransformer
{
    /**
     * @var \DateTime
     */
    protected $baseDate;

    /**
     * Cached formats
     *
     * @var array
     */
    protected $dateFormats = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->baseDate = new \DateTime('1900-01-00 00:00:00 UTC');
    }

    /**
     * Returns true if the Excel format is a date format
     *
     * @param string $format
     *
     * @return boolean
     */
    public function isDateFormat($format)
    {
        if (!isset($this->dateFormats[$format])) {
            $this->dateFormats[$format] = (bool) preg_match('{^(\[\$[[:alpha:]]*-[0-9A-F]*\])*[hmsdy]}i', $format);
        }

        return $this->dateFormats[$format];
    }

    /**
     * Transforms an Excel date into a DateTime object
     *
     * @param String $value
     *
     * @return \DateTime
     */
    public function transform($value)
    {
        $days = floor($value);

        $seconds = round(($value - $days)*86400);

        $date = clone $this->baseDate;
        $date->modify(sprintf('+%sday +%ssecond', $days-1, $seconds));

        return $date;
    }
}
