<?php namespace Spatie\Commands;

use GuzzleHttp\Client;
use Spatie\Scanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScanCommand extends Command {

    public function configure()
    {
        $this
            ->setName('scan')
            ->setDescription('Scan a https-enabled site for mixed content')
            ->addArgument('url', InputArgument::OPTIONAL, 'The url of the site to scan. Should start with https://');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        if ($url == '')
        {
            $url = $this->askUserForUrl($output);
        }

        if (! $this->validateUrl($url))
        {
            $output->writeln('<error>' . $url . ' is not a valid url that starts with https://</error>');
            return;
        }

        $scanner = new Scanner($output, new Client());

        $scannerResults = $scanner
            ->setRootUrl($url)
            ->scan();

        $this->presentResults($output, $scannerResults);
    }

    /**
     * Present the results of the scan
     *
     * @param OutputInterface $output
     * @param $scannerResults
     */
    protected function presentResults(OutputInterface $output, $scannerResults)
    {
        $output->writeln('');

        if (count($scannerResults)) {
            $output->writeln('<error>Found some mixed content</error>');

            foreach ($scannerResults as $siteUrl => $mixedContentUrls) {
                $tableArray[] = [$siteUrl, implode(PHP_EOL, $mixedContentUrls)];
            }

            $table = $this->getHelper('table');
            $table
                ->setHeaders(['URL', 'Mixed Content'])
                ->setRows($tableArray);

            $table->render($output);
        }
        else
        {
            $output->writeln('<info>No mixed content found! Hurray!</info>');
        }
    }

    /**
     * Validate the given url
     *
     * @param $url
     * @return bool
     */
    protected function validateUrl($url)
    {
        return parse_url($url) AND $this->startsWith($url, 'https://');
    }

    /**
     * @param OutputInterface $output
     * @return mixed
     */
    public function askUserForUrl(OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');

        $url = $dialog->ask(
            $output,
            'Which https-site should be scanned for mixed content? '
        );

        return $url;
    }

    /**
     * Determine if the string starts with the given needle
     *
     * @param $string
     * @param $needle
     * @return bool
     */
    function startsWith($string, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($string, $needle, -strlen($string)) !== FALSE;
    }
}