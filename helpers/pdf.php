<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author Shawn
 */
class PDFHelper extends AppHelper {
    //put your code here
    
    public function __construct($app) {
        parent::__construct($app);

        // load class
        $this->app->loader->register('FPDF', 'classes:fpdf/fpdf.php');
        $this->app->loader->register('GridPDF', 'classes:fpdf/scripts/grid.php');
                
    }

    public function get($name) {
        $class = $name.'PDF';
        if (file_exists($this->app->path->path('classes:fpdf/scripts/'.basename($class,'PDF').'.php'))) {
            $this->app->loader->register($class, 'classes:fpdf/scripts/'.basename($class,'PDF').'.php');
        } else {
            $class = 'FDPF';
        }
        
        $object = new $class($this->app);
        
        return $object;
    }
    
    public function __get($name) {
        return $this->get($name);
    }
        
        

}
