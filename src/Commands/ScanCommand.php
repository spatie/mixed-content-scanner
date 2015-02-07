<?php namespace Spatie\Commands;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Spatie\Scanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ScanCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('scan')
            ->setDescription('Scan a https-enabled site for mixed content')
            ->addArgument('url', InputArgument::OPTIONAL, 'The url of the site to scan. Should start with https://')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'File where the results will be written as json');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        if ($url == '') {
            $url = $this->askUserForUrl($output);
        }

        if (! $this->validateUrl($url)) {
            $output->writeln('<error>'.$url.' is not a valid url that starts with http(s)://</error>');

            return;
        }

        $scanner = new Scanner($output, new Client());

        $scannerResults = $scanner
            ->setRootUrl($url)
            ->scan();

        $this->presentResults($output, $scannerResults);

        $outputFile = $input->getOption('output');
        if ($outputFile) {
            $this->writeAsJson($scannerResults, $outputFile, $output);
        }
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

            $tableArray = [];

            foreach ($scannerResults as $siteUrl => $mixedContentUrls) {
                $tableArray[] = [$siteUrl, implode(PHP_EOL, $mixedContentUrls)];
            }

            $table = $this->getHelper('table');
            $table
                ->setHeaders(['URL', 'Mixed Content'])
                ->setRows($tableArray);

            $table->render($output);
        } else {
            $output->writeln('<info>No mixed content found! Hurray!</info>');
        }

        $output->writeln('');
    }

    /**
     * Validate the given url
     *
     * @param $url
     * @return bool
     */
    protected function validateUrl($url)
    {
        if (! parse_url($url) )
        {
            return false;
        }
        if ( ! $this->startsWith($url, 'https://') )
        {
            return false;
        }
        if ($this->startsWith($url, 'http://')) {
            return false;
        };

        return true;
    }

    /**
     * @param  OutputInterface $output
     * @return mixed
     */
    public function askUserForUrl(OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');

        $url = $dialog->ask(
            $output,
            'Which http(s)-site should be scanned for mixed content? '
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
    protected function startsWith($string, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($string, $needle, -strlen($string)) !== FALSE;
    }

    /**
     * Write the given scannerResults as json to the given outputfile
     *
     * @param array  $scannerResults
     * @param string $outputFile
     * @param OutputInterface$output
     */
    protected function writeAsJson($scannerResults, $outputFile, OutputInterface $output)
    {
        file_put_contents($outputFile, (new Collection($scannerResults))->toJson());

        $output->writeln(['<comment>Results written to: '.$outputFile, '']);
    }
}
