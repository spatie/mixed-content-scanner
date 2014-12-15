<?php namespace Spatie;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Scan a https-site for mixed content
 *
 * Heavily based on the scanner by Bramus
 * https://github.com/bramus/mixed-content-scan
 */
class Scanner {
    protected $rootUrlBasePath;

    /**
     * The root URL to start scanning at
     * @var String
     */
    private $rootUrl;

    /**
     * The url parts of the root URL
     *
     * @var Array
     */
    private $rootUrlParts;

    /**
     * Array of all pages scanned / about to be scanned
     * @var Array
     */
    private $pages = [];

    /**
     * Array of patterns in URLs to ignore to fetch content from
     * @var  Array
     */
    private $ignorePatterns = [];

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Array with urls with mixed content
     * @var  Array
     */
    private $mixedContentUrls = [];


    /**
     * Create a new Scanner instance
     * .
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output) {
        $this->output = $output;
    }

    /**
     * Set the url of the site to scan
     *
     * @param $rootUrl
     * @return $this
     */
    public function setRootUrl($rootUrl) {

        // Make sure the rootUrl is parse-able
        $urlParts = parse_url($rootUrl);

        // Force trailing / on rootUrl, it's easier for us to work with it
        if (substr($rootUrl, -1) != '/') $rootUrl .= '/';

        // store rootUrl
        $this->rootUrl = strstr($rootUrl, '?') ? substr($rootUrl, 0, strpos($rootUrl, '?')) : $rootUrl;

        // store rootUrl without queryString
        $this->rootUrlBasePath = $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'];

        // store urlParts
        $this->rootUrlParts = $urlParts;

        return $this;

    }

    public function setIgnorePatterns($ignorePatterns, $toReplace = '{$rootUrl}') {

        // Force trailing / on $toReplace
        if (substr($toReplace, -1) != '/') $toReplace .= '/';

        // Store ignorepatterns
        $this->ignorePatterns = (array) $ignorePatterns;

        // Replace {$rootUrl} in the ignorepatterns
        foreach ($this->ignorePatterns as &$p) {
            $p = str_replace($toReplace, $this->rootUrl, $p);
        }
    }

    /**
     * Scan entire website
     * Return an array with mixed content urls
     *
     * @return Array
     */
    public function scan() {

        $this->output->writeln(['<info>Start scanning ' . $this->rootUrl . '</info>', '']);


        // Add the root URL to the list of pages
        $this->pages[] = $this->rootUrl;

        // Current index at $this->pages
        $currentPageIndex = 0;

        // Start looping
        while(true) {

            // Get the current pageUrl
            $currentPageUrl = $this->pages[$currentPageIndex];

            // Give feedback on the CLI
            $this->output->writeln('<comment>Scanning ' . $currentPageUrl . ' ...</comment>');

            // Scan a single page. Returns the mixed content urls (if any)
            $mixedContentUrls = $this->scanPage($currentPageUrl);

            // Got mixed content? Give feedback on the CLI
            if (count($mixedContentUrls)) {

                foreach ($mixedContentUrls as $url) {

                    $this->mixedContentUrls[$currentPageUrl][] = $url;

                    $this->output->writeln('<error>Found mixed content: ' . $url . '</error>');
                }
            }

            // Done scanning all pages? Then quit! Otherwise: scan the next page
            if ($this->scannedAllPages($currentPageIndex))
            {
                break;
            }

            $currentPageIndex++;
        }

        $this->output->writeln(['', '<info>Succesfully scanned ' . count($this->pages) . ' pages for mixed content</info>']);

        return $this->mixedContentUrls;

    }


    /**
     * Scan a single URL
     *
     * @param  String $pageUrl 	URL of the page to scan
     * @return array
     */
    private function scanPage($pageUrl) {

        // Array holding all URLs which are found to be Mixed Content
        // We'll return this one at the very end
        $mixedContentUrls = [];

        // Get the HTML of the page
        $html = $this->getContents($pageUrl);

        // Create new DOMDocument using the fetched HTML
        $doc = new \DOMDocument();
        if ($doc->loadHTML($html)) {

            // Loop all links found
            foreach ($doc->getElementsByTagName('a') as $el) {
                if ($el->hasAttribute('href')) {

                    // Normalize the URL first so that it's an absolute URL.
                    $url = $this->normalizeUrl($el->getAttribute('href'), $pageUrl);

                    // Remove fragment from URL (if any)
                    if (strpos($url, '#')) $url = substr($url, 0, strpos($url, '#'));

                    // If the URL should not be ignored (pattern matching) and isn't added to the list yet, add it to the list of pages to scan.
                    if ((preg_match('#^' . $this->rootUrlBasePath . '#i', $url) === 1) && !in_array($url, $this->pages)) {

                        $ignorePatternMatched = false;
                        foreach ($this->ignorePatterns as $p) {
                            if ($p && preg_match('#' . $p . '#i', $url)) {
                                $ignorePatternMatched = true;
                                // echo ' - ignoring ' . $url . PHP_EOL;
                                break;
                            }
                        }
                        if (!$ignorePatternMatched) {
                            $this->pages[] = $url;
                        }
                    }

                }
            }

            // Check all iframes contained in the HTML
            foreach ($doc->getElementsByTagName('iframe') as $el) {
                if ($el->hasAttribute('src')) {
                    $url = $el->getAttribute('src');
                    if (substr($url, 0, 7) == "http://") {
                        $mixedContentUrls[] = $url;
                    }
                }
            }

            // Check all images contained in the HTML
            foreach ($doc->getElementsByTagName('img') as $el) {
                if ($el->hasAttribute('src')) {
                    $url = $el->getAttribute('src');
                    if (substr($url, 0, 7) == "http://") {
                        $mixedContentUrls[] = $url;
                    }
                }
            }

            // Check all script elements contained in the HTML
            foreach ($doc->getElementsByTagName('script') as $el) {
                if ($el->hasAttribute('src')) {
                    $url = $el->getAttribute('src');
                    if (substr($url, 0, 7) == "http://") {
                        $mixedContentUrls[] = $url;
                    }
                }
            }

            // Check all stylesheet links contained in the HTML
            foreach ($doc->getElementsByTagName('link') as $el) {
                if ($el->hasAttribute('href') && $el->hasAttribute('rel') && ($el->getAttribute('rel') == 'stylesheet')) {
                    $url = $el->getAttribute('href');
                    if (substr($url, 0, 7) == "http://") {
                        $mixedContentUrls[] = $url;
                    }
                }
            }

            // Check all `object` elements contained in the HTML
            foreach ($doc->getElementsByTagName('object') as $el) {
                if ($el->hasAttribute('data')) {
                    $url = $el->getAttribute('data');
                    if (substr($url, 0, 7) == "http://") {
                        $mixedContentUrls[] = $url;
                    }
                }
            }

        }

        // Return the array of Mixed Content
        return $mixedContentUrls;
    }


    /**
     * Normalizes a URL to become an absolute URL
     * @param  String $linkedUrl	The URL linked to
     * @param  String $pageUrlContainingTheLinkedUrl	The URL of the page holding the URL linked to
     * @return String
     */
    private function normalizeUrl($linkedUrl, $pageUrlContainingTheLinkedUrl) {

        // Absolute URLs
        // --> Don't change
        if (substr($linkedUrl, 0, 8) == "https://" || substr($linkedUrl, 0, 7) == "http://") {
            return $this->canonicalize($linkedUrl);
        }

        // Protocol relative URLs
        // --> Prepend scheme
        if (substr($linkedUrl, 0, 2) == "//") {
            return $this->canonicalize($this->rootUrlParts['scheme'] . ':' . $linkedUrl);
        }

        // Root-relative URLs
        // --> Prepend scheme and host
        if (substr($linkedUrl, 0, 1) == "/") {
            return $this->canonicalize($this->rootUrlParts['scheme'] . '://' . $this->rootUrlParts['host'] . '/' . substr($linkedUrl, 1));
        }

        // Document fragment
        // --> Don't scan it
        if (substr($linkedUrl, 0, 1) == "#") {
            return '';
        }

        // Links that are not http or https (e.g. mailto:, tel:)
        // --> Don't scan it
        $linkedUrlParts = parse_url($linkedUrl);
        if (isset($linkedUrlParts['scheme']) && !in_array($linkedUrlParts['scheme'], array('http','https',''))) {
            return '';
        }

        // Document-relative URLs
        // --> Append $linkedUrl to $pageUrlContainingTheLinkedUrl's PATH
        return $this->canonicalize(substr($pageUrlContainingTheLinkedUrl, 0, strrpos($pageUrlContainingTheLinkedUrl, '/')) . '/' . $linkedUrl);

    }


    /**
     * Remove ../ and ./ from a given URL
     * @see  http://php.net/manual/en/function.realpath.php#71334
     * @param  String
     * @return String
     */
    private function canonicalize($url) {

        $url = explode('/', $url);
        $keys = array_keys($url, '..');

        foreach($keys AS $keypos => $key) {
            array_splice($url, $key - ($keypos * 2 + 1), 2);
        }

        $url = implode('/', $url);
        $url = str_replace('./', '', $url);

        return $url;
    }


    /**
     * Get the contents of a given URL (via GET)
     * @param  String $pageUrl 	The URL of the page to get the contents of
     * @return String
     */
    private function getContents(&$pageUrl) {

        // Init CURL
        $curl = curl_init();

        @curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_URL => $pageUrl,
            CURLOPT_TIMEOUT_MS => 10000
        ));

        // Fetch the page contents
        $resp = curl_exec($curl);

        // Fetch the URL of the page we actually fetched
        $newUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

        if ($newUrl != $pageUrl) {

            // echo ' >> ' . $newUrl . PHP_EOL;

            // If we started at the rootURL, and it got redirected:
            // --> overwrite the rootUrl so that we use the new one from now on
            if ($pageUrl == $this->rootUrl) {

                // Store the new rootUrl
                $this->setRootUrl($newUrl, false);

                // Update ignore patterns
                $this->setIgnorePatterns($this->ignorePatterns, $pageUrl);

            }

            // Update $pageUrl (pass by reference!)
            $pageUrl = $newUrl;

        }

        // Got an error?
        $curl_errno = curl_errno($curl);
        $curl_error = curl_error($curl);
        if ($curl_errno > 0) {
            $this->output->writeln('<error>cURL Error (' . $curl_errno . '): ' . $curl_error . '</error>');
        }

        // Close it
        @curl_close($curl);

        // Return the fetched contents
        return $resp;
    }

    /**
     * Determine if we scanned all the pages
     *
     * @param $currentPageIndex
     * @return bool
     */
    public function scannedAllPages($currentPageIndex)
    {
        return $currentPageIndex + 1 == count($this->pages);
    }

}
