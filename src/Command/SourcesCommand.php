<?php
declare(strict_types=1);

namespace Plaisio\Console\Stratum\Command;

use Plaisio\Console\Command\PlaisioCommand;
use Plaisio\Console\Exception\ConfigException;
use Plaisio\Console\Helper\PlaisioXmlPathHelper;
use Plaisio\Console\Helper\TwoPhaseWrite;
use Plaisio\Console\Stratum\Helper\PlaisioXmlQueryHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for collecting source patterns for finding stored routines provided by packages.
 */
class SourcesCommand extends PlaisioCommand
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function configure()
  {
    $this->setName('plaisio:stratum-sources')
         ->setDescription('Sets the Stratum patterns for finding sources of stored routines');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $this->io->title('Plaisio: Stratum Sources');

    $patterns        = $this->findStratumSourcePatterns();
    $configFilename  = $this->stratumConfigFilename();
    $sourcesFilename = $this->sourcesListFilename($configFilename);

    $this->saveSourcePatterns($sourcesFilename, $patterns);

    return 0;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Saves the Stratum sources patterns to a file.
   *
   * @param string   $sourcesFilename The name of the file.
   * @param string[] $patterns        The Stratum sources patterns.
   */
  protected function saveSourcePatterns(string $sourcesFilename, array $patterns): void
  {
    $content = implode(PHP_EOL, $patterns);
    $content .= PHP_EOL;

    $helper = new TwoPhaseWrite($this->io);
    $helper->write($sourcesFilename, $content);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the Stratum sources patterns for this project.
   *
   * @return string[]
   */
  private function findStratumSourcePatterns(): array
  {
    $plaisioXmlList = PlaisioXmlPathHelper::findPlaisioXmlAll('stratum');

    $patterns = [];
    foreach ($plaisioXmlList as $plaisioConfigPath)
    {
      $packageRoot = dirname($plaisioConfigPath);
      $helper      = new PlaisioXmlQueryHelper($plaisioConfigPath);
      $list        = $helper->queryStratumSourcePatterns();
      foreach ($list as $item)
      {
        $patterns[] = (($packageRoot!='.') ? $packageRoot.'/' : '').$item;
      }
    }

    sort($patterns);

    return $patterns;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the name of the file for storing the list of patterns for sources of stored routines.
   *
   * @param string $configFilename The name Stratum configuration file.
   *
   * @return string
   */
  private function sourcesListFilename(string $configFilename): string
  {
    $settings = parse_ini_file($configFilename, true);

    if (!isset($settings['loader']['sources']))
    {
      throw new ConfigException("Setting '%s' not found in section '%s' in file '%s'",
                                'sources',
                                'loader',
                                $configFilename);
    }

    $sources = $settings['loader']['sources'];

    if (!str_starts_with($sources, 'file:'))
    {
      throw new ConfigException("Setting '%s' in section '%s' in file '%s' must be formatted like 'file:<filename>'",
                                'sources',
                                'loader',
                                $configFilename);
    }

    $basedir = dirname($configFilename);
    $path    = substr($sources, 5);

    return $basedir.'/'.$path;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the name of the Stratum configuration file.
   *
   * @return string
   */
  private function stratumConfigFilename(): string
  {
    $path   = PlaisioXmlPathHelper::plaisioXmlPath('stratum');
    $helper = new PlaisioXmlQueryHelper($path);

    return $helper->queryStratumConfigFilename();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
