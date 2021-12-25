<?php

if (! function_exists('mode2array')) {
    /**
     * Parses a perceived octal mode into an array of permissions.
     *
     * @see https://caboodle.tech/blog/21/06/2017/trusting-user-input-in-phps-chmod-decimal-vs-octal/
     *
     * @throws RuntimeException
     */
    function mode2array(int $mode): array
    {
        if (! is_octal($mode)) {
            throw new RuntimeException($mode . ' is not a valid octal mode.');
        }

        $permissions['domain']['read']    = (bool) ($mode & 04000);
        $permissions['domain']['write']   = (bool) ($mode & 02000);
        $permissions['domain']['execute'] = (bool) ($mode & 01000);

        $permissions['user']['read']    = (bool) ($mode & 00400);
        $permissions['user']['write']   = (bool) ($mode & 00200);
        $permissions['user']['execute'] = (bool) ($mode & 00100);

        $permissions['group']['read']    = (bool) ($mode & 00040);
        $permissions['group']['write']   = (bool) ($mode & 00020);
        $permissions['group']['execute'] = (bool) ($mode & 00010);

        $permissions['world']['read']    = (bool) ($mode & 00004);
        $permissions['world']['write']   = (bool) ($mode & 00002);
        $permissions['world']['execute'] = (bool) ($mode & 00001);

        return $permissions;
    }
}

if (! function_exists('is_octal')) {
    /**
     * Converts a perceived octal mode (of 4-digits or less) to a decimal
     * and then back to check if it really is an octal.
     *
     * @see https://caboodle.tech/blog/21/06/2017/trusting-user-input-in-phps-chmod-decimal-vs-octal/
     */
    function is_octal(int $octal): bool
    {
        if ($octal < 0 || $octal > 4095) {
            return false;
        }

        return octdec(decoct($octal)) === $octal;
    }
}
