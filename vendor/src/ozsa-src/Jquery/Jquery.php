<?php


use Myfc\JqueryApp;
/**
 *
 * @author vahit�erif
 *        
 */
class Jquery
{
    
    public $start = "<script> \n  $(function(){ \n";
    
    public $end   = "}); \n</script>";
    
    public $content = "";
    
    public $app;
    
    const DATA = 'data';

    /**
     *  
     *    Ba�lat�c� fonksiyon
     * 
     */
    public function __construct($div = '')
    {
        
        $this->clean();
        $this->app = new JqueryApp($div);

       
        
    }
    
    
    /**
     * Static olarak s�n�f� ba�latmak i�in kullan�l�r
     * @param string $div
     * @return \Myfc\Jquery
     */
    
    public static function boot( $div = '' )
    {
        
        return new static($div);
        
    }
    
    public static function  addJquery($return = false)
    {
        
        $jquery = "<script src='https://code.jquery.com/jquery-2.1.3.min.js' type='text/javascript'></script>";
        
        if($return)
        {
            
            return $jquery;
            
        }else{
            
            echo $jquery;
            
        }
    }
    
    /**
     *  
     *   ��eri�i temizler
     *  
     */
    
    public function clean()
    {
        
        $this->content = "";
        
    }
    
   
    
    /**
     * Div de�i�tirme i�leminde kullan�l�r
     * @param string $div
     * @return \Myfc\Jquery
     */
    
    public function setDiv($div = '')
    {
        
        $this->app->setDiv($div);
        
        return $this;
        
    }
    
  
    public function execute($return = true)
    {
        $this->content = $this->start.$this->content.$this->end;
        
        if($return)
        {
            
            return $this->content;
            
        }else{
            
            echo $this->content;
            
        }
        
    }
    
    /**
     *  
     *   ->div �eklinde div atamas� yap�labilir
     * 
     *    @param string $name
     */
    
    public function __get($name = '')
    {
        
        $this->setDiv($name);
        
        return $this;
        
    }
    
    /**
     * S�n�fda olmayan fonksiyonlar� kullanmak i�in otomatik olarak JqueryApp a y�nlendirilmesi
     * @param unknown $name
     * @param unknown $params
     * @return \Myfc\Jquery
     */
    
    public function __call($name,$params)
    {
        
        $call = call_user_func_array(array($this->app,$name), $params);
        
        if(is_string($call))
        {
            
            $this->content .= $call;
            
        }
        
        if(is_object($call))
        {
            
            $this->content .= $call->getContent();
            
        }
        
        return $this;
        
    }
    
    
    
    
}

?>