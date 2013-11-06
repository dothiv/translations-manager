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
