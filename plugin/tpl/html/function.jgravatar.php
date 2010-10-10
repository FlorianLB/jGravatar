<?php
/**
* @package    jelix
* @subpackage jtpl_plugin
* @version    $Id$
* @author     Jouanneau Laurent
* @copyright  2005-2006 Jouanneau laurent
* @link        http://www.jelix.org
* @licence    GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/


jClasses::inc('jGravatar~jGravatar');

/**
 * function plugin :  write the image tag that display the gravatar
 *
 * @param jTpl $tpl template engine
 * @param string $selector selector action
 * @param array $params parameters for the url
 * @param boolean $escape if true, then escape the string for html
 */
function jtpl_function_html_jgravatar($tpl, $email, $gravatar_size = null, $rating = null , $alt ='')
{
	jGravatar::display($email, $gravatar_size, $rating, $alt);
}