<?php
declare(strict_types=1);

namespace Plaisio\Console\Stratum\Helper;

use Plaisio\Console\Exception\ConfigException;

/**
 * Helper class for querying information from a plaisio.xml file.
 */
class PlaisioXmlQueryHelper extends \Plaisio\Console\Helper\PlaisioXmlQueryHelper
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the path to the config file of Stratum.
   *
   * @return string
   */
  public function queryStratumConfigFilename(): string
  {
    $xpath = new \DOMXpath($this->xml);
    $node  = $xpath->query('/stratum/config')->item(0);

    if ($node===null)
    {
      throw new ConfigException('Stratum configuration file not defined in %s', $this->path);
    }

    return $node->nodeValue;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the Stratum source patterns for finding stored routines.
   *
   * @return string[]
   */
  public function queryStratumSourcePatterns(): array
  {
    $patterns = [];

    $xpath = new \DOMXpath($this->xml);
    $list  = $xpath->query('/stratum/includes/include');
    foreach ($list as $item)
    {
      $patterns[] = $item->nodeValue;
    }

    return $patterns;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
