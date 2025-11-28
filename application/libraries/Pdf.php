<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Load Dompdf dari vendor (hasil composer install)
require_once FCPATH . 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf
{
  protected $ci;
  protected $dompdf;

  public function __construct()
  {
    $this->ci = &get_instance();

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('chroot', realpath(FCPATH));

    $this->dompdf = new Dompdf($options);
  }

  public function loadHtml($html)
  {
    $this->dompdf->loadHtml($html);
  }

  public function setPaper($size, $orientation = 'portrait')
  {
    $this->dompdf->setPaper($size, $orientation);
  }

  public function render()
  {
    $this->dompdf->render();
  }

  public function stream($filename, $options = [])
  {
    $this->dompdf->stream($filename, $options);
  }

  public function output()
  {
    return $this->dompdf->output();
  }
}
