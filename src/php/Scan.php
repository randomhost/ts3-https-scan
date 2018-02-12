<?php

namespace randomhost\TeamSpeak3;

use InvalidArgumentException;
use TeamSpeak3;
use TeamSpeak3_Node_Channel;
use TeamSpeak3_Node_Server;

/**
 * Scans a TeamSpeak 3 server for insecure HTTP links.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2018 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://composer.random-host.com
 */
class Scan
{
    /**
     * Each character in this string will be used as option characters and
     * matched against options passed to the script starting with a single
     * hyphen (-). For example, an option string "x" recognizes an option -x.
     * Only a-z, A-Z and 0-9 are allowed.
     *
     * @var string
     */
    protected $shortOptions = 'h';

    /**
     * An array of options. Each element in this array will be used as option
     * strings and matched against options passed to the script starting with
     * two hyphens (--). For example, an longopts element "opt" recognizes an
     * option --opt.
     *
     * @var array
     */
    protected $longOptions
        = array(
            'help',
            'user:',
            'password:',
            'host:',
            'queryport:',
            'serverport:',
        );

    /**
     * Array of option / argument pairs.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Array of required option / argument pairs.
     *
     * @var array
     */
    protected $requiredOptions
        = array(
            'user',
            'password',
            'host',
        );

    /**
     * Server properties to be scanned.
     *
     * @var string[]
     */
    protected $serverProperties
        = array(
            'virtualserver_hostbutton_url' => 'Host Button Link URL',
            'virtualserver_hostbutton_gfx_url' => 'Host Button Image URL',
            'virtualserver_hostbanner_url' => 'Host Banner Link URL',
            'virtualserver_hostbanner_gfx_url' => 'Host Banner Image URL',
            'virtualserver_welcomemessage' => 'Welcome Message',
            'virtualserver_hostmessage' => 'Host Message',
        );

    /**
     * Returns available short options.
     *
     * @return string
     */
    public function getShortOptions()
    {
        return (string)$this->shortOptions;
    }

    /**
     * Returns available long options.
     *
     * @return array
     */
    public function getLongOptions()
    {
        return $this->longOptions;
    }

    /**
     * Sets command line options as returned by getopt().
     *
     * @param array $options Command line options.
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Performs the scan.
     *
     * @return $this
     */
    public function run()
    {
        return $this
            ->preRun()
            ->scan();
    }

    /**
     * Scans the TeamSpeak 3 server for insecure links.
     *
     * @return $this
     */
    protected function scan()
    {
        $options = $this->options;

        echo <<<EOT
Scanning:
 - Host: {$options['host']}
 - Port: {$options['serverport']}
 - Query Port: {$options['queryport']}
 - User: {$options['user']}
 - Password: ******


EOT;

        /**
         * Connect to TeamSpeak 3 Server.
         *
         * @var $server TeamSpeak3_Node_Server
         */
        $uri = sprintf(
            'serverquery://%1$s:%2$s@%3$s:%4$s/?server_port=%5$s',
            $options['user'],
            $options['password'],
            $options['host'],
            $options['queryport'],
            $options['serverport']
        );
        $server = TeamSpeak3::factory($uri);

        /**
         * Scan server properties
         */
        $affectedServerProperties = array();
        foreach ($this->serverProperties as $propertyKey => $propertyName) {
            if (false !== strpos($server[$propertyKey], 'http://')) {
                $affectedServerProperties[] = $propertyName;
            }
        }

        /**
         * Scan channels.
         *
         * @var $channel TeamSpeak3_Node_Channel
         */
        $affectedChannels = array();
        foreach ($server->channelList() as $channel) {
            if (false !== strpos($channel->channel_description, 'http://')) {
                $affectedChannels[] = $channel->channel_name;
            }
        }

        /**
         * Print result.
         */
        if (empty($affectedChannels) && empty($affectedServerProperties)) {
            echo 'No HTTP links found.'.PHP_EOL;

            return $this;
        }

        if (!empty($affectedServerProperties)) {
            echo 'The following server properties contain insecure HTTP links:'.PHP_EOL;
            foreach ($affectedServerProperties as $affectedServerProperty) {
                echo " - ${affectedServerProperty}".PHP_EOL;
            }
            echo PHP_EOL;
        }

        if (!empty($affectedChannels)) {
            echo 'The following channels contain insecure HTTP links:'.PHP_EOL;
            foreach ($affectedChannels as $affectedChannel) {
                echo " - ${affectedChannel}".PHP_EOL;
            }
            echo PHP_EOL;
        }

        echo 'Done.'.PHP_EOL;

        return $this;
    }

    /**
     * Reads command line options and performs pre-run tasks.
     *
     * @return $this
     */
    protected function preRun()
    {
        if (array_key_exists('help', $this->options) || array_key_exists('h', $this->options)) {
            $this->displayHelp();
        }

        $this->checkRequiredParameters();

        return $this;
    }

    /**
     * Checks if all required parameters are set.
     *
     * @return $this
     *
     * @throws InvalidArgumentException Thrown in case of missing required arguments.
     */
    protected function checkRequiredParameters()
    {
        $missing = array_diff(
            $this->requiredOptions,
            array_keys($this->options)
        );
        if (!empty($missing)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Missing required parameters: %s',
                    implode(', ', $missing)
                )
            );
        }

        if (!array_key_exists('queryport', $this->options)) {
            $this->options['queryport'] = 10011;
        }

        if (!array_key_exists('serverport', $this->options)) {
            $this->options['serverport'] = 9987;
        }

        return $this;
    }

    /**
     * Displays a help message.
     */
    protected function displayHelp()
    {
        echo <<<EOT
Scans a TeamSpeak 3 server for insecure HTTP links.

--user       Query account login name
--password   Query account password
--host       Host name
--queryport  Optional: Query port (default: 10011)
--serverport Optional: Voice port (default: 9987)
EOT;
        exit;
    }
}
