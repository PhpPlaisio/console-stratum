<?php
declare(strict_types=1);

namespace Plaisio\Console\Stratum\Test\Command;

use PHPUnit\Framework\TestCase;
use Plaisio\Console\Application\PlaisioApplication;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Test cases for SourcesCommand.
 */
class SourcesCommandTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test adding a property to the kernel.
   */
  public function test01(): void
  {
    $application = new PlaisioApplication();
    $application->setAutoExit(false);
    $tester = new ApplicationTester($application);
    $tester->run(['command' => 'plaisio:stratum-sources'],
                 ['verbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE]);

    $output = $tester->getDisplay();
    self::assertSame(0, $tester->getStatusCode(), $output);
    self::assertFileEquals('test/Command/etc/stratum-sources.expected.txt', 'test/Command/etc/stratum-sources.txt');

    echo $output;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
