<?php namespace Tests\GoogleScrapBundle\Service;

use GoogleScrapBundle\Service\GoogleScrap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class ParseResultsAndGetPositionTest extends TestCase
{
    public function testParseResultsAndGetPositionWithProperDomain()
    {
        // When
        $googleScrap = new GoogleScrap();
        $googleScrap->setDomainName('http://kpoliki.ru/');
        $file = $this->getMockedData();

        $position = $googleScrap->parseResultsAndGetPosition($file);

        // Result
        $this->assertEquals(4, $position);
    }

    public function testParseResultsAndGetPositionWithProperDomainAndAdditionalPath()
    {
        // When
        $googleScrap = new GoogleScrap();
        $googleScrap->setDomainName('http://kpoliki.ru/semeistvo/ushastih');
        $file = $this->getMockedData();

        $position = $googleScrap->parseResultsAndGetPosition($file);

        // Result
        $this->assertEquals(4, $position);
    }

    public function testParseResultsAndGetPositionWithAnotherDomain()
    {
        // When
        $googleScrap = new GoogleScrap();
        $googleScrap->setDomainName('http://koliki.ru/');
        $file = $this->getMockedData();

        $position = $googleScrap->parseResultsAndGetPosition($file);

        // Result
        $this->assertNull($position);
    }

    /**
     * @return string
     */
    private function getMockedData() {
        $finder = new Finder();
        $finder->files()->in(__DIR__);
        foreach ($finder as $file) {
            $file = file_get_contents($file->getRealPath());
        }

        return $file;
    }

}