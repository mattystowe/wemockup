<?php
/**
 * Cyberduck CLI interface for console based ftp
 *
 * https://trac.cyberduck.io/wiki/help/en/howto/cli#Usage
 *
 */
namespace App;

use Exception;
use Log;
use App\MockupLogger;
use App\WeMockupFiles;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class CyberDuck
{

  public $host;
  public $port = 21; // default for ftp
  public $user;
  public $password;

  public $duckPath = 'duck';


  public function __construct()
  {
      $this->duckPath = env('PATH_TO_CYBERDUCK');
  }


  public function setHost($host, $port = 21) {
    $this->host = $host;
    $this->port = $port;
  }

  public function setLogin($user, $password) {
    $this->user = $user;
    $this->password = $password;
  }



  public function processUpload($source, $dest) {
    $cmd = $this->constructUploadCmd($source, $dest);
    if ($this->process($cmd)) {
      return true;
    } else {
      return false;
    }
  }

  public function processDownload($source, $dest) {
    $cmd = $this->constructDownloadCmd($source, $dest);
    if ($this->process($cmd)) {
      return true;
    } else {
      return false;
    }
  }




  public function process($cmd) {
    $process = new Process($cmd);
    $process->start();
    while ($process->isRunning()) {
      //Log::debug("Cyberduck:: Processing: " . $process->getOutput());
      sleep(1);
    }


    //Check the script has exited successfully
    if ($process->isSuccessful()) {
      Log::debug("Cyberduck:: Transfer Successful");
      return true;
    } else {
        Log::error("Cyberduck:: Error in file transfer");
        return false;
    }
  }






  public function constructUploadCmd($source,$dest) {
    $cmd = $this->duckPath . " --username " . $this->user . " --password " . $this->password . " --assumeyes --nokeychain  --existing=overwrite --upload " . $this->host . ":" . $this->port . $dest . " " . $source;
    Log::debug("Cyberduck:: Cmd: " . $cmd);
    return $cmd;
  }

  public function constructDownloadCmd($source, $dest) {
    $cmd = $this->duckPath . " --username " . $this->user . " --password " . $this->password . " --assumeyes --nokeychain  --existing=overwrite --download " . $this->host . ":" . $this->port . $source . " " . $dest;
    Log::debug("Cyberduck:: Cmd: " . $cmd);
    return $cmd;
  }




}
