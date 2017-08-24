<?php namespace GoogleScrapBundle\Service;

use Symfony\Component\DomCrawler\Crawler;

class GoogleScrap
{
    /**
     * @var string $domainName
     */
    private $domainName;

    /** @var string $keyWord */
    private $keyWord;

    /**
     * @var integer|null $position
     */
    private $position;

    /**
     * @param string $domainName
     */
    public function setDomainName($domainName) {
        $this->domainName = $domainName;
    }

    /**
     * @param string $keyWord
     */
    public function setKeyWord($keyWord) {
        $this->keyWord = $keyWord;
    }

    /**
     * @return array
     */
    public function getDomainPositionByKeyword() {
        $status = $this->makeRequest();

        return ['status' => $status, 'position' => $this->position];
    }

    /**
     * @param $result
     * @return int|null
     */
    public function parseResultsAndGetPosition($result) {
        $position = null;
        $crawler = new Crawler($result);

        $links = $crawler->filter('cite')->each(function (Crawler $node) {
            return $node->text();
        });

        $domain = $this->getHostFromUrl();

        if ($domain) {
            foreach ($links as $currentPosition => $link) {
                if (strpos($link, $domain) !== false) {
                    return $currentPosition + 1;
                }
            }
        }

        return null;
    }

    /**
     * @return string
     */
    private function makeRequest() {
        $url = $this->createRequestUrl();
        $proxies = $this->getProxies();

        $connectedToProxy = false;

        while (!$connectedToProxy && !empty($proxies)) {
            $arrayKey = array_rand($proxies);
            $proxy = $proxies[$arrayKey];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt ($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);

            $result = curl_exec($ch);

            if ($result && !curl_error($ch)) {
                $this->position = $this->parseResultsAndGetPosition($result);
                $connectedToProxy = true;
            } else {
                unset ($proxies[$arrayKey]);
            }

            curl_close($ch);
            sleep(1);
        }

        if (empty($proxies)) {
            return false;
        }

        return true;
    }

    /**
     * @param int $resultsAmount
     * @param string $lang
     * @return string
     */
    private function createRequestUrl($resultsAmount = 100, $lang = 'ru') {
        return 'http://www.google.com.ru/custom?hl=' . $lang . '&num=' . $resultsAmount . '&q=' .
            urlencode($this->keyWord);
    }

    /**
     * @return array
     */
    private function getProxies() {
        $proxy = [];
        $crawler = new Crawler();

        $crawler->addXmlContent(file_get_contents('http://www.xroxy.com/proxyrss.xml'));

        $proxyIp = $crawler->filterXPath('//prx:ip')->each(function (Crawler $node) {
            return $node->text();
        });

        $proxyPort = $crawler->filterXPath('//prx:port')->each(function (Crawler $node) {
            return $node->text();
        });

        foreach ($proxyIp as $k => $ipString) {
            $proxy[] = $proxyIp[$k] . ':' . $proxyPort[$k];
        }

        return $proxy;
    }

    /**
     * @return string|null
     */
    private function getHostFromUrl() {
        $domain = null;
        $parsedUrl = parse_url($this->domainName);

        if (array_key_exists('host', $parsedUrl)) {
            return $parsedUrl['host'];
        }

        return $domain;
    }
}