<?php

if (! function_exists('website_identity')) {
    /**
     * Get website identity data
     *
     * @param string|null $key
     * @return mixed
     */
    function website_identity($key = null) {
        $identity = app('website.identity');
        
        if ($key === null) {
            return $identity;
        }
        
        return $identity->$key ?? null;
    }
}

if (! function_exists('website_name')) {
    /**
     * Get website name
     *
     * @return string
     */
    function website_name() {
        return website_identity('name') ?? 'WebKhanza';
    }
}

if (! function_exists('website_logo')) {
    /**
     * Get website logo URL
     *
     * @return string|null
     */
    function website_logo() {
        $logo = website_identity('logo');
        return $logo ? asset('storage/' . $logo) : null;
    }
}

if (! function_exists('website_favicon')) {
    /**
     * Get website favicon URL
     *
     * @return string
     */
    function website_favicon() {
        $favicon = website_identity('favicon');
        return $favicon ? asset('storage/' . $favicon) : asset('favicon.ico');
    }
}