<?php
/*
Plugin Name: WP-Redirection
Plugin URI: http://phkcorp.com?do=wordpress
Description: A Wordpress plugin for redirecting/rewriting url's to a new host url. Does handle multi-site hostings.
Author: PHK Corporation
Version: 1.0.2
Author URI: http://phkcorp.com

Copyright 2011  PHK Corporation  (email : phkcorp2005@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

function addWPRedirectionManagementPage()
{
	add_options_page('WP-Redirection','WP-Redirection',8,'wp-redirection','displayWPRedirectionManagementPage');
}

function displayWPRedirectionManagementPage()
{
	global $wpdb;

	if (is_admin())
	{


		if (isset($_POST["op"]))
		{
			if ($_POST["op"] == "new")
			{
				if (isset($_POST['brd_host']) && isset($_POST['brd_path']) && isset($_POST['brd_new_host']))
				{
					$url = parse_url($_POST['brd_host']);
					if (isset($url['host']))
					{
						$brd_path = $_POST['brd_path'];
						$brd_map = (isset($_POST['brd_map']) ? $_POST['brd_map'] : '');
						$brd_new_host = $_POST['brd_new_host'];
						$brd_code = $_POST['brd_code'];

						if ($brd_map != "" && $brd_new_host != "" && $brd_code != "")
						{
							$query = "INSERT INTO ".$wpdb->prefix."redirection (host,path,new_host,map,code) VALUES ('".$url['host']."','".$brd_path."','$brd_new_host','$brd_map','$brd_code')";
							$wpdb->query($query);
							echo "<div class='updated fade'><p>WP-Redirection parameters saved.</p></div>";
						}
					}
					else
					{
						echo "<div class='updated fade'><p>No host detected/entered. Must use protocol (e.g. http://,https://,etc) and/or new path cannot be empty.</p></div>";
					}
				}
			}
			else if ($_POST["op"] == "edit")
			{
				$error_msg = "";
				$id = $_POST["id"];
				if (count($_POST["host"]) > 1)
				{
					$host = $_POST["host"];
					$path = $_POST["path"];
					$new_host = $_POST["new_host"];
					$bucket = $_POST["bucket"];
					$code = $_POST["code"];
					$tid = $_POST["tid"];
					for ($i=0; $i<count($host); $i++)
					{
						if ($tid[$i] == $id)
						{
							if ($bucket[$i] != "")
							{
								$query = "UPDATE ".$wpdb->prefix."redirection SET host='$host[$i]',path='$path[$i]',new_host='$new_host[$i]',map='$bucket[$i]',code='$code[$i]' WHERE id='$id'";
							}
							else
							{
								$error_msg = "<div class='updated fade'>New path cannot be empty.</p></div>";
							}
						}
					}
				}
				else
				{
					$host = $_POST["host"][0];
					$path = $_POST["path"][0];
					$new_host = $_POST["new_host"][0];
					$bucket = $_POST["bucket"][0];
					$code = $_POST["code"][0];
					if ($bucket != "")
					{
						$query = "UPDATE ".$wpdb->prefix."redirection SET host='$host',path='$path',new_host='$new_host',map='$bucket',code='$code' WHERE id='$id'";
					}
					else
					{
						$error_msg = "<div class='updated fade'>New path cannot be empty.</p></div>";
					}
				}
				if ($error_msg == "")
				{
					$wpdb->query($query);
					echo "<div class='updated fade'><p>WP-Redirection parameters saved.</p></div>";
				}
				else
				{
					echo $error_msg;
				}
			}
			else if ($_POST["op"] == "delete")
			{
				$id = $_POST["id"];
				$query = "DELETE FROM ".$wpdb->prefix."redirection WHERE id='$id'";
				$wpdb->query($query);
				echo "<div class='updated fade'><p>WP-Redirection parameter successfully deleted.</p></div>";
			}
		}


		$t = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."redirection");
?>
		<script type="text/javascript">
			function editItem()
			{
				var item_checked = false;

				if (document.redirect.item.length != null)
				{
					for (i=0; i<document.redirect.item.length; i++)
					{
						if (document.redirect.item[i].checked == true)
						{
							document.redirect.id.value = document.redirect.item[i].value;
							item_checked = true;
						}
					}
				}
				else
				{
					if (document.redirect.item.checked == true)
					{
						document.redirect.id.value = document.redirect.item.value;
						item_checked = true;
					}

				}

				if (item_checked == true)
				{
					document.redirect.op.value = "edit";
					document.redirect.submit();
				}
				else
				{
					alert("No item checked!");
				}
			}

			function deleteItem(id)
			{
				var item_checked = false;

				if (window.confirm("Delete"))
				{
					if (document.redirect.item.length)
					{
						for (i=0; i<document.redirect.item.length; i++)
						{
							if (document.redirect.item[i].checked == true)
							{
								document.redirect.id.value = document.redirect.item[i].value;
								item_checked = true;
							}
						}
						if (item_checked == true)
						{
							document.redirect.op.value = "delete";
							document.redirect.submit();
						}
						else
						{
							alert("No item checked!");
						}
					}
					else
					{
						if (document.redirect.item.checked == true)
						{
							document.redirect.id.value = document.redirect.item.value;
							document.redirect.op.value = "delete";
							document.redirect.submit();
						}
						else
						{
							alert("No item checked!");
						}
					}
				}
			}

			function newItem()
			{
				document.redirect.op.value = "new";
				document.redirect.submit();
			}
		</script>
		<div class="wrap">
			<h2>WP-Redirection</h2>

			<form name="redirect" method="post">
				<fieldset class='options'>
					<legend><h2><u>Settings</u></h2></legend>
					<table class="editform" cellspacing="2" cellpadding="5" width="100%">
						<tr>
							<th width="30%" valign="top" align="right" style="padding-top: 5px;">
								Original Host:
							</th>
							<td>
								<input type='text' size='30' maxlength='80' name='brd_host' id='brd_host' value='' />&nbsp;<i><small>The original host with protocol (e.g. http://www.phkcorp.com or http://regex)</small></i>
							</td>
						</tr>
						<tr>
							<th width="30%" valign="top" align="right" style="padding-top: 5px;">
								Original Path:
							</th>
							<td>
								<input type='text' size='30' maxlength='80' name='brd_path' id='brd_path' value='' />&nbsp;<i><small>The original path (file opt.) or regex (e.g. /path_name or /\/pathname\/(\S+[.html])/i)</small></i>
							</td>
						</tr>
						<tr>
							<th width="30%" valign="top" align="right" style="padding-top: 5px;">
								New Host:
							</th>
							<td>
								<input type='text' size='30' maxlength='80' name='brd_new_host' id='brd_new_host' value='' />&nbsp;<i><small>The new host to redirect w/o protocol (e.g. www.phkcorp.com)</small></i>
							</td>
						</tr>
						<tr>
							<th width="30%" valign="top" align="right" style="padding-top: 5px;">
								New Path:
							</th>
							<td>
								<input type='text' size='30' maxlength='80' name='brd_map' id='brd_map' value='' />&nbsp;<i><small>The new path for the new host (e.g. new_path, &lt;Can be blank&gt;)</small></i>
							</td>
						</tr>
						<tr>
							<th width="30%" valign="top" align="right" style="padding-top: 5px;">
								Redirection Code:
							</th>
							<td>
								<input type='text' size='5' maxlength='80' name='brd_code' id='brd_code' value='' />&nbsp;<i><small>The redirect code (e.g 301 or 302)</small></i>
							</td>
						</tr>
						<tr>
							<th>&nbsp;</th>
							<td colspan="2">
								<small>
								<i>
									<ul>
										<li>-Leave off the trailing slash on the paths</li>
										<li>-Regex with matching results will be redirected to the new path</li>
										<li>-Enter http://regex in the original host field to specify a full regex of the host/path/file match in the original path field</li>
									</ul>
								</i>
								</small>
							</td>
						</tr>
						<tr>
							<th>&nbsp;</th>
							<td colspan="2">
							<p class="submit"><input type='button' name='wp_redirection_update' value='Save' onClick='newItem()' /></p>
							</td>
						</tr>
					</table>
				</fieldset>

<?php

			if (count($t) > 0)
			{
				echo '<hr>';
				echo "<table border='0'>";
				echo "<th>Original Host</th><th>Original Path</th><th>New Host</th><th>New Path</th><th>Redirect Code</th>";
				foreach ($t as $brd)
				{
					echo "<tr>";
					echo "<td><input type='text' name='host[]' value='".$brd->host."'></td>\n";
					echo "<td><input type='text' size='50' name='path[]' value='".$brd->path."'></td>\n";
					echo "<td><input type='text' name='new_host[]' value='".$brd->new_host."'></td>\n";
					echo "<td><input type='text' name='bucket[]' value='".$brd->map."'></td>\n";
					echo "<td><input type='text' size='3' name='code[]' value='".$brd->code."'></td>\n";
					echo "<td><input type='radio' name='item' value='".$brd->id."'><input type='hidden' name='tid[]' value='".$brd->id."'></td>\n";
					echo "</tr>";
				}
				echo "<tr><td colspan='5' align='right'><input type='button' name='edit' value='Edit' onClick='editItem()'><input type='button' name='delete' value='Delete' onClick='deleteItem()'></td></tr>";
				echo "<input type='hidden' name='id'>";
				echo "</table>";
			}
			echo "<input type='hidden' name='op'>";
			echo "</form>";

?>
			<h2><u>Instructions for Enabling URL Redirection</u></h2>
			<ol>
				<li>Use the Domain Mapping plugin from the Tools menu to specify the original sub-domains that require redirection</li>
				<li>Modify the hosts file (full subdomains for test/wildcard for production) with the sub-domains requiring redirection. (Modify DNS for production with the original sub-domains)</li>
				<li>Complete the above URL redirection</li>
			</ol>

			<h2><u>Regular Expression Assistance</u></h2>
			<p>Regular Expression testing tool at <a href="http://regex.larsolavtorvik.com/" target="_blank">http://regex.larsolavtorvik.com/</a>&nbsp;<i>(opens in new window)</i></p>
			<table>
				<tr>
					<td valign="top">
						<table>
							<th>Anchors</th>
							<tr><td>^</td><td>Start of line</td></tr>
							<tr><td>\A</td><td>Start of string</td></tr>
							<tr><td>$</td><td>End of line</td></tr>
							<tr><td>\Z</td><td>End of string</td></tr>
							<tr><td>\b</td><td>Word boundary</td></tr>
							<tr><td>\B</td><td>Not word boundary</td></tr>
							<tr><td>\&lt;</td><td>Start of word</td></tr>
							<tr><td>\&gt;</td><td>End of word</td></tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<th>Character Classes</th>
							<tr><td>\c</td><td>Control character</td></tr>
							<tr><td>\s</td><td>White space</td></tr>
							<tr><td>\S</td><td>Not white space</td></tr>
							<tr><td>\d</td><td>Digit</td></tr>
							<tr><td>\D</td><td>Not digit</td></tr>
							<tr><td>\w</td><td>Word</td></tr>
							<tr><td>\W</td><td>Not word</td></tr>
							<tr><td>\xhh</td><td>Hexadecimal character hh</td></tr>
							<tr><td>\Oxxx</td><td>Octal character xxx</td></tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<th>POSIX Character Classes</th>
							<tr><td>[:upper:]</td><td>Upper case letters</td></tr>
							<tr><td>[:lower:]</td><td>Lower case letters</td></tr>
							<tr><td>[:alpha:]</td><td>All letters</td></tr>
							<tr><td>[:digit:]</td><td>Digits and letters</td></tr>
							<tr><td>[:xdigit:]</td><td>Hexadecimal digits</td></tr>
							<tr><td>[:punct:]</td><td>Punctuation</td></tr>
							<tr><td>[:blank:]</td><td>Space and tab</td></tr>
							<tr><td>[:space:]</td><td>Blank characters</td></tr>
							<tr><td>[:cntrl:]</td><td>Control characters</td></tr>
							<tr><td>[:graph:]</td><td>Printed characters</td></tr>
							<tr><td>[:print:]</td><td>Printed characters and spaces</td></tr>
							<tr><td>[:word:]</td><td>Digits, letters and underscore</td></tr>
						</table>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<table>
							<th>Assertions</th>
							<tr><td>?=</td><td>Lookahead assertion</td></tr>
							<tr><td>?!</td><td>Negative lookahead</td></tr>
							<tr><td>?&lt;=</td><td>Lookbehind assertion</td></tr>
							<tr><td>?!= or ?&lt;!</td><td>Negative lookbehind</td></tr>
							<tr><td>?&gt;</td><td>Once-only Subexpression</td></tr>
							<tr><td>?()</td><td>Condition [if then]</td></tr>
							<tr><td>?()|</td><td>Condition [if then else]</td></tr>
							<tr><td>?#</td><td>Comment</td></tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<th>Quantifiers</th>
							<tr><td>*</td><td>0 or more</td></tr>
							<tr><td>*?</td><td>0 or more, ungreedy</td></tr>
							<tr><td>+</td><td>1 or more</td></tr>
							<tr><td>+?</td><td>1 or more, ungreedy</td></tr>
							<tr><td>?</td><td>0 or 1</td></tr>
							<tr><td>??</td><td>0 or 1, ungreedy</td></tr>
							<tr><td>{3}</td><td>Exactly 3</td></tr>
							<tr><td>{3,}</td><td>3 or more</td></tr>
							<tr><td>{3,5}</td><td>3, 4, or 5</td></tr>
							<tr><td>{3,5}?</td><td>3, 4 or 5, ungreedy</td></tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<th>Special Characters</th>
							<tr><td>\</td><td>Escape Character</td></tr>
							<tr><td>\n</td><td>New line</td></tr>
							<tr><td>\r</td><td>Carriage return</td></tr>
							<tr><td>\t</td><td>Tab</td></tr>
							<tr><td>\v</td><td>Vertical tab</td></tr>
							<tr><td>\f</td><td>Form feed</td></tr>
							<tr><td>\a</td><td>Alarm</td></tr>
							<tr><td>[\b]</td><td>Backspace</td></tr>
							<tr><td>\e</td><td>Escape</td></tr>
							<tr><td>\N{name}</td><td>Name Character</td></tr>
						</table>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<table>
							<th>String Replacement</th><th>(Backreferences)</th>
							<tr><td>$n</td><td>nth non-passive group</td></tr>
							<tr><td>$2</td><td>"xyz" in /^(abc(xyz))$/</td></tr>
							<tr><td>$1</td><td>"xyz" in /^(?:abc)(xyz)$/</td></tr>
							<tr><td>$`</td><td>Before matching string</td></tr>
							<tr><td>$'</td><td>After matching string</td></tr>
							<tr><td>$+</td><td>Last matched string</td></tr>
							<tr><td>$&</td><td>Entire matched string</td></tr>
							<tr><td>$_</td><td>Entire input string</td></tr>
							<tr><td>$$</td><td>Literal "$"</td></tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<th>Ranges</th>
							<tr><td>.</td><td>Any character except new line (\n)</td></tr>
							<tr><td>(a|b)</td><td>a or b</td></tr>
							<tr><td>(...)</td><td>Group</td></tr>
							<tr><td>(?:...)</td><td>Passive Group</td></tr>
							<tr><td>[abc]</td><td>Range (a or b or c)</td></tr>
							<tr><td>[^abc]</td><td>Not a or b or c</td></tr>
							<tr><td>[a-q]</td><td>Letter between a and q</td></tr>
							<tr><td>[A-Q]</td><td>Upper case letter between A and Q</td></tr>
							<tr><td>[0-7]</td><td>Digit between 0 and 7</td></tr>
							<tr><td>\n</td><td>nth group/subpattern</td></tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<th>Pattern Modifiers</th>
							<tr><td>g</td><td>Global match</td></tr>
							<tr><td>i</td><td>Case-insensitive</td></tr>
							<tr><td>m</td><td>Multiple lines</td></tr>
							<tr><td>s</td><td>Treat string as single line</td></tr>
							<tr><td>x</td><td>Allow comments and white space pattern</td></tr>
							<tr><td>e</td><td>Evaluate replacement</td></tr>
							<tr><td>U</td><td>Ungreedy pattern</td></tr>
						</table>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<table>
							<th colspan="3">Pattern Modifiers</th>
							<tr><td>^</td><td>[</td><td>.</td></tr>
							<tr><td>$</td><td>{</td><td>*</td></tr>
							<tr><td>(</td><td>\</td><td>+</td></tr>
							<tr><td>)</td><td>|</td><td>?</td></tr>
							<tr><td>&lt;</td><td>&gt;</td><td>&nbsp;</td></tr>
						</table>
					</td>
					<td valign="top" colspan="2">
						<table>
							<th colspan="2" align="left">Pattern Modifiers</th>
							<tr><td>/[A-Za-z0-9-]+/</td><td>Letters, numbers and hyphens</td></tr>
							<tr><td>/\d{1,2}\/\d{1,2}\/\d{4}/</td><td>Date (e.g. 21/3/2006)</td></tr>
							<tr><td>/[^\s]+(?=\.(jpg|gif|png))\.\2/</td><td>jpg, gif or png image</td></tr>
							<tr><td>/(^[1-9]{1}$|^[1-4]{1}[0-9]{1}$|^50$)/</td><td>Any number from 1 to 50 inclusive</td></tr>
							<tr><td>/#?([A-Fa-f0-9]){3}(([A-Fa-f0-9]){3})?/</td><td>Valid hexadecimal colour code</td></tr>
							<tr><td>/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,15}/</td><td>8 to 15 character string with at least one upper case letter, one lower case letter, and one digit (useful for password).</td></tr>
							<tr><td>/\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6}/</td><td>Email address</td></tr>
							<tr><td>\&lt;(/?[^\&gt;]+)\&gt;</td><td>HTML Tags</td></tr>
							<tr><td>/^$|(.?)+(.)/ or /^(.*)$/</td><td>To catch anything in the path</td></tr>
							<tr><td>/\/my-path\/+([a-z0-9._-])+(.html|.asp)/</td><td>A fixed path with any html or asp file</td></tr>
						</table>
					</td>
				</tr>
			</table>
<?php
	}
}

/*
 * The redirect function
 */
function phkcorp_redirect()
{
	global $wpdb;

	if (!is_admin())
	{
		error_reporting(0);

		$request = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$url = parse_url($request);
		$scheme = $url["scheme"];

		/*
		 * Scenarios:
		 * ---------
		 * 1. Redirect host name only to channel
		 * 2. Redirect host name & path regardless of file to channel
		 * 3. Redirect host name & path with a file to a channel
		 * 4. Redirect the result of a regex match to a channel
		 *
		 */

		/*
		 * Find records ONLY which match the incoming host and the original host?
		 */
		$query = "SELECT * FROM ".$wpdb->prefix."redirection WHERE host='".$url['host']."'";
		$results = $wpdb->get_results($query);

		if ($wpdb->num_rows > 0)
		{
			/*
			 * Records found for this incoming host and original host!
			 */
			$match_found = 0;
			$redirect = array();

			foreach($results as $pattern)
			{
				/*
				 * $pattern->path can be (1) path, (2) path/file, (3) nothing, or (4) regular expression
				 *
				 * To catch anything in the path and perform the redirection, use the regex /^$|(.?)+(.)/ or /^(.*)$/
				 */
				$res = preg_replace($pattern->path, $pattern->map, $url["path"]);
				/*
				 * If regex is match, then map is return in $res, otherwise $url["path"] is returned in $res when path is a regex
				 * otherwise $res is empty/null. IMPORTANT! $res must not be empty for regex match
				 * A check is performed on non-empty $res otherwise, site goes into an endless redirecting loop!
				 */
				if (!strcmp($res,$pattern->map) && $res != '') {
					/*
					 * Regex match found
					 */
					$redirect['scheme'] = $scheme;
					$redirect['host'] = $pattern->new_host;
					$redirect['bucket'] = (!strcmp($pattern->map,"/") ? "" : $pattern->map );
					$redirect['code'] = $pattern->code;
					$match_found = 1;
				}
				else
				{
					/*
					 * Uses the original matching criteria
					 */
					$brd_path = $url["path"];
					if ($brd_path[strlen($brd_path)-1] == '/') $brd_path = substr($brd_path,0,strlen($brd_path)-1);
					$url['path'] = $brd_path;

					if ($pattern->path == $url["path"])
					{
						/*
						 * Redirects an exact path match (path & file)
						 */
						$redirect['scheme'] = $scheme;
						$redirect['host'] = $pattern->new_host;
						$redirect['bucket'] = (!strcmp($pattern->map,"/") ? "" : $pattern->map );
						$redirect['code'] = $pattern->code;
						$match_found = 1;
					}
					else if (strstr($url["path"],$pattern->path) != FALSE)
					{
						/*
						 * Redirects a path only (no file) match
						 */
						$redirect['scheme'] = $scheme;
						$redirect['host'] = $pattern->new_host;
						$redirect['bucket'] = (!strcmp($pattern->map,"/") ? "" : $pattern->map );
						$redirect['code'] = $pattern->code;
						$match_found = 1;
					}
				}
				if ($match_found) break;
			}

			if ($match_found)
			{
				$new_url = $redirect['scheme']."://".$redirect['host']."/".$redirect['bucket'];
				wp_redirect($new_url, $redirect['code']);
				exit;
			}
			else
			{
				$query = "SELECT * FROM ".$wpdb->prefix."redirection WHERE host='regex'";
				$results = $wpdb->get_results($query);

				$match_found = 0;
				$redirect = array();

				if ($wpdb->num_rows > 0)
				{
					foreach($results as $pattern)
					{
						$res = preg_replace($pattern->path, $pattern->map, $request);
						if (!strcmp($res,$pattern->map) && $res != '') {
							/*
							 * Regex match found
							 */
							$redirect['scheme'] = $scheme;
							$redirect['host'] = $pattern->new_host;
							$redirect['bucket'] = (!strcmp($pattern->map,"/") ? "" : $pattern->map );
							$redirect['code'] = $pattern->code;
							$match_found = 1;
						}
						if ($match_found) break;
					}
				}

				if ($match_found)
				{
					$new_url = $redirect['scheme']."://".$redirect['host']."/".$redirect['bucket'];
					wp_redirect($new_url, $redirect['code']);
					exit;
				}
			}
		}
		else
		{
			$query = "SELECT * FROM ".$wpdb->prefix."redirection WHERE host='regex'";
			$results = $wpdb->get_results($query);

			$match_found = 0;
			$redirect = array();

			if ($wpdb->num_rows > 0)
			{
				foreach($results as $pattern)
				{
					$res = preg_replace($pattern->path, $pattern->map, $request);
					if (!strcmp($res,$pattern->map) && $res != '') {
						/*
						 * Regex match found
						 */
						$redirect['scheme'] = $scheme;
						$redirect['host'] = $pattern->new_host;
						$redirect['bucket'] = (!strcmp($pattern->map,"/") ? "" : $pattern->map );
						$redirect['code'] = $pattern->code;
						$match_found = 1;
					}
					if ($match_found) break;
				}
			}

			if ($match_found)
			{
				$new_url = $redirect['scheme']."://".$redirect['host']."/".$redirect['bucket'];
				wp_redirect($new_url, $redirect['code']);
				exit;
			}
		}
	}
}

//
// Hooks
//
add_action('admin_menu', 'addWPRedirectionManagementPage');
add_action('init','phkcorp_redirect');


function wp_redirection_activate() {
    global $wpdb;
    
    $sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."redirection (
              id int(11) NOT NULL AUTO_INCREMENT,
              host varchar(255) NOT NULL,
              path varchar(255) NOT NULL,
              new_host varchar(255) NOT NULL,
              map varchar(255) NOT NULL,
              code varchar(255) NOT NULL,
              PRIMARY KEY (id)
                    )";
    $wpdb->query($sql);

    
}

function wp_redirection_deactivate() {
    global $wpdb;
    
    if (get_option('wp_redirection_delete')) {
        $sql = 'DROP TABLE '.$wpdb->prefix.'redirection;';
        $wpdb->query($sql);
    }
    $sql = 'DROP TABLE IF EXISTS '.$wpdb->prefix.'wp_redirection;';
    $wpdb->query($sql);
    
    remove_action('init','phkcorp_redirect');
}
register_activation_hook(__FILE__, 'wp_redirection_activate');
register_deactivation_hook(__FILE__, 'wp_redirection_deactivate');

?>
