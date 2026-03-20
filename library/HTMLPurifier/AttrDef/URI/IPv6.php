<?php

declare (strict_types=1);
/**
 * Validates an IPv6 address.
 * @author Feyd @ forums.devnetwork.net (public domain)
 * @note This function requires brackets to have been removed from address
 *       in URI.
 */
class Html_Purifier_attr_Def_uri_i_Pv6 extends Html_Purifier_attr_Def_uri_i_Pv4
{
    /**
     * @param string $aIP
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($a_ip, $config, $context)
    {
        if (!$this->ip4) {
            $this->_load_regex();
        }
        $original = $a_ip;
        $hex = '[0-9a-fA-F]';
        $pre = '(?:/(?:12[0-8]|1[0-1][0-9]|[1-9][0-9]|[0-9]))';
        // /0 - /128
        //      prefix check
        if (strpos($a_ip, '/') !== false) {
            if (preg_match('#' . $pre . '$#s', $a_ip, $find)) {
                $a_ip = substr($a_ip, 0, -strlen($find[0]));
                unset($find);
            } else {
                return false;
            }
        }
        //      IPv4-compatibility check
        if (preg_match('#(?<=:' . ')' . $this->ip4 . '$#s', $a_ip, $find)) {
            $a_ip = substr($a_ip, 0, -strlen($find[0]));
            $ip = explode('.', $find[0]);
            $ip = array_map('dechex', $ip);
            $a_ip .= $ip[0] . $ip[1] . ':' . $ip[2] . $ip[3];
            unset($find, $ip);
        }
        //      compression check
        $a_ip = explode('::', $a_ip);
        $c = count($a_ip);
        if ($c > 2) {
            return false;
        }
        if ($c == 2) {
            list($first, $second) = $a_ip;
            $first = explode(':', $first);
            $second = explode(':', $second);
            if (count($first) + count($second) > 8) {
                return false;
            }
            while (count($first) < 8) {
                array_push($first, '0');
            }
            array_splice($first, 8 - count($second), 8, $second);
            $a_ip = $first;
            unset($first, $second);
        } else {
            $a_ip = explode(':', $a_ip[0]);
        }
        $c = count($a_ip);
        if ($c != 8) {
            return false;
        }
        //      All the pieces should be 16-bit hex strings. Are they?
        foreach ($a_ip as $piece) {
            if (!preg_match('#^[0-9a-fA-F]{4}$#s', sprintf('%04s', $piece))) {
                return false;
            }
        }
        return $original;
    }
}
// vim: et sw=4 sts=4