<?php

namespace DotHiv\TranslationsManagerBundle\Service;

class GitService {

    private $path;
    private $remote;

    public function __construct($path, $remote) {
        $this->path = $path;
        $this->remote = $remote;
    }

    public function update() {
        if (!$this->cloneRepository())
            $this->discard();
        $this->pull();
    }

    public function change($file, $content) {
        $fh = fopen($this->path . '/' . $file, 'w');
        fwrite($fh, $content);
        fclose($fh);
    }

    public function status() {
        return $this->exec('git status');
    }

    public function discard() {
        $this->exec('git reset HEAD --hard');
    }

    public function diff() {
        return $this->exec('git diff');
    }

    public function commit($msg, $name, $email) {
        $sanitizer = '/^[A-Za-zöäüÖÄÜß0-9\.,; @-]*$/';
        if (!preg_match($sanitizer, $msg))
            throw new \Exception("Commit message can only contain A-z, numbers and . ; , but was '$msg'");
        if (!preg_match($sanitizer, $name))
            throw new \Exception("Name can only contain A-z, numbers and . ; , but was '$name'");
        if (!preg_match($sanitizer, $email))
            throw new \Exception("Email can only contain A-z, numbers and . ; , but was '$email'");
        return $this->exec("git commit -a -m 'trans(): $msg' --author='$name <$email>'");
    }

    public function log() {
        return $this->exec('git log');
    }

    private function cloneRepository() {
        if (!file_exists($this->path . '/.git')) {
            mkdir($this->path, 0700, true);
            $this->exec('git clone --depth 1 \'' . $this->remote  . '\' .');
            return true;
        }
        return false;
    }

    private function pull() {
        $this->exec('git pull');
    }

    private function exec($cmd) {
        $proc=proc_open($cmd, // Befehl
                array( // Pipes für stdin, stdout, stderr
                        array("pipe","r"),
                        array("pipe","w"),
                        array("pipe","w")
                ),
                $pipes, // Pipes übergeben
                $this->path, // working directory
                array(
                        // env
                        'PATH' => getenv('PATH'),
                )
        );

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $ret = proc_close($proc);
        if ($ret) {
            throw new \Exception("git: " . $stderr);
        }
        return $stdout;
    }

}
