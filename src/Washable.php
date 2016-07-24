<?php

namespace LaravelLaundromat;

use Illuminate\Console\AppNamespaceDetectorTrait;

trait Washable
{
    use AppNamespaceDetectorTrait;

    /**
     * Clean the current object before sending to front end.
     *
     * @param string $cleanerName [Name of cleaner class or full cleaner namespace]
     *
     * @return CleanPost
     */
    public function clean($cleanerName = null)
    {
        if ($cleanerName) {
            return $this->callCleaner($cleanerName);
        } elseif (isset($this->defaultCleaner)) {
            return $this->callCleaner($this->defaultCleaner);
        }

        $className = $this->getStandardCleanerName();

        return $this->callCleaner($className);
    }

    /**
     * Create the cleaner and call the clean mehtod on it.
     *
     * @param string $cleanerName [Name of cleaner]
     *
     * @return Cleaner|HttpException
     */
    protected function callCleaner($cleanerName)
    {
        $cleaner = $this->resolveCleaner($cleanerName);
        
        if (class_exists($cleaner)) {
            $cleanerObject = new $cleaner();

            return $cleanerObject->clean($this);
        }

        abort(500, "Class {$cleaner} does not exist. ".
            'Create the class or use a full namespace.');
    }

    /**
     * Resolve valid cleaner name for given name.
     *
     * @param string $name [Cleaner name]
     *
     * @return string
     */
    protected function resolveCleaner($name)
    {
        if (class_exists($name)) {
            return $name;
        }

        return $this->getStandardCleanerNamespace($name);
    }

    /**
     * Get cleaner name from name of calling class.
     *
     * @return string
     */
    protected function getStandardCleanerName()
    {
        $reflection = new \ReflectionClass(get_class());

        return 'Clean'.$reflection->getShortName();
    }

    /**
     * Get full namespace for cleaner.
     *
     * @param string $className [Name of cleaner class]
     *
     * @return string
     */
    protected function getStandardCleanerNamespace($className)
    {
        $namespace = $this->getAppNamespace().'Cleaners\\';

        return $namespace.$className;
    }
}
