<?php
class FakeMySQLPDO
{
    var $dbh;

    function FakeMySQLPDO($dsn, $user, $pass)
    {
        list($type, $params) = explode(':', $dsn);
        $host = $name = '';
        foreach (explode(';', $params) as $param)
        {
            list($key, $val) = explode('=', $param);
            $$key = $val;
        }
        if (isset($unix_socket))
        {
            $host .= ":$unix_socket";
        }
        $this->dbh = @mysql_pconnect($host, $user, $pass);
        if ($this->dbh)
        {
            if (!@mysql_select_db($dbname, $this->dbh))
            {
                throw new FakeMySQLPDOException(mysql_error($this->dbh),
                    $this->errorCode());
            }
        }
        else
        {
            throw new FakeMySQLPDOException(mysql_error(), '');
        }
    }

    function sqlstate_for_mysql_errno($errno)
    {
        static $sqlstate = array(
            '1146' => '42S02', // Table not found
            '1062' => '23000', // Duplicate entry %s for key %d
        );
        $out = 'HY000'; // General error
        if (array_key_exists($errno, $sqlstate))
        {
            $out = $sqlstate[$errno];
        }
        error_log("SQLSTATE for MySQL error number $errno = $out");

        return $out;
    }

    function exec($sql)
    {
        // TODO - handle errors
        if ($this->dbh)
        {
            mysql_query($sql, $this->dbh);
            return mysql_affected_rows($this->dbh);
        }
        return FALSE;
    }

    function prepare($sql)
    {
        if ($this->dbh)
        {
            $stmt =& new FakeMySQLPDOStatement($sql);
            $stmt->dbh =& $this->dbh;
            return $stmt;
        }
        return FALSE;
    }

    function close()
    {
        // TODO - handle errors?
        if ($this->dbh)
        {
            mysql_close($this->dbh);
        }
    }

    function errorInfo()
    {
        if (!$this->dbh)
        {
            return NULL;
        }
        $errno = mysql_errno($this->dbh);
        return array(
            $this->errorCode(),
            $errno,
            mysql_error($this->dbh)
        );
    }

    function errorCode()
    {
        $errno = mysql_errno($this->dbh);
        return FakeMySQLPDO::sqlstate_for_mysql_errno($errno);
    }
}

class FakeMySQLPDOStatement
{
    var $sql;
    var $resultset;

    function FakeMySQLPDOStatement($sql)
    {
        $this->sql = $sql;
        $this->resultset = NULL;
    }

    function errorInfo()
    {
        if (!$this->dbh)
        {
            return NULL;
        }
        $errno = mysql_errno($this->dbh);
        return array(
            $this->errorCode(),
            $errno,
            mysql_error($this->dbh)
        );
    }

    function errorCode()
    {
        $errno = mysql_errno($this->dbh);
        return FakeMySQLPDO::sqlstate_for_mysql_errno($errno);
    }

    function execute($params=array())
    {
        if (!$this->dbh)
        {
            return FALSE;
        }
        foreach ($params as $key => &$val)
        {
            $val = mysql_real_escape_string($val, $this->dbh);
            if (!is_numeric($val))
            {
                $val = "'$val'";
            }
        }
        $sql = strtr($this->sql, $params);
        error_log("Executing query: $sql");
        $this->resultset = @mysql_query($sql, $this->dbh);
        if (!$this->resultset)
        {
            throw new FakeMySQLPDOException(mysql_error($this->dbh),
                $this->errorCode());
        }
        $ret = array();
        if (strpos($sql, 'INSERT') === 0)
        {
            // get last insert id
            $ret['last_insert_id'] = @mysql_insert_id($this->dbh);
        }
        if (strpos($sql, 'UPDATE') === 0 or strpos($sql, 'DELETE') === 0)
        {
            $ret['affected_rows'] = @mysql_affected_rows($this->dbh);
        }
        return $ret;
    }

    function fetch()
    {
        if (!$this->resultset)
        {
            return FALSE;
        }
        return @mysql_fetch_assoc($this->resultset);
    }

    function fetchAll()
    {
        @mysql_data_seek($this->resultset, 0);
        $results = array();
        while ($row = $this->fetch())
        {
            $results[] = $row;
        }
        error_log('Result set: '.print_r($results, TRUE));
        return $results;
    }
}

class FakeMySQLPDOException extends Exception
{
    function __construct($msg, $code)
    {
        parent::__construct($msg);
        $this->code = $code;
    }
}
