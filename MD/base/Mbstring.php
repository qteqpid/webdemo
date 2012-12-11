<?php

	
	if( ! function_exists('mb_internal_encoding') )
	{
		function mb_internal_encoding($enc=FALSE)
		{
			if( function_exists('iconv_set_encoding') ) {
				if($enc) {
					iconv_set_encoding('internal_encoding', $enc);
				}
				return iconv_get_encoding('internal_encoding');
			}
			return '';
		}
	}
	
	if( ! function_exists('mb_strlen') )
	{
		function mb_strlen($str, $enc=FALSE)
		{
			if( function_exists('iconv_strlen') ) {
				return $enc ? iconv_strlen($str, $enc) : iconv_strlen($str);
			}
			return strlen($str);
		}
	}
	
	if( ! function_exists('mb_substr') )
	{
		function mb_substr($str, $start, $len=FALSE, $enc=FALSE)
		{
			if( function_exists('iconv_substr') ) {
				if( $enc ) {
					return $len ? iconv_substr($str, $start, $len, $enc) : iconv_substr($str, $start, $enc);
				}
				else {
					return $len ? iconv_substr($str, $start, $len) : iconv_substr($str, $start);
				}
			}
			return $len ? substr($str, $start, $len) : substr($str, $start);
		}
	}
	
	mb_internal_encoding('UTF-8');