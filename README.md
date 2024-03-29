# randomhost/ts3-https-scan

<!-- TOC -->
* [1. Purpose](#1-purpose)
* [2. Requirements](#2-requirements)
* [3. Installation](#3-installation)
* [4. Usage](#4-usage)
* [5. Troubleshooting](#5-troubleshooting)
* [6. License](#6-license)
<!-- TOC -->

## 1. Purpose

This package connects to a TeamSpeak 3 server via the query interface and scans
various server properties as well as channel descriptions for insecure HTTP links.

The result is printed to stdout.

**Scanned server properties:**

| Property                           | Description           |
|------------------------------------|-----------------------|
| `virtualserver_welcomemessage`     | Welcome Message       |
| `virtualserver_hostmessage`        | Host Message          |
| `virtualserver_hostbanner_url`     | Host Banner Link URL  |
| `virtualserver_hostbanner_gfx_url` | Host Banner Image URL |
| `virtualserver_hostbutton_url`     | Host Button Link URL  |
| `virtualserver_hostbutton_gfx_url` | Host Button Image URL |

## 2. Requirements

* [Composer][1] is required to install dependencies
* Valid ServerQuery login credentials
* Custom anti-flood settings or whitelisted IP address

## 3. Installation

* Run `composer install` in the root directory of the package.

## 4. Usage

Run `php src/bin/scan.php` with option  `--help` or `-h` for available options.

**Available options:**

| Option         | Mandatory | Description                   |
|----------------|-----------|-------------------------------|
| `--user`       | yes       | Query account login name      |
| `--password`   | yes       | Query account password        |
| `--host`       | yes       | Host name                     |
| `--queryport`  | no        | Query port (Default: `10011`) |
| `--serverport` | no        | Voice port (Default: `9987`)  |

**Example:**

```bash
php src/bin/scan.php --user serveradmin --password changeme --host localhost
```

**Result:**

```
Scanning:
 - Host: localhost
 - Port: 9987
 - Query Port: 10011
 - User: serveradmin
 - Password: ******

The following server properties contain insecure HTTP links:
 - Host Banner Link URL
 - Host Banner Image URL

The following channels contain insecure HTTP links:
 - Lobby
 - Gaming 2

Done.
```

## 5. Troubleshooting

The TeamSpeak 3 ServerQuery interface features a built-in flood protection
against "command spamming" which comes with rather strict default settings.

If you receive an error message like `flood ban`, this means that the IP address
of the machine you executed the script on has been temporarily banned from
accessing the ServerQuery interface.

In this case, you have the following options:

* Add the IP address of the machine executing the script to the `query_ip_whitelist.txt`
  file in the TeamSpeak 3 servers root directory to disable flood protection for
  that address. (**Recommended**)
* Increase the flood protection threshold of the server instance using the
  ServerQuery interface.  
  **Example:**  
  ```
  instanceedit serverinstance_serverquery_flood_commands=200
  ```  
  You may have to play with the values to find a suitable trade-off between
  convenience and security.

## 6. License

See [LICENSE.txt](LICENSE.txt) for full license details.


[1]: https://getcomposer.org/
