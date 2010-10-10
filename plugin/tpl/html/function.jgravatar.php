<?php
/**
* @package    jGravatar
* @subpackage jtpl_plugin
* @author     Florian Lonqueu-Brochard
* @copyright  2010 Florian Lonqueu-Brochard
* @licence    MIT
*/


jClasses::inc('jGravatar~jGravatar');

/**
 * function plugin :  write the image tag that display the gravatar
 *
 * @see jGravatar::get
 */
function jtpl_function_html_jgravatar($tpl, $email, $gravatar_size = null, $rating = null , $alt ='')
{
	jGravatar::display($email, $gravatar_size, $rating, $alt);
}