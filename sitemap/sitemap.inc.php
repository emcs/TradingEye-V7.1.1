<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/

// sitemap generator class
class Sitemap{

	// constructor receives the list of URLs to include in the sitemap
	function Sitemap($items = array()){
		$this->_items = $items;
	}
	
	
	/**
	 * Add a new sitemap item
	 *
	 * @param string url link url
	 * @param string lastmod	last modified
	 * @param float priority given priority to search engine to crawl the link. 0.1 is lowest and 1.0 is highest.
	 * @param array additional_fields	additional fields
	 * @return 
	 */	
	function addItem(   $url,
						$lastmod = '',
						$changefreq = '',
						$priority = '',
						$additional_fields = array())	{
		$this->_items[] = array_merge(array('loc' => $url,
		'lastmod' => $lastmod,
		'changefreq' => $changefreq,
		'priority' => $priority),
		$additional_fields);
	}
	
	
	/** generate Google sitemap
	 * 
	 * @return xml :google xml sitemap
	 */
	function generateGoogleSitemap(){
		ob_start();
		header('Content-type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<?xml-stylesheet type="text/xsl" href="'.SITE_URL.'sitemap/sitemap.xsl"?>';
		echo '<!-- generator="XML SITEMAP gererator for tradingeye software. Author: Dave Bui - http://www.sailboatvn.co.uk" -->';
		echo '<!-- sitemap-generator-url="http://www.sailboatvn.co.uk" xml-sitemap-generator-version="1.00" -->';		
		echo '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		foreach ($this->_items as $i){
			echo '<url>';
			foreach ($i as $index => $_i){
				if (!$_i) continue;
				echo "<$index>" . $this->_escapeXML($_i) . "</$index>";
			}
		echo '</url>';
		}
		echo '</urlset>';
		return ob_get_clean();
	}
	
	
	/** generate Yahoo sitemap
	 * 
	 * @return txt yahoo format sitemap
	 */
	function generateYahooSitemap()	{
		ob_start();
		header('Content-type: text/plain');
		
		foreach ($this->_items as $i){
			echo $i['loc'] . "\n";
		}
		return ob_get_clean();
	}
	
	/** escape string characters for inclusion in XML structure
	 * 
	 */
	function _escapeXML($str){
		$translation = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
		foreach ($translation as $key => $value)
		{
		$translation[$key] = '&#' . ord($key) . ';';
		}
		$translation[chr(38)] = '&';
		return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/","&#38;",
		strtr($str, $translation));
	}#END _escapeXML()
	
	/** convert timestamp to search engine date formate
	 * 
	 */
	function searchEngineDateFormat($timestamp){
		
		if(!empty($timestamp) || $timestamp!=0)
			{
				$time=strftime("%Y-%m-%d",$timestamp);
				return $time;
			}
			else
			{
				return "";
			}
	}
}#END CLASS
?>