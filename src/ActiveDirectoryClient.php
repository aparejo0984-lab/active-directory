<?php

class ActiveDirectoryClient
{
    private $server;
    private $port;
    private $username;
    private $password;
    private $query;
    private $base;

    public function __construct($server, $port, $username, $password, $base, $query)
    {
        $this->server = $server;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->query = $query;
        $this->base = $base;
    }

    /**
     * @throws ErrorException
     */
    public function connect()
    {
        try {
            $ldap = ldap_connect($this->server,$this->port, $this->username,$this->password);
            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_bind($ldap, $this->username, $this->password);
            return $ldap;
        } catch (\Exception $exception) {
            throw new ErrorException(sprintf('Exception code: %s, message %s', $exception->getCode(), $exception->getMessage()));
        }
    }

    /**
     * @throws ErrorException
     */
    public function execute($ldap)
    {
        try {
            return ldap_search($ldap,$this->ldap_base, $this->query);
        } catch (\Exception $exception) {
            throw new ErrorException(sprintf('Exception code: %s, message %s', $exception->getCode(), $exception->getMessage()));
        }
    }


    /**
     * @throws ErrorException
     */
    public function fetchUsers()
    {
        $ldap = $this->connect();
        $search = $this->execute($ldap);
        $entry = ldap_first_entry($ldap, $search);

        $results = array();

        do {
            $results[] = $this->activeDirectoryClient->formatAttributes($ldap,$entry);
        } while ($entry = ldap_next_entry($ldap, $entry));

        return $results;
    }

    /**
     * Simplify the attributes into array for easier processing
     */
    public function formatAttributes($ldap, $entry)
    {
        $attributes = array();

        $attrs = ldap_get_attributes($ldap, $entry);

        if ($attrs["count"] == 0 || !$attrs) return $attributes;

        for ($i=0; $i < $attrs["count"]; $i++) {
            $attr_name = $attrs[$i];
            unset($attrs[$attr_name]['count']);
            $attributes[$attr_name] = $attrs[$attr_name];
        }
        return $attributes;
    }
}
