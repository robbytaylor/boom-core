<?php

class Controller_Asset_PDF extends Controller_Asset
{
	public function action_view()
	{
		$this->_log_download();
		$this->_do_download();
	}

	public function action_thumb()
	{
		// The filename of the asset.
		$filename = $this->asset->getFilename();

		// Thumbnail dimensions.
		$width = $this->request->param('width');
		$height = $this->request->param('height');

		// The filename of the asset thumbnail.
		$thumb = $filename.".thumb";

		// Does the thumbnail exist?
		if ( ! file_exists($thumb))
		{
			// No, save the first page of the PDF to an image to create a thumbnail.
			$image = new Imagick($filename.'[0]');
			$image->setImageFormat('jpg');
			$image->writeImage($thumb);

			unset($image);
		}

		$image = Image::factory($thumb);

		if ($width || $height)
		{
			$image->resize($width, $height);
		}

		// Display the thumbnail
		$this->response
			->headers('Content-type', 'image/jpg')
			->body($image->render());
	}

}