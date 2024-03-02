<?php
/**
 * DjoSmer, 2024.02
 */

namespace Lancache;

require_once '../customwildcarddns/CustomWildcardDNS.php';

use Custom\WildcardDNS;

class CacheDomains
{

    public const CACHE_DOMAINS_FILE = '/tmp/cacheDomains.json';

    /**
     * @param string $cacheDomainsUrl
     * @return array|false
     */
    function getCacheDomains($cacheDomainsUrl)
    {
        try {
            $content = file_get_contents($cacheDomainsUrl);

            if (empty($content)) {
                return returnError('Cache domains file is empty.');
            }

            $data = json_decode($content, true);

            if (!isset($data['cache_domains'])) {
                return returnError("Cache domains file doesn't have a cache_domains key.");
            }

            $data['url'] = $cacheDomainsUrl;
            file_put_contents(self::CACHE_DOMAINS_FILE, json_encode($data));

            return ['success' => true, 'data' => $data['cache_domains']];

        } catch (\Exception $ex) {
            return returnError($ex->getMessage());
        }
    }

    /**
     * @param array $clientDomains
     * @param string $ip
     * @return bool
     */
    function updateCacheDomains(array $clientDomains, string $ip): bool
    {

        if (!count($clientDomains)) {
            return $this->echoEvent(returnError('Domains list is empty.'));
        }

        if (!validIP($ip)) {
            return $this->echoEvent(returnError('IP must be valid'));
        }

        $clientDomains = array_flip($clientDomains);
        $content = file_get_contents(self::CACHE_DOMAINS_FILE);
        $data = json_decode($content, true);

        $domainFileUrl = preg_replace('/[^\\/]*\\.json$/i', '', $data['url']);

        $customWildcardDNS = WildcardDNS::getInstance();
        foreach ($data['cache_domains'] as $cacheDomain) {
            $name = $cacheDomain['name'];
            $wildcardName = 'LanCache_' . $name;
            $domainFiles = $cacheDomain['domain_files'];

            if (!isset($clientDomains[$name])) {
                continue;
            }

            $this->echoEvent(sprintf('Start working with %s', $name));
            $customWildcardDNS->deleteEntry($wildcardName, '*');

            foreach ($domainFiles as $domainFile) {
                try {
                    $content = file_get_contents($domainFileUrl . $domainFile);
                    $this->echoEvent(sprintf('- Loaded %s', $domainFile));

                    $domains = explode("\n", $content);

                    foreach ($domains as $domain) {
                        if (empty($domain) || $domain[0] === '#') {
                            continue;
                        }
                        $domain = preg_replace('/^\*\./i', '', $domain);
                        $result = $customWildcardDNS->addEntry($wildcardName, $domain, $ip);
                        $this->echoEvent('- ' . $result['message']);
                    }

                } catch (\Exception $ex) {
                    $this->echoEvent($ex->getMessage());
                }
            }
        }

        pihole_execute('restartdns');

        $this->echoEvent(returnSuccess("That's done."));

        return true;
    }

    /**
     * @param array|string $data
     * @return bool
     */
    function echoEvent($data): bool
    {
        if (is_string($data)) {
            $data = ['message' => $data];
        }
        echo 'data: ' . json_encode($data) . "\n\n";
        return true;
    }
}
