<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_magic_quotes_runtime(0);

function minim($config=NULL)
{
    static $instance;
    if (!$instance)
    {
        $instance = new Minim($config);
    }
    return $instance;
}

class Minim
{
    var $blocks;
    var $extends;
    var $root;
    var $debug;
    var $log_msgs;
    var $config;

    function Minim($config=NULL)
    {
        $this->blocks = array();
        $this->extends = array();
        $this->root = realpath(dirname(__FILE__).'/../');
        $this->debug = array_key_exists('debug', $_REQUEST);
        $this->log_msgs = array();
        if (is_null($config))
        {
            include "{$this->root}/config.php";
            $this->config = $config;
        }
        require $this->lib('helpers');
    }

    function log($msg)
    {
        if ($this->debug)
        {
            $this->log_msgs[] = $msg;
        }
    }

    function set_block($name, $contents)
    {
        $this->log("Setting $name block contents");
        $this->blocks[$name] = $contents;
    }

    function get_block($name)
    {
        if (array_key_exists($name, $this->blocks))
        {
            $this->log("Fetching $name block");
            return $this->blocks[$name];
        }
        $this->log("Block $name not found");
        return "";
    }

    function extend($name)
    {
        $this->log("Extending $name template");
        array_push($this->extends, $name);
        $this->log("Extends stack: ".join(" > ", $this->extends));
    }

    function render($_name, $_context=array())
    {
        ob_start();
        $_filename = "{$this->root}/templates/{$_name}.php";
        if (is_readable($_filename))
        {
            $this->log("Rendering $_name template");
            $this->log("<a href=\"#\" class=\"expanded\">Context<span>: " . print_r($_context, TRUE) . "</span></a>");
            extract($_context);
            include $_filename;
        }
        else
        {
            die("Template $_name not found at $_filename");
        }

        // render any parent templates
        if ($template = array_pop($this->extends))
        {
            $this->render($template, $_context);
        }
        elseif ($this->debug)
        {
            print <<<JAVASCRIPT
<script type="text/js">
</script>
JAVASCRIPT;
            print '<pre class="debug">'.join("\n", $this->log_msgs)."</pre>";
        }
        ob_end_flush();
    }

    function def_block($name)
    {
        ob_start();
    }

    function end_block($name)
    {
        $this->set_block($name, ob_get_clean());
    }

    function block($name)
    {
        echo $this->get_block($name);
    }

    function template($name)
    {
        return "{$this->root}/templates/{$name}.php";
    }

    function lib($name)
    {
        return "{$this->root}/lib/{$name}.php";
    }

    function fixture($name)
    {
        return "{$this->root}/fixtures/{$name}.php";
    }

    function db()
    {
        static $dbh;
        if (!$dbh)
        {
            extract($this->config['database']);
            $dsn = "mysql:dbname=$name;host=$host";
            if (isset($sock))
            {
                $dsn .= ";unix_socket=$sock";
            }
            if (FALSE) //class_exists('PDO'))
            {
                try
                {
                    $dbh = new PDO($dsn, $user, $pass);
                }
                catch (PDOException $e)
                {
                    die("Could not connect: ".$e->getMessage());
                }
            }
            else
            {
                require $this->lib('FakePDO.class');
                $dbh = new FakePDO($dsn, $user, $pass);
            }
        }
        return $dbh;
    }

    var $url_map = array();

    function url_for($_mapping, $_params)
    {
        if (array_key_exists($_mapping, $this->config['url_map']))
        {
            $_map = $this->config['url_map'][$_mapping];
            $this->log("Using URL map: $_mapping -> ".htmlspecialchars(var_export($_map, TRUE)));
            $this->log("Params: ".var_export($_params, TRUE));
            extract($_params);
            $_pat = $_map['url_pattern'];
            $_rev = preg_replace(',\(\?P<(.*?)>.*?\),e', '$$1', $_pat);
            $_rev = ltrim(rtrim($_rev, '$'), '^');
            $this->log("Mapped to URL: $_rev");
            return $_rev;
        }
        return "#error:_mapping_not_found:_$mapping";
    }

    function truncate($str, $limit=300)
    {
        // TODO - add unicode support (mb_strlen)
        if (strlen($str) < $limit)
        {
            return $str;
        }

        // cheat and use PHP's wordwrap() function to avoid splitting words
        // TODO - add unicode support (?)
        $lines = explode("\n", wordwrap($str, $limit));
        return $lines[0] . '...'; // TODO - change to horizontal ellipsis
    }
}
