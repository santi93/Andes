<?php
$meta = new flagMeta($image->pid);
$dbdata = $meta->get_saved_meta();
$exifdata = $meta->get_EXIF();
$iptcdata = $meta->get_IPTC();
$xmpdata = $meta->get_XMP();
$alttext = trim ( $meta->get_META('title') );		
$description = trim ( $meta->get_META('caption') );	
$timestamp = $meta->get_date_time();

$makedescription = '<b>'.__('Meta Data','flag')."</b><br>";
if ($dbdata) { 
			foreach ($dbdata as $key => $value){
				if ( is_array($value) ) continue;
					$makedescription .= '<b>'.$meta->i8n_name($key)."</b> ".$value."<br>";
			}
} else {
			$makedescription .= __('No meta data saved','flag')."<br>";
}
if ($exifdata) { 
			$makedescription .= "\n<b>".__('EXIF Data','flag')."</b><br>"; 
			foreach ($exifdata as $key => $value){
				$makedescription .= '<b>'.$meta->i8n_name($key)."</b> ".$value."<br>";
			}
		}
if ($iptcdata) { 
			$makedescription .= "\n<b>".__('IPTC Data','flag')."</b><br>"; 
			foreach ($iptcdata as $key => $value){
				$makedescription .= '<b>'.$meta->i8n_name($key)."</b> ".$value."<br>";
			}
}
if ($xmpdata) {  
			$makedescription .= "\n<b>".__('XMP Data','flag')."</b><br>"; 
			foreach ($xmpdata as $key => $value){
				$makedescription .= '<b>'.$meta->i8n_name($key)."</b> ".$value."<br>";
			}
}
?>