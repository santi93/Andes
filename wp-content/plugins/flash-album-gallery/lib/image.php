<?php
if ( !class_exists('flagImage') ) :
/**
* Image PHP class for the WordPress plugin FlAG Gallery
* 
*/
class flagImage{
	
	/**** Public variables ****/	
	var $errmsg			=	'';			// Error message to display, if any
	var $error			=	FALSE; 		// Error state
	var $imageURL		=	'';			// URL Path to the image
	var $thumbURL		=	'';			// URL Path to the thumbnail
	var $imagePath		=	'';			// Server Path to the image
	var $thumbPath		=	'';			// Server Path to the thumbnail
	var $href			=	'';			// A href link code
	
	// TODO: remove thumbPrefix and thumbFolder (constants)
	var $thumbPrefix	=	'thumbs_';	// FolderPrefix to the thumbnail
	var $thumbFolder	=	'/thumbs/';	// Foldername to the thumbnail
	
	/**** Image Data ****/
	var $galleryid		=	0;			// Gallery ID
	var $pid			=	0;			// Image ID	
	var $filename		=	'';			// Image filename
	var $description	=	'';			// Image description
	var $alttext		=	'';			// Image alttext
	var $imagedate		=	'';			// Image date/time	

	/**** Gallery Data ****/
	var $name			=	'';			// Gallery name
	var $path			=	'';			// Gallery path	
	var $title			=	'';			// Gallery title
	var $previewpic		=	0;			// Gallery preview pic		

	/**
	 * Constructor
	 * 
	 * @param object $gallery The flagGallery object representing the gallery containing this image
	 * @return void
	 */
	function flagImage($gallery) {			
			
		//This must be an object
		$gallery = (object) $gallery;
		
		// Build up the object
		foreach ($gallery as $key => $value)
			$this->$key = $value ;
		
		// Finish initialisation
		$this->name			= $gallery->name;
		$this->path			= $gallery->path;
		$this->title		= $gallery->title;
		$this->previewpic	= $gallery->previewpic;
		$this->galleryid	= $gallery->galleryid;
		$this->alttext		= $gallery->alttext;
		$this->description	= $gallery->description;

		// set urls and paths
		$this->imageURL		= get_option ('siteurl') . '/' . $this->path . '/' . $this->filename;
		$this->thumbURL 	= get_option ('siteurl') . '/' . $this->path . '/thumbs/thumbs_' . $this->filename;
		$this->imagePath	= WINABSPATH.$this->path . '/' . $this->filename;
		$this->thumbPath	= WINABSPATH.$this->path . '/thumbs/thumbs_' . $this->filename;
		$this->meta_data	= unserialize($this->meta_data);
		
		wp_cache_add($this->pid, $this, 'flag_image');
		
	}
	
	function get_href_link() {
		// create the a href link from the picture
		$this->href  = "\n".'<a href="'.$this->imageURL.'" title="'.htmlspecialchars( stripslashes($this->description) ).'">'."\n\t";
		$this->href .= '<img alt="'.$this->alttext.'" src="'.$this->imageURL.'"/>'."\n".'</a>'."\n";

		return $this->href;
	}

	function get_href_thumb_link() {
		// create the a href link with the thumbanil
		$this->href  = "\n".'<a href="'.$this->imageURL.'" title="'.htmlspecialchars( stripslashes($this->description) ).'">'."\n\t";
		$this->href .= '<img alt="'.$this->alttext.'" src="'.$this->thumbURL.'"/>'."\n".'</a>'."\n";

		return $this->href;
	}
	
}
endif;
?>