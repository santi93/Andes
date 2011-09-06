<?php

/**
 * Parse the skin contents to retrieve skin's metadata.
 *
 * <code>
 * /*
 * Skin Name: Name of Skin
 * Skin URI: Link to skin information
 * Description: Skin Description
 * Author: Skin author's name
 * Author URI: Link to the author's web site
 * Version: Version of Skin
 *  * / # Remove the space to close comment
 * </code>
 *
 * Skin data returned array contains the following:
 *		'Name' - Name of the skin, must be unique.
 *		'Title' - Title of the skin and the link to the skin's web site.
 *		'Description' - Description of what the skin does and/or notes
 *		from the author.
 *		'Author' - The author's name
 *		'AuthorURI' - The authors web site address.
 *		'Version' - The skin version number.
 *		'SkinURI' - Skin web site address.
 *
 */

function get_skin_data( $skin_file, $type='' ) {
	// We don't need to write to the file, so just open for reading.
	$fp = fopen($skin_file, 'r');

	// Pull only the first 8kiB of the file in.
	$skin_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose($fp);

	preg_match( '|^'.$type.'Skin Name:(.*)$|mi', $skin_data, $name );
    if($name[1]) {
    	preg_match( '|Skin URI:(.*)$|mi', $skin_data, $uri );
    	preg_match( '|Version:(.*)|i', $skin_data, $version );
    	preg_match( '|Description:(.*)$|mi', $skin_data, $description );
    	preg_match( '|Author:(.*)$|mi', $skin_data, $author_name );
    	preg_match( '|Author URI:(.*)$|mi', $skin_data, $author_uri );

    	foreach ( array( 'name', 'uri', 'version', 'description', 'author_name', 'author_uri' ) as $field ) {
    		if ( !empty( ${$field} ) )
    			${$field} = trim(${$field}[1]);
    		else
    			${$field} = '';
    	}

    	$skin_data = array(
    				'Name' => $name, 'Title' => $name, 'SkinURI' => $uri, 'Description' => $description,
    				'Author' => $author_name, 'AuthorURI' => $author_uri, 'Version' => $version
    				);
    	return $skin_data;
    }
}

/**
 * Gets the basename of a skin.
 *
 * This method extracts the name of a skin from its filename.
 *
 */
function skin_basename($file) {
	$flag_options = get_option('flag_options');
	$file = str_replace('\\','/',$file); // sanitize for Win32 installs
	$file = preg_replace('|/+|','/', $file); // remove any duplicate slash
	$skin_dir = str_replace('\\','/',$flag_options['skinsDirABS']); // sanitize for Win32 installs
	$skin_dir = preg_replace('|/+|','/', $skin_dir); // remove any duplicate slash
	$mu_skin_dir = str_replace('\\','/',$flag_options['skinsDirABS']); // sanitize for Win32 installs
	$mu_skin_dir = preg_replace('|/+|','/', $mu_skin_dir); // remove any duplicate slash
	$file = preg_replace('#^' . preg_quote($skin_dir, '#') . '/|^' . preg_quote($mu_skin_dir, '#') . '/#','',$file); // get relative path from skins dir
	$file = trim($file, '/');
	return $file;
}

/**
 * Check the skins directory and retrieve all skin files with skin data.
 *
 */
function get_skins($skin_folder='', $type='') {

	$flag_options = get_option('flag_options');
	$flag_skins = array ();
	$skin_root = $flag_options['skinsDirABS'];
	if( !empty($skin_folder) )
		$skin_root = $skin_folder;

	// Files in flash-album-gallery/skins directory
	$skins_dir = @ opendir( $skin_root);
	$skin_files = array();
	if ( $skins_dir ) {
		while (($file = readdir( $skins_dir ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' )
				continue;
			if ( is_dir( $skin_root.'/'.$file ) ) {
				$skins_subdir = @ opendir( $skin_root.'/'.$file );
				if ( $skins_subdir ) {
					while (($subfile = readdir( $skins_subdir ) ) !== false ) {
						if ( substr($subfile, 0, 1) == '.' )
							continue;
						if ( substr($subfile, -4) == '.php' )
							$skin_files[] = "$file/$subfile";
					}
				}
			} else {
				if ( substr($file, -4) == '.php' )
					$skin_files[] = $file;
			}
		}
	}
	@closedir( $skins_dir );
	@closedir( $skins_subdir );

	if ( !$skins_dir || empty($skin_files) )
		return $flag_skins;

	foreach ( $skin_files as $skin_file ) {
		if ( !is_readable( "$skin_root/$skin_file" ) )
			continue;

		$skin_data = get_skin_data( "$skin_root/$skin_file", $type );

		if ( empty ( $skin_data['Name'] ) )
			continue;

		$flag_skins[skin_basename( $skin_file )] = $skin_data;
	}

	uasort( $flag_skins, create_function( '$a, $b', 'return strnatcasecmp( $a["Name"], $b["Name"] );' ));

	return $flag_skins;
}

?>