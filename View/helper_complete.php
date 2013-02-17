<?php 
App::uses('AppHelper', 'Helper');
/**
 * this Helper
 *
 * @property Html $Html
 * @property Session $Session
 * @property Form $Form
 * @property Layout $Layout
 * @property Watchitoo $Watchitoo
 */
class this extends AppHelper
{
    var $Html;
    var $Session;
    var $Form;
    
    public function __contruct()
    {
        $View = new View();
        $this->Html = new HtmlHelper($View);
        $this->Session = new SessionHelper($View);        
        $this->Form = new FormHelper($View);        
        $this->Layout = new LayoutHelper($View);
        $this->Watchitoo = new WatchitooHelper($View);
    }
}

$this = new this();
?> 