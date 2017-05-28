<?php

namespace Maghead\Schema\Loader;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use ArrayObject;

class MatchSet extends ArrayObject {

    public $matchBy;

    function __construct(array $files, $matchBy = null)
    {
        parent::__construct($files);
        $this->matchBy = $matchBy;
    }
}

class FileSchemaLoader
{
    const FILE_SUFFIX = 'Schema.php';

    const MATCH_FILENAME = 1;

    const MATCH_CLASSDECL = 2;

    const CLASSDECL_PATTERN = '/Schema\s+extends\s+((?:Maghead\\\\Schema\\\\)?(?:Declare|Mixin|Template|\w+)Schema)/sm';

    protected $paths;

    protected $fileSuffixLen;

    protected $matchBy = self::MATCH_CLASSDECL;

    protected $directoryIteratorFlags = RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS;

    protected $collectedFiles = [];

    /**
     * Includes '/vendor/' to ignore schema defined in the vendor directory.
     */
    protected $ignorePatterns = ['Test\.php$', '/(?:\.git|\.svn|vendor)/'];

    private $compiledIgnorePattern;

    public function __construct(array $paths = [])
    {
        $this->paths = $paths;
        $this->fileSuffixLen = strlen(self::FILE_SUFFIX);
    }

    public function addIgnorePattern($pattern)
    {
        $this->ignorePatterns[] = $pattern;
    }

    public function addPath($p, $matchBy = null)
    {
        $this->paths[] = new MatchSet((array) $p, $matchBy);
    }

    public function matchBy($m)
    {
        $this->matchBy = $m;
    }

    public function requireAndCollect($path)
    {
        require_once $path;
        $this->collectedFiles[] = $path;
    }

    /**
     * If the file matches the conditions, then return true.
     */
    public function scan($filepath, $matchBy = self::MATCH_CLASSDECL)
    {
        if ($this->compiledIgnorePattern && preg_match($this->compiledIgnorePattern, $filepath)) {
            return false;
        }
        if (!preg_match('/\.php$/', $filepath)) {
            return false;
        }

        switch ($matchBy) {
            case self::MATCH_FILENAME:
                return substr($filepath, - $this->fileSuffixLen) === self::FILE_SUFFIX;
            case self::MATCH_CLASSDECL:
                $content = file_get_contents($filepath);
                return preg_match(self::CLASSDECL_PATTERN, $content, $matches);
        }

        return true;
    }

    public function scanPaths(array $paths, $matchBy)
    {
        foreach ($paths as $a) {
            if ($a instanceof MatchSet) {
                $this->scanPaths($a->getArrayCopy(), $a->matchBy ?: $matchBy);
            } else {
                $path = $a;

                if (is_file($path)) {

                    $this->requireAndCollect($path);

                } else if (is_dir($path)) {
                    $rii = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($path, $this->directoryIteratorFlags),
                        RecursiveIteratorIterator::SELF_FIRST
                    );

                    foreach ($rii as $fi) {
                        $filepath = $fi->getPathname();
                        if ($this->scan($filepath)) {
                            $this->requireAndCollect($filepath);
                        }
                    }
                }
            }
        }
    }

    public function load()
    {
        $this->compiledIgnorePattern = join('|', array_map(function($p) {
            $p = str_replace('#', '\\#', $p);
            return "(?:$p)";
        }, $this->ignorePatterns));
        $this->compiledIgnorePattern = "!{$this->compiledIgnorePattern}!";

        $this->scanPaths($this->paths, $this->matchBy);
        return $this->collectedFiles;
    }
}
