<?php
class AFRSSTask extends sfBaseTask
{
  protected function configure()
  {
    set_time_limit(120);
    mb_language("Japanese");
    mb_internal_encoding("utf-8");
    $this->namespace        = 'zuniv.us';
    $this->name             = 'AFRSS';
    $this->aliases          = array('zuniv.us-afrss');
    $this->briefDescription = '';

  }
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    self::processRSS();
  }
  public static function processRSS(){
    echo "processRSS()\n";
    $rsscheckedlist = unserialize(Doctrine::getTable('SnsConfig')->get("zuniv_us_rsschecked"));
    $rss_id = Doctrine::getTable('SnsConfig')->get("zuniv_us_rss_memberid",null);
    $rss1 = Doctrine::getTable('SnsConfig')->get("zuniv_us_rss1",null);
    $rss2 = Doctrine::getTable('SnsConfig')->get("zuniv_us_rss2",null);
    $rss3 = Doctrine::getTable('SnsConfig')->get("zuniv_us_rss3",null);
    $rss4 = Doctrine::getTable('SnsConfig')->get("zuniv_us_rss4",null);
    $rss5 = Doctrine::getTable('SnsConfig')->get("zuniv_us_rss5",null);
    $rss6 = Doctrine::getTable('SnsConfig')->get("zuniv_us_rss6",null);
    
    $url = "http://pipes.yahoo.com/pipes/pipe.run?_id=db686a257649dd2b1cc738825de2bb5e&_render=rss" ;
    $url .= "&url1=".urlencode($rss1); 
    $url .= "&url2=".urlencode($rss2); 
    $url .= "&url3=".urlencode($rss3); 
    $url .= "&url4=".urlencode($rss4); 
    $url .= "&url5=".urlencode($rss5); 
    $url .= "&url6=".urlencode($rss6); 
        
    $channel = new Zend_Feed_Rss($url);
    $counter = 0;
    foreach ($channel as $item) {
      if(array_key_exists($item->link(),$rsscheckedlist)){
        echo "DUPULICATED. PASS.\n";
      }else{
        $counter++;
        if($counter >= 4){
          break;
        }
        echo $item->title() . "\n";

        $client = new Zend_Http_Client('http://api.bit.ly/v3/shorten');
        $client->setParameterGet(array(
          'login' => Doctrine::getTable('SnsConfig')->get('zuniv_us_bitlylogin'),
          'apiKey' => Doctrine::getTable('SnsConfig')->get('zuniv_us_bitlykey'),
          'longUrl' => $item->link(),
        ));
        $shorturl = $item->link();
        $response = $client->request();
        if ($response->isSuccessful()) {
          $results = Zend_Json::decode($response->getBody());
          if ($results['status_txt'] == 'OK' && isset($results['data']['url'])) {
            $shorturl = $results['data']['url'];
          } //else{ die("STATUS NG"); }
        } //else{ die("false == isSuccessful()"); }

        $act = new ActivityData();
        $act->setMemberId($rss_id);
        $act->setBody($item->title() . " : " . $shorturl );
        $act->setSource("RSS");
        $act->setSourceUri($item->link());
        $act->setIsMobile(0);
        $act->save();

        $rsscheckedlist[$item->link()] = strtotime($item->pubDate());
        Doctrine::getTable('SnsConfig')->set("zuniv_us_rsschecked", serialize($rsscheckedlist));

      }
    }
  }
}

