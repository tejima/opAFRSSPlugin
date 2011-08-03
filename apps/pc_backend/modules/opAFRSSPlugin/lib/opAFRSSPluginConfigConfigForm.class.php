<?php
class opAFRSSPluginConfigForm extends sfForm
{
  protected $configs = array(

//s($app['all']['twipne_config']['accesskey'],$app['all']['twipne_config']['secretaccesskey']);
    'bitlylogin' => 'zuniv_us_bitlylogin',
    'bitlykey' => 'zuniv_us_bitlykey',
    'memberid' => 'zuniv_us_rss_memberid',
    'rss1' => 'zuniv_us_rss1',
    'rss2' => 'zuniv_us_rss2',
    'rss3' => 'zuniv_us_rss3',
    'rss4' => 'zuniv_us_rss4',
    'rss5' => 'zuniv_us_rss5',
    'rss6' => 'zuniv_us_rss6',
  );
  public function configure()
  {
    $this->setWidgets(array(
      'bitlylogin' => new sfWidgetFormInput(array('default'=>'tejicube')),
      'bitlykey' => new sfWidgetFormInput(array('default'=>'R_51143aaef55818ceecb48de4695c3e69')),
      'memberid' => new sfWidgetFormInput(),
      'rss1' => new sfWidgetFormInput(),
      'rss2' => new sfWidgetFormInput(),
      'rss3' => new sfWidgetFormInput(),
      'rss4' => new sfWidgetFormInput(),
      'rss5' => new sfWidgetFormInput(),
      'rss6' => new sfWidgetFormInput(),
    ));
    $this->setValidators(array(
      'bitlylogin' => new sfValidatorString(array(),array()),
      'bitlykey' => new sfValidatorString(array(),array()),
      'memberid' => new sfValidatorString(array(),array()),
      'rss1' => new sfValidatorString(array(),array()),
      'rss2' => new sfValidatorString(array('required' => false),array()),
      'rss3' => new sfValidatorString(array('required' => false),array()),
      'rss4' => new sfValidatorString(array('required' => false),array()),
      'rss5' => new sfValidatorString(array('required' => false),array()),
      'rss6' => new sfValidatorString(array('required' => false),array()),
    ));


    foreach($this->configs as $k => $v)
    {
      $config = Doctrine::getTable('SnsConfig')->retrieveByName($v);
      if($config)
      {
        $this->getWidgetSchema()->setDefault($k,$config->getValue());
      }
    }
    $this->getWidgetSchema()->setNameFormat('rss[%s]');
  }
  public function save()
  {
    foreach($this->getValues() as $k => $v)
    {
      if(!isset($this->configs[$k]))
      {
        continue;
      }
      $config = Doctrine::getTable('SnsConfig')->retrieveByName($this->configs[$k]);
      if(!$config)
      {
        $config = new SnsConfig();
        $config->setName($this->configs[$k]);
      }
      $config->setValue($v);
      $config->save();
    }
  }
  public function validate($validator,$value,$arguments = array())
  {
    return $value;
  }
}

