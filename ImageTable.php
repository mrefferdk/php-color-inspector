<?php
namespace jakerb;

class ImageTable {

		/**
		 * @var string[]
		 * A list of allowed MIME Types.
		 */
		protected $allowedMimeTypes = [
				'image/png',
				'image/jpeg',
		];

		/**
		 * @var string
		 * The path to the image being processed.
		 */
		protected $imageSrc;

		/**
		 * @var string $imageMime
		 * The MIME type of image used to specify to GD.
		 */
		protected $imageMIME;

		/**
		 * ImageTable constructor.
		 *
		 * @param string $imageSrc Path to the image.
		 *
		 * @return \jakerb\ImageTable
		 * @throws \Exception
		 */
		public function __construct($imageSrc) {

				if (!extension_loaded('gd')) {
						throw new \Exception("ImageTable requires GD library to work.", 1);
				}

				if (!file_exists($imageSrc)) {
						throw new \Exception("The file you provided could not be found.", 1);
				}

				$this->imageMIME = mime_content_type($imageSrc);

				if (!in_array(strtolower($this->imageMIME), $this->allowedMimeTypes)) {
						throw new \Exception("The file you provided is of an unsupported or unrecognised type.", 1);
				}

				$this->imageSrc = $imageSrc;

				return $this;
		}

		public function showColors()
		{
				$image = null;
				$file = null;

				switch ($this->imageMIME) {
						case 'image/jpeg':
								$image = imagecreatefromjpeg($this->imageSrc);
								break;
						case 'image/png':
								$image = imagecreatefrompng($this->imageSrc);
								break;
				}

				$width = imagesx($image);
				$height = imagesy($image);

				$uses = [];

				echo "<div style='background-color: white'><img src='{$this->imageSrc}'/></div>";

				for ($y = 0; $y < $height; $y++) {

						for ($x = 0; $x < $width; $x++) {
								if ($rgb = imagecolorat($image, $x, $y)) {
										$r = ($rgb >> 16) & 0xFF;
										$g = ($rgb >> 8) & 0xFF;
										$b = $rgb & 0xFF;

										if (!isset($uses["$r,$g,$b"])) {
												$uses["$r,$g,$b"] = 1;
										}
										$uses["$r,$g,$b"]++;

								}
						}
				}

				arsort($uses);
				echo "<table><tr><th>Pixel count</th><th>RGB code</th></tr>";
				foreach ($uses as $rgb => $number) {
						echo "<tr><td>$number</td><td><span style='color:white; display: block; background-color: rgb($rgb)'>$rgb</span></td></tr>";
				}
				echo "</table>";

		}


}

$img = new ImageTable('diff.png');
echo $img->showColors();
