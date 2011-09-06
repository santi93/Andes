package com.modularweb.imageGalleries {
	
	// Importing neccessary classes
	import flash.display.Loader;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.net.URLRequest;
	import flash.display.Bitmap;
	import flash.display.SimpleButton;
	import flash.geom.Matrix3D;
	import flash.geom.Point;
	import flash.net.URLLoader;
	import flash.text.StyleSheet;
	import flash.utils.Timer;
	import caurina.transitions.Tweener
	import caurina.transitions.properties.ColorShortcuts;
	
	public class PieceMaker extends MovieClip {
		
		private var t:MovieClip; // The reference to the gallery itself
		private var xml:XML; // The XML reference with the image paths, etc.
		private var xmlLoader:URLLoader; // The loader for the XML file
		private var css:StyleSheet; // The stylesheet reference for styling the text on back of the cards
		private var cssLoader:URLLoader; // The loader for the XML file
		private var loadingList:Array = new Array(); // The list of all images to be loaded
		private var loadCounter:int = 0; // The counter to monitor how many images have been loaded yet
		private var i:int; // Needed for the loops
		private var j:int; // Needed for the loops
		private var loader:Loader; // Loading the images
		private var turnStatus:int = 0; // Monitoring the status of the turning
		private var currentImage:int = -1; // Monitoring, which image is currently shown
		private var swapCount:int = 0; // Monitoring the progress when turning
		private var completeCount:int = 0; // Monitoring the progress when turning
		private var rotationDirection:int; // Turn forwards or backwards
		private var rotationStart:int = 0; // Monitoring the rotation process
		private var rotationTarget:int = 0; // Monitoring the rotation process
		private var images:Array = new Array(); // Array to store all the images
		private var segmentWidth:Number; // Width of one segment
		private var container:MovieClip; // Container holding the segments
		private var controls:MovieClip; // Container holding buttons (previous, next, autoplay, etc.)
		private var oneImage:MovieClip; // Container holding the single current image and the info box
		private var autoplaymc:MovieClip; // The MovieClip controlling the autoplay
		private var autoplayOn:Boolean = true; // Checking, if autoplay is currently on
		private var autoplayWasOn:Boolean = false; // Needed to check, if autoplay was on, before info was shown (info stops the autoplay)
		private var singleImage:MovieClip; // The single current image is shown, when there is no movement
		private var singleText:MovieClip; // The single info text is shown, when there is no movement
		private var allowControls:Boolean = false; // Controls are hidden, before the second image is loaded
		
		// Following variables are public, so that they can also be set externally.
		public var xmlSource:String; // Path to the XML file
		public var cssSource:String; // Path to the CSS file
		public var imageSource:String; // Path to the images
		public var imageWidth:int; // Width of the images
		public var imageHeight:int; // Height of the images
		public var segments:int; // Number of segments
		public var tween:Array = new Array(); // Array holding tweenTime, tweenDelay and transitionType. Tweens are done in Tweener: http://hosted.zeh.com.br/tweener/docs/en-us/
		public var innerColor:int; // Color of the sides of the elements
		public var textBackground:int; // Color of the description text background
		public var zDistance:int; // To which extend are the cubes moved on z axis, when tweened. Negative values bring the cube closer to the camera
		public var expand:int; // To which etxend are the cubes moved away from each other when tweening
		public var shadowDarkness:Number; // To which extend are the sides darkened, when tweening. 100 is black, 0 is no change
		public var textDistance:int; // Distance of the info text to the borders of its background
		public var autoplay:int; // Number of seconds to the next image, when autoplay is on
		
		public function PieceMaker () {
			// Waiting for all properties to be set
			t = this as MovieClip;
			t.addEventListener("properties", loadXML);
			// Initiating Tweeners ColorShortcuts
			ColorShortcuts.init();
		}
		
		private function loadXML (e:Event) {
			// Loading the XML file
			xmlLoader = new URLLoader();
			xmlLoader.addEventListener(Event.COMPLETE, loadCSS);
			xmlLoader.load(new URLRequest(xmlSource));
		}
		
		private function loadCSS (e:Event) {
			// loading the CSS file
			cssLoader = new URLLoader();
			cssLoader.addEventListener(Event.COMPLETE, initiate);
			cssLoader.load(new URLRequest(cssSource));
		}
		
		private function initiate (e:Event) {
			// Referencing the XML file and the CSS file
			xml = new XML(xmlLoader.data)
			css = new StyleSheet();
			css.parseCSS(cssLoader.data);
			
			// Assigning Variables from the XML
			imageWidth = int(xml.Settings.imageWidth);
			imageHeight = int(xml.Settings.imageHeight);
			segments = int(xml.Settings.segments);
			innerColor = int(xml.Settings.innerColor);
			textBackground = int(xml.Settings.textBackground);
			zDistance = int(xml.Settings.zDistance);
			expand = int(xml.Settings.expand);
			shadowDarkness = int(xml.Settings.shadowDarkness) / -100;
			autoplay = int(xml.Settings.autoplay);
			textDistance = int(xml.Settings.textDistance);
			
			tween.push(Number(xml.Settings.tweenTime));
			tween.push(Number(xml.Settings.tweenDelay));
			tween.push(String(xml.Settings.tweenType));
			
			dispatchEvent(new Event("xmlVariablesSet"));
			
			// Remove autoplay, when it's set to 0 in the XML;
			if (autoplay == 0) {
				autoplayOn = false;
			}
			
			// Defining segment width 
			segmentWidth = imageWidth / segments;
			
			// Creating the container for the cubes
			container = new MovieClip();
			container.z = imageHeight / 2;
			addChild(container);
			
			// Creating oneImage container for single image and info text
			oneImage = new MovieClip();
			oneImage.z = imageHeight / 2;
			oneImage.visible = false;
			addChild(oneImage);
			
			// Adding the text component to oneImage
			var tc:MovieClip = new MovieClip();
			tc.name = "textContainer";
			tc.x = imageWidth / 2;
			tc.y = imageHeight / -2;
			tc.z = imageHeight / -2;
			tc.graphics.beginFill(textBackground, 1);
			tc.graphics.drawRect(0, 0, imageHeight, imageHeight);
			tc.graphics.endFill();
			tc.rotationY = -90;
			oneImage.addChild(tc);
			var tmc:MovieClip = new textMC();
			tmc.name = "textMC";
			tmc.tf.x = textDistance;
			tmc.tf.y = textDistance;
			tmc.tf.width = imageHeight - textDistance * 2;
			tmc.tf.height = imageHeight - textDistance * 2;
			tmc.tf.wordWrap = true;
			tmc.tf.mouseWheelEnabled = false;
			tmc.tf.embedFonts = true;
			tmc.tf.styleSheet = css;
			tc.addChild(tmc);
			
			// Adding the closeButton to the text component
			var cb:SimpleButton = new closeButton();
			cb.x = imageHeight;
			cb.addEventListener (MouseEvent.MOUSE_UP, closeInfoClick);
			tc.addChild(cb);
			
			// Adding the imageContainer to the oneImage element
			var ic:MovieClip = new MovieClip();
			ic.name = "imageContainer";
			ic.x = imageWidth / -2;
			ic.y = imageHeight / -2;
			ic.z = imageHeight / -2;
			oneImage.addChild(ic);
			
			// Creating singleImage to be shown when there is no tweening
			singleImage = new MovieClip();
			singleImage.x = imageWidth / -2;
			singleImage.y = imageHeight / -2;
			var premask:MovieClip = new MovieClip();
			premask.graphics.beginFill(0xFFFFFF, 1);
			premask.graphics.drawRect(0, 0, imageWidth, imageHeight);
			premask.graphics.endFill();
			singleImage.addChild(premask);
			var pre:MovieClip = new preloader();
			pre.mask = premask;
			singleImage.addChild(pre);
			addChild(singleImage);
			
			// Creating singleText to be shown when there is no tweening
			singleText = new MovieClip();
			singleText.visible = false;
			singleText.x = imageHeight / -2;
			singleText.y = imageHeight / -2;
			singleText.graphics.beginFill(textBackground, 1);
			singleText.graphics.drawRect(0, 0, imageHeight, imageHeight);
			singleText.graphics.endFill();
			// Creating the textfield
			var tmc2:MovieClip = new textMC();
			tmc2.name = "textMC";
			tmc2.tf.x = textDistance;
			tmc2.tf.y = textDistance;
			tmc2.tf.width = imageHeight - textDistance * 2;
			tmc2.tf.height = imageHeight - textDistance * 2;
			tmc2.tf.wordWrap = true;
			tmc2.tf.mouseWheelEnabled = false;
			tmc2.tf.embedFonts = true;
			tmc2.tf.styleSheet = css;
			singleText.addChild(tmc2);
			// Creating the closeButton
			var cb2:SimpleButton = new closeButton();
			cb2.x = imageHeight;
			cb2.addEventListener (MouseEvent.MOUSE_UP, closeInfoClick);
			singleText.addChild(cb2);
			addChild(singleText);
			
			// Creating controls component to hold all the buttons
			controls = new MovieClip();
			addChild(controls);
			controls.visible = false;
			controls.alpha = 0;
			// Adding nextButton to controls
			var nb:SimpleButton = new nextButton();
			nb.name = "nextBtn";
			nb.x = imageWidth / 2;
			nb.addEventListener (MouseEvent.MOUSE_UP, clicked);
			controls.addChild(nb);
			// adding prevButton to controls
			var pb:SimpleButton = new nextButton();
			pb.name = "prevBtn";
			pb.scaleX = -1;
			pb.x = imageWidth / -2;
			pb.addEventListener (MouseEvent.MOUSE_UP, clicked);
			controls.addChild(pb);
			// Adding infoButton to controls
			var ib:SimpleButton = new infoButton();
			ib.name = "infoButton";
			ib.addEventListener (MouseEvent.MOUSE_UP, infoClick);
			controls.addChild(ib);
			
			// Creating the autoplay MovieClip, as long as autoplay is not 0
			if (autoplay != 0) {
				autoplaymc = new autoplayMC();
				autoplaymc.autoplayControl = 0;
				autoplaymc.buttonMode = true;
				autoplaymc.addEventListener(MouseEvent.MOUSE_UP, autoplayClick);
				controls.addChild(autoplaymc);
			}
			
			// Adding listeners to show or hide controls depending on MouseOver on the gallery
			addEventListener (MouseEvent.MOUSE_OVER, roll);
			addEventListener (MouseEvent.MOUSE_OUT, rollout);
			
			// Initializing the stage listener to center the 3D projection center
			stage.addEventListener (Event.RESIZE, stageResized);
			stageResized (new Event("initiate 3D"));
			
			// Creating a loadingList from XML
			for (i = 0; i < xml.Image.length(); i++) {
				loadingList.push(xml.Image[i].@Filename);
			}
			
			// Creating the cubic segments (cube) to be turned later on
			for (i = 0; i < segments; i++) {
				var cube:MovieClip = new MovieClip();
				cube.x = (segmentWidth) * i - imageWidth / 2 + segmentWidth / 2;
				cube.name = "cube" + i;
				
				// Creating the 4 sides of the cubes
				for (j = 0; j < 4; j++) {
					var c:MovieClip = new MovieClip();
					var side:MovieClip = new MovieClip();
					// Adding an imageContainer to each side, which will contain the images later on
					var imageContainer:MovieClip = new MovieClip();
					imageContainer.name = "imageContainer";
					imageContainer.x = segmentWidth / -2;
					imageContainer.z = imageHeight / -2;
					// Giving each side a black surface
					imageContainer.graphics.beginFill(0x000000, 1);
					imageContainer.graphics.drawRect(0, imageHeight / -2, segmentWidth, imageHeight);
					imageContainer.graphics.endFill();
					side.addChild(imageContainer);
					// Naming, rotating and adding the each side to the cube
					side.name = "side" + j;
					side.rotationX = 90 * j;
					cube.addChildAt(side, 0);
					// Adding the proloader image to the first side, which is shown until first image is loaded
					if (j == 0) {
						var pl:MovieClip = new preloader();
						pl.x = segmentWidth * -i;
						pl.y = imageHeight / -2;
						var msk:MovieClip = new MovieClip();
						msk.graphics.beginFill(0x000000, 1);
						msk.graphics.drawRect(0, imageHeight / -2, segmentWidth, imageHeight);
						msk.graphics.endFill();
						pl.mask = msk;
						imageContainer.addChild(pl);
						imageContainer.addChild(msk);
					}
				}
				
				// Creating the left and right side of the cube
				for (j = 0; j < 2; j++) {
					var inner:MovieClip = new MovieClip();
					inner.graphics.beginFill(innerColor, 1);
					inner.graphics.drawRect(imageHeight / -2, imageHeight / -2, imageHeight, imageHeight);
					inner.graphics.endFill();
					inner.rotationY = 90;
					inner.x = j * segmentWidth - segmentWidth / 2;
					cube.addChildAt(inner, 3);
				}
				
				// Making those inner sides, which are hidden by the cube itself, invisible
				if (i < segments / 2) {
					container.addChild(cube);
					cube.getChildAt(4).visible = false;
				}
				else {
					container.addChildAt(cube, 0);
					cube.getChildAt(3).visible = false;
				}
				if (Math.round(segments / 2) != segments / 2 && Math.floor(segments / 2) == i) {
					cube.getChildAt(3).visible = false;
				}
			
			}
			
			// Initiating image loading
			loadImage();
		}
		
		private function loadImage () {
			// Image is loaded, path consists of the ImageSource attribute from the XML and the entry from the loadingList, based on the loadCounter
			loader = new Loader();
			loader.contentLoaderInfo.addEventListener(Event.COMPLETE, loadComplete);
			loader.load(new URLRequest(imageSource + "/" + loadingList[loadCounter]));
		}
		
		private function loadComplete (lc:Event) {
			// Creating an array for the images, in which the image is stored many times
			var imageArray:Array = new Array()
			
			// Cloning the image for each segment 
			for (i = 0; i < segments; i++) {
				var image:Bitmap = new Bitmap(Bitmap(loader.content).bitmapData.clone());
				image.x = segmentWidth * -i;
				image.y = imageHeight / -2;
				image.smoothing = true;
				// Creating and assigning a mask to show only the appropriate part of the image
				var msk:MovieClip = new MovieClip();
				msk.graphics.beginFill(0x000000, 1);
				msk.graphics.drawRect(0, imageHeight / -2, segmentWidth, imageHeight);
				msk.graphics.endFill();
				// Adding mask and image to a MovieClip, masking and storing in the array
				var mc:MovieClip = new MovieClip();
				mc.addChild(image);
				mc.addChild(msk)
				image.mask = msk;
				imageArray.push(mc);
			}
			
			// Adding two more clones for oneImage and singleImage
			var oneI:Bitmap = new Bitmap(Bitmap(loader.content).bitmapData.clone());
			imageArray.push(oneI);
			var singleI:Bitmap = new Bitmap(Bitmap(loader.content).bitmapData.clone());
			imageArray.push(singleI);
			
			// Pushing the array of currently loaded image to a bigger array of all images
			images.push(imageArray);
			
			// When first image is loaded, this makes it show up and hide the preloader
			if (loadCounter == 0) {
				controls.getChildByName("nextBtn").dispatchEvent(new MouseEvent("mouseUp"));
			}
			
			// Showing controls, when more then the currently shown image is loaded
			if (loadCounter > currentImage && allowControls) {
				controls.visible = true;
			}
			
			// If there are more images to load, the new loading process is initiated
			loadCounter++;
			if (loadCounter < loadingList.length) {
				loadImage();
			}
		}
		
		private function clicked (e:MouseEvent) {
			// Making elements visible or invisible
			oneImage.visible = false;
			singleImage.visible = false;
			container.visible = true;
			controls.visible = false;
			
			// Setting back autoplay, as long as autoplay is active
			if (autoplay != 0) {
				Tweener.removeTweens (autoplaymc);
				autoplaymc.autoplayControl = 0;
			}
			
			// Setting properties, depending on if to go to next or previous image
			if (e.target.name == "nextBtn") {
				turnStatus--;
				if (turnStatus == -1) {
					turnStatus = 3;
				}
				rotationDirection = 90;
				currentImage++;
				if (currentImage >= images.length) {
					currentImage = 0;
				}
			}
			else {
				turnStatus++;
				if (turnStatus == 4) {
					turnStatus = 0;
				}
				rotationDirection = -90;
				currentImage--;
				if (currentImage < 0) {
					currentImage = images.length - 1;
				}
			}
			
			// Loading info text into the text containers
			var tc:MovieClip = MovieClip(oneImage.getChildByName("textContainer")).getChildByName("textMC") as MovieClip;
			tc.tf.text = xml.Image[currentImage].Text.children().toXMLString().split("\n").join("").replace(/Ӂ/g, " ");
			MovieClip(singleText.getChildByName("textMC")).tf.text = xml.Image[currentImage].Text.children().toXMLString().split("\n").join("").replace(/Ӂ/g, " ");
			
			// Making infoButton invisible, if there is no info text
			if (tc.tf.text != "") {
				controls.getChildByName("infoButton").visible = true;
			}
			else {
				controls.getChildByName("infoButton").visible = false;
			}
			
			// Aligning infoButton and autoplay MovieClip, depending on if both are shown
			if (autoplay != 0 && tc.tf.text != "") {
				autoplaymc.x = -30;
				controls.getChildByName("infoButton").x = 30;
			}
			else {
				autoplaymc.x = 0;
				controls.getChildByName("infoButton").x = 0;
			}
			
			// Calling nextImage function, which starts the tweens
			nextImage();
		}
		
		private function nextImage () {			
			// Setting rotationStart and rotationTarget, to monitor the tweening progress
			rotationStart = rotationTarget;
			rotationTarget += rotationDirection;
			
			// Setting tweens and swapping sides for each single cube
			for (i = 0; i < container.numChildren; i++) {
				// Referencing a cube
				var cube:MovieClip = container.getChildByName("cube" + i) as MovieClip;
				// Referencing the side to be shown next
				var side:MovieClip = cube.getChildByName("side" + turnStatus) as MovieClip;
				// Loading image parts from the array to these sides of the cubes
				MovieClip(side.getChildByName("imageContainer")).addChild(images[currentImage][i]);
				// Swapping the side to be shown next to index 2, which is behind the left and right side and the previously shown image side
				cube.swapChildren(side, cube.getChildAt(2));
				
				// Shading the image to be shown next and then assigning a tween to give it its original color back
				Tweener.addTween (side, {_tintBrightness:shadowDarkness, time:0});
				Tweener.addTween (side, {_tintBrightness:0, time:tween[0], delay:tween[1] * i, transition:tween[2]});
				// In turn setting a tween to shade the currently shown image while tweening
				Tweener.addTween (cube.getChildAt(5), {_tintBrightness:shadowDarkness, time:tween[0], delay:tween[1] * i, transition:tween[2]});
				// Initiating the tweens of the cube to be moved on the z axis and expand
				Tweener.addTween (cube, {z:zDistance, x:(segmentWidth + expand) * i - (imageWidth + segments * expand) / 2 + (segmentWidth + expand) / 2, time:tween[0] / 2, delay:tween[1] * i, transition:"easeInOutCubic"});
				Tweener.addTween (cube, {z:0, x:segmentWidth * i - imageWidth / 2 + segmentWidth / 2, time:tween[0] / 2, delay:tween[0] / 2 + i * tween[1], transition:"easeInOutCubic"});
				// Initiating the rotation tween
				Tweener.addTween (cube, {rotationX:container.getChildAt(i).rotationX + rotationDirection, time:tween[0], delay:tween[1] * i, transition:tween[2], onComplete:turnComplete});
				
				// Adding a listener to check the progress of each rotation
				cube.addEventListener(Event.ENTER_FRAME, rotationCheck);
			}
		}
		
		// Checking the degree, when it's turned more the 45 degrees, the sides of old and new image are swapped
		private function rotationCheck (e:Event) {
			if (rotationDirection > 0 && e.target.rotationX > rotationStart + (rotationTarget - rotationStart) / 2 || rotationDirection < 0 && e.target.rotationX < rotationStart + (rotationTarget - rotationStart) / 2) {
				e.target.removeEventListener(Event.ENTER_FRAME, rotationCheck);
				MovieClip(e.target).swapChildrenAt(2, 5);
			}
		}
		
		// When tweening is done, singleImage is shown, container is made invisible, controls are shown again, etc.
		private function turnComplete () {
			var ic:MovieClip = oneImage.getChildByName("imageContainer") as MovieClip;
			completeCount++;
			if (completeCount == segments) {
				allowControls = true;
				completeCount = 0;
				if (ic.numChildren > 0) {
					ic.removeChildAt(0);
				}
				ic.addChild(images[currentImage][segments]);
				singleImage.addChild(images[currentImage][segments + 1]);
				oneImage.visible = false;
				container.visible = false;
				singleImage.visible = true;
				if (images.length > 1) {
					controls.visible = true;
				}
				if (autoplay != 0 && autoplayOn) {
					autoplayFunction();
				}
			}
		}
		
		// Moving towards the description text
		private function infoClick (e:MouseEvent) {
			oneImage.visible = true;
			singleImage.visible = false;
			
			if (autoplayOn) {
				autoplayClick(new MouseEvent("click"));
				autoplayWasOn = true;
			}
			else {
				autoplayWasOn = false;
			}
			controls.visible = false;
			
			addEventListener(Event.ENTER_FRAME, infoRotation);
			
			Tweener.addTween (oneImage, {rotationY:90, z:imageWidth / 2, time:0.7, transition:"easeInOutCubic", onComplete:infoComplete});
			Tweener.addTween (oneImage.getChildAt(1), {_tintBrightness:shadowDarkness / 2, time:0.7, transition:"easeInOutCubic"});
			Tweener.addTween (oneImage.getChildAt(0), {_tintBrightness:0, time:0.7, transition:"easeInOutCubic"});
		}
		
		// Checking the rotation to the text to swap elements accordingly
		private function infoRotation (e:Event) {
			if (oneImage.rotationY > 45) {
				oneImage.swapChildrenAt(0, 1);
				removeEventListener(Event.ENTER_FRAME, infoRotation);
			}
		}
		
		// Assignning visibility, when tween is done
		private function infoComplete () {
			singleText.visible = true;
			oneImage.visible = false;
		}
		
		// Moving back to the image
		private function closeInfoClick (e:MouseEvent) {
			singleText.visible = false;
			oneImage.visible = true;
			
			if (autoplayWasOn) {
				autoplayClick(new MouseEvent("click"));
			}
			
			addEventListener(Event.ENTER_FRAME, infoBackRotation);
			
			Tweener.addTween (oneImage, {rotationY:0, z:imageHeight / 2, time:0.7, transition:"easeInOutCubic", onComplete:backFromInfo});
			Tweener.addTween (oneImage.getChildAt(1), {_tintBrightness:shadowDarkness / 2, time:0.7, transition:"easeInOutCubic"});
			Tweener.addTween (oneImage.getChildAt(0), {_tintBrightness:0, time:0.7, transition:"easeInOutCubic"});
		}
		
		// Checking the rotation back to the image to swap elements accordingly
		private function infoBackRotation (e:Event) {
			if (oneImage.rotationY < 45) {
				oneImage.swapChildrenAt(0, 1);
				removeEventListener(Event.ENTER_FRAME, infoBackRotation);
			}
		}
		
		// Assignning visibility, when tween is done
		private function backFromInfo () {
			controls.visible = true;
			singleImage.visible = true;
			oneImage.visible = false;
		}
		
		// Tweening the autoplay variable
		private function autoplayFunction () {
			Tweener.addTween (autoplaymc, {autoplayControl:1000, time:autoplay, transition:"linear", onUpdate:autoplayUpdate, onComplete:autoplayComplete});
		}
		
		// Graphically displaying the autoplay progress
		private function autoplayUpdate () {
			var msk:MovieClip = autoplaymc.aMask as MovieClip;
			var degree:Number = autoplaymc.autoplayControl * 0.36;
			msk.graphics.clear();
			msk.graphics.beginFill (0x00FF00, 1);
			msk.graphics.moveTo (0, 0);
			msk.graphics.lineTo(0, -100);
			if (degree > 0) {msk.graphics.lineTo(100, -100)};
			if (degree > 90) {msk.graphics.lineTo(100, 100)};
			if (degree > 180) {msk.graphics.lineTo(-100, 100)};
			if (degree > 270) {msk.graphics.lineTo(-100, -100)};
			msk.graphics.lineTo(Math.sin(degree*2*Math.PI/360)*100, -Math.cos(degree*2*Math.PI/360)*100);
			msk.graphics.lineTo(0, 0);
			msk.graphics.endFill();
		}
		
		// Initiating a movement and resetting autoplay, when autoplay time is over
		private function autoplayComplete () {
			var msk:MovieClip = autoplaymc.aMask as MovieClip;
			while (msk.numChildren > 0) {
				msk.removeChildAt(0);
			}
			controls.getChildByName("nextBtn").dispatchEvent(new MouseEvent("mouseUp"));
		}
		
		// Stopping or starting the autoplay, when the icon is clicked
		private function autoplayClick (e:MouseEvent) {
			if (autoplayOn) {
				autoplayOn = false;
				Tweener.removeTweens (autoplaymc);
				autoplaymc.autoplayControl = 0;
				MovieClip(autoplaymc.aMask).graphics.clear();
				autoplaymc.playIcon.visible = true;
				autoplaymc.stopIcon.visible = false;
			}
			else {
				autoplayOn = true;
				autoplayFunction();
				autoplaymc.playIcon.visible = false;
				autoplaymc.stopIcon.visible = true;
			}
		}
		
		// This function centers the 3D projection center, when the stage is resized
		public function stageResized (e:Event) {
			if (root is MovieClip) {
				var r:MovieClip = root as MovieClip;
				var point:Point = new Point(t.x, t.y);
				r.transform.perspectiveProjection.projectionCenter = parent.localToGlobal(point);
			}
		}
		
		// Making controls visible or invisible, depending on if the mouse is over the gallery
		private function roll (e:MouseEvent) {
			Tweener.addTween (controls, {alpha:1, time:0.8});
		}
		private function rollout (e:MouseEvent) {
			Tweener.addTween (controls, {alpha:0, time:0.8});
		}
		
	}
	
}